<?php

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath. DIRECTORY_SEPARATOR . 'include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("corpus-directory", "d", "path", "path to a json file with annotations"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));

$opt->addParameter(new ClioptParameter("corpus-name", null, null, "corpus name"));
$opt->addParameter(new ClioptParameter("corpus-description", null, null, "corpus description"));
$opt->addParameter(new ClioptParameter("user-id", null, null, "user id"));
$opt->addParameter(new ClioptParameter("annotation-sets", null, null, "annotation sets ids, string, comma sepparated eg. \"1,2,3\""));

/******************** parse cli *********************************************/

ini_set('memory_limit', '1024M');

try {
    $opt->parseCli($argv);
    $dsn = CliOptCommon::parseDbParameters($opt, Config::Config()->get_dsn());
    $verbose = $opt->exists("verbose");
    $corpusDir = $opt->getRequired("corpus-directory");
    $corpusName = $opt->getRequired("corpus-name");
    $corpusDesc = $opt->getRequired("corpus-description");
    $userId = intval($opt->getRequired("user-id"));
    $annotationSetsIds = array_map('intval', explode(',', $opt->getRequired("annotation-sets")));

    $cliImporter = new CliImporter($dsn, $verbose);
    $cliImporter->import_dir($corpusDir,$corpusName, $corpusDesc, $annotationSetsIds, $userId);
    unset($cliImporter);

} catch(Exception $ex){
    print "!! ". $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    print("\n");
    return;
}

class CliImporter{

    function __construct($dsn, $verbose){
        $this->db = new Database($dsn, false);
        $GLOBALS['db'] = $this->db; // necessary for other functions
        $this->verbose = $verbose;
        $this->info("new import, verbose mode: on");
        $this->MAXIMUM_FILE_SIZE = 2500000; //in bytes
    }

    /**
     * Print message if verbose mode is on.
     */
    function info($message){
        if ($this->verbose)
            echo $message . "\n";
    }

    /**
     * Assign an annotation set identified by a name to a corpus identified by id.
     * @param $annotation_set_id Id of an annotation set
     * @param $corpus_id Id of a corpus
     */
    function assignAnnotationSetToCorpus($annotation_set_id, $corpus_id){
        if ( $annotation_set_id !== null ){
            $cols = array("annotation_set_id"=>$annotation_set_id, "corpus_id"=>$corpus_id);
            $this->db->insert("annotation_sets_corpora", $cols);
        }
    }

    /**
     * assign a report perspective to given corpus.
     */
    function assignreportperspectivetocorpus($perspective_id, $corpus_id){
        $cols = array();
        $cols['corpus_id'] = $corpus_id;
        $cols['perspective_id'] = $perspective_id;
        $cols['access'] = 'loggedin';
        $this->db->insert("corpus_and_report_perspectives", $cols);
    }

    function create_corpus($name, $description, $user_id, $annotation_set_ids){
        $corpus = new CCorpus();
        $corpus->name = $name;
        $corpus->description = $description;
        $corpus->public = false;
        $corpus->user_id = $user_id;
        $corpus->save();

        foreach ($annotation_set_ids as $annotation_set_id) {
            $this->assignAnnotationSetToCorpus($annotation_set_id, $corpus->id);
        }

        $this->assignReportPerspectiveToCorpus("preview", $corpus->id);
        $this->assignReportPerspectiveToCorpus("annotator", $corpus->id);
        $this->assignReportPerspectiveToCorpus("autoextension", $corpus->id);
        $this->assignReportPerspectiveToCorpus("metadata", $corpus->id);

        return $corpus;
    }

    function import_dir($corpus_dir, $corpus_name, $corpus_description, $annotation_sets, $user_id) {
        //count files in dir
        $new_corpus_directory = new RecursiveDirectoryIterator($corpus_dir);
        $new_corpus_iterator = new RecursiveIteratorIterator($new_corpus_directory);

        //files must have *.ccl extension
        $new_corpus_regex = new RegexIterator(
            $new_corpus_iterator,
            '/^.+\.xml$/i',
            RecursiveRegexIterator::GET_MATCH);
        $ccl_array = array();
        foreach ($new_corpus_regex as $ccl_path => $object)
            array_push($ccl_array, $ccl_path);

        $this->info("number of XML files: " . count($ccl_array));
        if (count($ccl_array) == 0){
            throw new Exception("Archive does not contain *.xml files: {$corpus_dir}");
        }

        $this->info("importing files");

        $corpus = $this->create_corpus($corpus_name, $corpus_description, $user_id, $annotation_sets);

        /* Pobierz aktualną listę podkorpusów */
        $subcorpora = array();
        foreach ( DbCorpus::getCorpusSubcorpora(intval($corpus->id)) as $row ){
            $subcorpora[strtolower($row['name'])] = $row['subcorpus_id'];
        }

        $i = 0;
        foreach($ccl_array as $ccl_path) {
            //upload file -> new report -> get_id
            $this->info("processing: {$ccl_path}");
            $title = basename($ccl_path);
            $subcorpus_id = null;

            /* Sprawdź, czy nazwa pliku zawiera nazwę podkorpusu */
            $parts = explode("-", $title);
            if (count($parts) > 1) {
                $subcorpus = $parts[0];
                $title = $parts[1];

                if (!isset($subcorpora[strtolower($subcorpus)])) {
                    $subcorpus_id = DbCorpus::createSubcopus($corpus->id, $subcorpus, "");
                    $subcorpora[strtolower($subcorpus)] = $subcorpus_id;
                } else {
                    $subcorpus_id = $subcorpora[strtolower($subcorpus)];
                }
            }

            $i += 1;
            if (filesize($ccl_path) > $this->MAXIMUM_FILE_SIZE){
                throw new Exception("source file is too large (over {$this->MAXIMUM_FILE_SIZE} bytes");
            }

            $filename = pathinfo($title, PATHINFO_FILENAME);

            $r = new TableReport();
            $r->corpora = intval($corpus->id);
            $r->user_id = intval($user_id); //ner
            $r->format_id = 1; //1-xml, 2-plain, 3-premorph
            $r->type = 1; //nieokreślony
            $r->title = $title;
            $r->status = 2; //Accepted
            $r->date = date("Y-m-d H:i:s"); //"CURRENT_TIMESTAMP()";
            $r->source = "dspace";
            $r->author = "dspace";
            $r->content = "";
            $r->filename = $filename;

            if ( $subcorpus_id != null ) $r->subcorpus_id = $subcorpus_id;
            $import = new WCclImport();
            $import_result = $import->importCcl($r, $ccl_path, 'final');

            DbReport::insertEmptyReportExt($r->id);
        }
    }
}
