<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$engine = realpath(dirname(__FILE__) . "/../engine/");
include($engine . "/config.php");
include($engine . "/config.local.php");
include($engine . "/include.php");
include($engine . "/cliopt.php");
include($engine . "/clioptcommon.php");

mb_internal_encoding("utf-32");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to a folder with documents"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "subcorpus ID"));
$opt->addParameter(new ClioptParameter("user", "u", "id", "user ID"));

/******************** parse cli *********************************************/
try{
    /** Parse cli parameters */
	$opt->parseCli($argv);
    $dsn = CliOptCommon::parseDbParameters($opt, "localhost", "root", null, "gpw", "3306");
	$sourceFolder = $opt->getRequired("folder");
	$targetSubcorpusId = $opt->getRequired("subcorpus");
	$ownerUserId = $opt->getRequired("user");

    /** Setup database  */
    $GLOBALS['db'] = new Database($config->dsn,false);

    /** Validate parameters  */
	CliOptCommon::validateUserId($ownerUserId);
	CliOptCommon::validateSubcorpusId($targetSubcorpusId);
	CliOptCommon::validateFolderExists($sourceFolder);

    /** Process the request  */
    $corpus = DbSuborpus::get($targetSubcorpusId);
    $corpusId = intval($corpus['corpus_id']);
    $importer = new CorpusDocumentImporter($corpusId);

    $files = DocumentReaderTxt::getFolderFiles($sourceFolder);
	$pairs = DocumentReaderTxt::pairTxtAndIniFiles($files);

	foreach ($pairs as $item){
		if ( isset($item["ini"]) ) {
		    echo "Processing {$item["txt"]}\n";
            $ini = DocumentReaderTxt::loadMetadataFromIniFile($item["ini"]);
            $content = file_get_contents($item["txt"]);
            $ini["metadata"]["subcorpus_id"] = $targetSubcorpusId;
            $ini["metadata"]["format_id"] = 2; // plain
            $ini["metadata"]["user_id"] = $ownerUserId;
            $ini["metadata"]["status"] = 2;
            $ini["metadata"]["lang"] = "pl"; // TODO make as script parameter
            $importer->insert($content, $ini["metadata"], $ini["custom"]);
        }
	}
    $importer->printLogs();

}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/**
 * Class DocumentImporterTxt
 * Load documents from txt and ini files located in given folder.
 */
class DocumentReaderTxt{

    /**
     * @param $files
     * @return array return a list of assoc arrays with values array("txt"=>path, "ini"=>path"). Ini path may be null.
     */
	static function pairTxtAndIniFiles($files){
		$fileIndex = array();
		foreach ($files as $file){
			$fileIndex[$file] = 1;
		}
		$pairs = array();
        foreach ($files as $file) {
            if (strtolower(substr($file, strlen($file) - 4, 4)) == ".txt") {
                $iniFile = substr($file, 0, strlen($file) - 4) . ".ini";
				$pairs[] = array("txt"=>$file, "ini"=>isset($fileIndex[$iniFile])?$iniFile:null);
            }
        }
        return $pairs;
	}

    static function getFolderFiles($dir, &$results = array()){
        $files = scandir($dir);
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                DocumentReaderTxt::getFolderFiles($path, $results);
                $results[] = $path;
            }
        }
        return $results;
    }

	static function loadMetadataFromIniFile($pathIni){
        $metadata = array();
        $metadata["date"] = null;
        $metadata["title"] = null;
        $metadata["source"] = null;
        $metadata["author"] = null;
        $metadata["format_id"] = null;
        $metadata["lang"] = null;
        $metadata["filename"] = DocumentReaderTxt::getBasenameWithoutExtension($pathIni);
        $custom = array();
        if ( $pathIni != null ) {
            $ini = parse_ini_file($pathIni, true, INI_SCANNER_RAW);
            if (isset($ini["metadata"])) {
                foreach ($metadata as $key => $val) {
                    if ($key == "date" && strtotime($ini["metadata"][$key])) {
                        $metadata["date"] = date("Y-m-d", strtotime($ini["metadata"][$key]));
                    }
                    if (isset($ini["metadata"][$key])) {
                        $metadata[$key] = isset($ini["metadata"][$key]) ? $ini["metadata"][$key] : null;
                    }
                }
            }
            if (isset($ini["custom"])) {
                foreach ($ini["custom"] as $key => $val) {
                    $custom[$key] = $val;
                }
            }
        }
		return array("metadata"=>$metadata, "custom"=>$custom);
	}

	static function getBasenameWithoutExtension($path){
        $file_extension = pathinfo($path, PATHINFO_EXTENSION);
        return basename($path, ".".$file_extension);
	}

}

class CorpusDocumentImporter extends GroupedLogger{

    var $extFields = null;
    var $extTable = null;
    var $corpusId = null;

    function __construct($corpusId){
        $this->corpusId = $corpusId;
        $corpus = DbCorpus::getCorpusById($corpusId);
        if ( $corpus['ext'] ) {
            $this->extFields = DbCorpus::getCorpusExtColumnsWithMetadata($corpus['ext']);
            $this->extTable = $corpus['ext'];
        }
    }

    function insert($content, $metadata, $customMetadata){
        global $db;
        $r = new CReport();
        foreach ($r->getFields() as $field){
            if ( isset($metadata[$field]) ){
                $r->$field = $metadata[$field];
            }
        }
        $r->content = $content;
        $r->corpora = $this->corpusId;
        $r->save();

        if ( $this->extFields && is_array($customMetadata) ){
            $row = array();
            $row['id'] = $r->id;
            foreach ($this->extFields as $f){
                if ( isset($customMetadata[$f['field']]) ){
                    $row[$f['field']] = $customMetadata[$f['field']];
                }
            }
            foreach ($customMetadata as $k=>$v){
                if ( !isset($row[$k]) ){
                    $this->warn("Custom metadata $k could not be mapped", "Document title={$metadata['title']}; field value={$v}");
                }
            }
            print_r($row);
            $db->replace($this->extTable, $row);
        }
    }

}
