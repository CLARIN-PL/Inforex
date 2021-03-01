<?php

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath. DIRECTORY_SEPARATOR . 'include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" ). DIRECTORY_SEPARATOR ."config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("json", "j", "path", "path to a json file with annotations"));
$opt->addParameter(new ClioptParameter("annotation-set", "a", "id", "annotation set ID"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));

/******************** parse cli *********************************************/

try{
    $opt->parseCli($argv);
    $dsn = CliOptCommon::parseDbParameters($opt, Config::Config()->get_dsn());
    $verbose = $opt->exists("verbose");
    $jsonPath = $opt->getRequired("json");
    $annotationSetId = $opt->getRequired("annotation-set");

    $SherlockImport = new SherlockImport($dsn, $verbose);
    $SherlockImport->process($jsonPath, $annotationSetId);

}catch(Exception $ex){
    print "!! ". $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    print("\n");
    return;
}

class SherlockImport{
    var $db;
    var $additional_senses = array(
        "Inne znaczenie" => "Token rzeczownikowy, czasownikowy, przymiotnikowy lub przysłówkowy, którego znaczenie w danym kontekście nie zostało opisane w Słowosieci",
        "Inna klasa" => "Token, który według wytycznych do konstruowania Słowosieci w danym kontekście powinien zostać zaliczony do innej klasy gramatycznej nieuwzględnionej w budowaniu słownika (np. do klasy wykrzykników)",
        "Nazwa własna" => "Każdy element nazwy własnej, w przypadku wprowadzenia znacznika [Nazwa własna] nie stosujemy innych np. [Wyraz obcy]",
        "Element frazeologizmu" => "Token, który jest składnikiem jednostki wielowyrazowej, ale nie jest jej głową",
        "Wyraz obcy" => "Wyraz spoza słownika/systemu języka polskiego",
        "Błąd tagera" => "Błąd popełniony przez narzędzie do automatycznej segmentacji i tagowania wpływający na błędne przypisanie jednostki ze Słowosieci, np niepodzielenie słowa “miałem” na dwa tokeny, co wymusiło interpretację rzeczownikową (narzędnik od ‘miał’), a wykluczyło interpretację czasownikową (1 osoba liczby pojedynczej rodzaju męskiego czasu przeszłego od ‘mieć’)",
        "Uszkodzenie tekstu" =>  "Token będący wynikiem uszkodzenia tekstu, np. literówki"
    );

    function __construct($dsn, $verbose){
        $this->db = new Database($dsn, false);
        $this->verbose = $verbose;
    }

    function process($json_path, $annotation_set_id){
        if(!$this->annotationSetExists($annotation_set_id)){
            echo "Annotation subset " . $annotation_set_id . " does not exist.\n";
            return false;
        }

        $subsets = $this->getSubsets($annotation_set_id);
        $json_file = file_get_contents($json_path);
        $sherlock_json = json_decode($json_file, true);
        $new_annotations = $sherlock_json['annotations'];

        $total_annotations = count($new_annotations);
        $current_annotation = 1;
        $progress = 0;
        foreach($new_annotations as $annotation){

            $annotation_name = $annotation['channel'];

            $pieces = explode('_', $annotation_name);
            $subset_name = end($pieces);
            $annotation_subset_id = $subsets[$subset_name];

            if($annotation_subset_id == null){
                echo $subset_name . " => " . $annotation_subset_id . " : " . $annotation_name;
                echo "\n";
            }

            $annotation_type_id = $this->insertAnnotationType($annotation_name, $annotation_set_id, $annotation_subset_id);

            // showing progress
            $current_annotation++;
            $percent_done = floor(100 * $current_annotation / $total_annotations);
            if($percent_done > $progress){
                $progress = $percent_done;
                echo intval($progress) . "%" . "\n";
            }
            // showing progress end

            if (is_null($annotation_type_id)){ // not procedding if annotation already exists
                continue;
            }
            $annotation_attribute_id = $this->insertAnnotationAttribute($annotation_type_id);

            $annotation_senses = array();
            foreach($annotation['senses'] as $sense){
                $value = $sense['id'];
                $description = $sense['description'] . "{wn:".$sense['wn']."}";
                $annotation_senses[] = array($annotation_attribute_id, $value, $description);
            }

            foreach($this->additional_senses as $sense => $description){
                $annotation_senses[] = array($annotation_attribute_id, "[AUX] " . $sense, $description);
            }

            $this->insertAnnotationAttributeValue($annotation_senses);
        }
    }

    //Check if necessary subsets exist. If not, create them.
    function getSubsets($annotation_set_id){
        $subset_names = array('n', 'v', 'adj', 'adv');
        $subset_names_and_id = array();

        foreach($subset_names as $subset_name){
            $params = array($annotation_set_id, $subset_name);

            $sql = "SELECT annotation_subset_id FROM annotation_subsets WHERE annotation_set_id = ? AND name = ?";
            $subset_id = $this->db->fetch_one($sql, $params);

            if($subset_id  == null){
                $sql = 'INSERT INTO annotation_subsets (annotation_set_id, name) VALUES (?, ?)';
                $this->db->execute($sql, $params);
                $subset_id = $this->db->last_id();
            }

            $subset_names_and_id[$subset_name] = $subset_id;
        }
        return $subset_names_and_id;
    }

    function insertAnnotationType($annotation_name, $annotation_set_id, $annotation_subset_id){
        $description = " ";
        if ($this->annotationTypeExists($annotation_name, $description, $annotation_subset_id, $annotation_set_id)){
            return null;
        }
        $sql = 'INSERT INTO annotation_types (name, description, annotation_subset_id, group_id) 
                VALUES (?, ?, ?, ? )';
        $params = array($annotation_name, $description, $annotation_subset_id, $annotation_set_id);
        $this->db->execute($sql, $params);

        $annotation_id = $this->db->last_id();

        return $annotation_id;
    }

    function insertAnnotationAttribute($annotation_type_id){
        $sql = 'INSERT INTO annotation_types_attributes (annotation_type_id, name, type)
                VALUES (?, ?, ? )';
        $params = array($annotation_type_id, 'sense', 'radio');
        $this->db->execute($sql, $params);
        $annotation_attribute = $this->db->last_id();
        return $annotation_attribute;
    }

    function insertAnnotationAttributeValue($shared_attributes){
        $attributes = array();
        $params = array();

        foreach($shared_attributes as $attribute){
            $attributes[] = "(".implode(", ", array_fill(0, count($attribute), "?")).")";
            $params = array_merge($params, $attribute);
        }
        $values = implode(", ", $attributes);
        $sql = "INSERT INTO annotation_types_attributes_enum (annotation_type_attribute_id, value, description) 
                VALUES ".$values;
        $this->db->execute($sql, $params);
    }

    function annotationTypeExists($annotation_name, $description, $annotation_subset_id, $annotation_set_id){
        $sql = 'SELECT annotation_type_id FROM annotation_types 
                WHERE name=? and description= ? and annotation_subset_id= ? and  group_id= ?';

        $params = array($annotation_name, $description, $annotation_subset_id, $annotation_set_id);
        $id = $this->db->fetch_one($sql, $params);
        return !is_null($id);
    }

    function annotationSetExists($set_id){
        $sql = "SELECT annotation_set_id FROM annotation_sets 
                WHERE annotation_set_id = ?";
        $annotation_set_id = $this->db->fetch_one($sql, array($set_id));

        if($annotation_set_id == null){
            return false;
        } else{
            return true;
        }
    }
}
