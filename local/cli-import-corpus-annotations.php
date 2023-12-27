<?php
$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath . DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath . DIRECTORY_SEPARATOR . 'include.php');
Config::Cfg()->put_path_engine($enginePath);
Config::Cfg()->put_localConfigFilename(realpath($enginePath . "/../config/") . DIRECTORY_SEPARATOR . "config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("corpus-directory", "d", "path", "path to directory containing corpus files"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));

$opt->addParameter(new ClioptParameter("corpus-id", "i", null, "corpus id"));
$opt->addParameter(new ClioptParameter("user-ids", "uid", null, "user ids, string, comma separated, eg. \"1,2,3\""));
$opt->addParameter(new ClioptParameter("annotation-set", "aid", null, "annotation set id"));
$opt->addParameter(new ClioptParameter("stage", null, null, "inserted annotations stage"));
$opt->addParameter(new ClioptParameter("source", null, null, "inserted annotations source"));
$opt->addParameter(new ClioptParameter("ignore-duplicates", null, null, "flag, ignore duplicates?"));
$opt->addParameter(new ClioptParameter("ignore-unknown-types", null, null, "flag, ignore unknown types?"));

/******************** parse cli *********************************************/

ini_set('memory_limit', '1024M');

/*
php import-corpus-annotations-cli.php -U inforex:password@db:3306/inforex -d /home/inforex/secured_data/inforex_preannotation/out -v -i 98 -uid 101 -aid 65 --stage new --source user
*/
function assertContains($needle, $stack)
{
    if (!in_array($needle, $stack)) {
        throw new Exception('Forbidden value: ' . $needle . ', possible values are: ' . implode(', ', $stack));
    }
}

try {
    $opt->parseCli($argv);

    Config::Cfg()->put_dsn(array(
        'phptype' => 'mysql',
        'username' => 'inforex',
        'password' => 'password',
        'hostspec' => 'db' . ":" . '3306',
        'database' => 'inforex'
    ));

    $corpusDir = $opt->getRequired("corpus-directory");
    $verbose = $opt->exists("verbose");
    $corpusId = $opt->getRequired("corpus-id");
    $userIds = array_map('intval', explode(',', $opt->getRequired("user-ids")));
    $annotationSetId = $opt->getRequired("annotation-set");
    $annotationStage = $opt->getOptional("stage", "agreement");
    assertContains($annotationStage, ['new', 'final', 'agreement']);
    $annotationSource = $opt->getOptional("source", "auto");
    assertContains($annotationSource, ['user', 'bootstrapping', 'auto']);

    $ignore_duplicates = $opt->exists("ignore-duplicates");
    $ignore_unknown_types = $opt->exists("ignore-unknown-types");

    $cliImporter = new CliAnnotationImporter(Config::Cfg()->get_dsn(), $verbose);
    $cliImporter->import_annotations_dir($corpusDir,
        $corpusId,
        $userIds,
        $annotationSetId,
        $annotationStage,
        $annotationSource,
        $ignore_duplicates,
        $ignore_unknown_types
    );
    unset($cliImporter);

} catch (Exception $ex) {
    print "!! " . $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    die("\n");
}

class CliAnnotationImporter
{

    function __construct($dsn, $verbose)
    {
        $this->db = new Database($dsn, false);
        $GLOBALS['db'] = $this->db; // necessary for other functions
        $this->verbose = $verbose;
        $this->info("new import, verbose mode: on");
        $this->MAXIMUM_FILE_SIZE = 2500000; //in bytes
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    function disconnect()
    {
        $this->db->disconnect();
    }

    /**
     * Print message if verbose mode is on.
     */
    function info($message)
    {
        if ($this->verbose)
            echo $message . "\n";
    }


    /** Returns id for report if found else null
     * @param string $ccl_path
     * @param int $corpusId
     * @return int|null
     */
    function getReportId($ccl_path, $corpusId)
    {
        $path_parts = pathinfo($ccl_path);
        $report_id_from_filename = (int)str_replace('.xml', '', $path_parts['basename']);
        $report = DbReport::getReportById($report_id_from_filename);
        if (count($report) > 0) {
            return (int)$report['id'];
        }

        $report = DbReport::getByFilenameAndCorpusId($path_parts['basename'], $corpusId);
        if (count($report) > 0) {
            return (int)$report[0]['id'];
        }
        $report = DbReport::getByFilenameAndCorpusId($path_parts['filename'], $corpusId);
        if (count($report) > 0) {
            return (int)$report[0]['id'];
        }

        if (strpos($path_parts['filename'], '-') !== false) {
            $filename_subcorp = explode('-', $path_parts['filename'])[1];
            $report = DbReport::getByFilenameAndCorpusId($filename_subcorp, $corpusId);
            if (count($report) > 0) {
                return (int)$report[0]['id'];
            }
        }

        if (strpos($path_parts['basename'], '-') !== false) {
            $basename_subcorp = explode('-', $path_parts['basename'])[1];
            $report = DbReport::getByFilenameAndCorpusId($basename_subcorp, $corpusId);
            if (count($report) > 0) {
                return (int)$report[0]['id'];
            }

        }

        return null;
    }

    /** Assign ccl annotations to existing corpus using ccl files
     * @param string $corpusDir
     * @param int $corpusId
     * @param array $userIds
     * @param int $annotationSetId
     * @param string $annotationStage
     * @param string $annotationSource
     * @param bool $ignore_duplicates
     * @param bool $ignore_unknown_types
     * @throws Exception
     */
    function import_annotations_dir($corpusDir, $corpusId, $userIds, $annotationSetId, $annotationStage,
                                    $annotationSource, $ignore_duplicates, $ignore_unknown_types)
    {
        //count files in dir
        $corpus_directory = new RecursiveDirectoryIterator($corpusDir);
        $corpus_iterator = new RecursiveIteratorIterator($corpus_directory);

        //files must have .xml or .ccl extension
        $corpus_regex = new RegexIterator(
            $corpus_iterator,
            '/^.*\.(xml|ccl)$/i',
            RecursiveRegexIterator::GET_MATCH);

        $ccl_array = array();
        foreach ($corpus_regex as $ccl_path => $object) {
            $report_id = $this->getReportId($ccl_path, $corpusId);

            if ($report_id !== null) {
                array_push($ccl_array, ['ccl_path' => $ccl_path, 'report_id' => $report_id]);
            }
        }

        $this->info("number of found files files: " . count($ccl_array));
        if (count($ccl_array) == 0) {
            throw new Exception("Archive does not contain *.xml or *.ccl files: {$corpusDir}");
        }

        $this->info("importing annotations");

        foreach ($userIds as $userId) {
            $this->info("importing annotations for user: " . $userId);
            foreach ($ccl_array as $report) {
                $annotations = new Import_Annotations_CCL($report['ccl_path'], $report['report_id'], $userId,
                    $annotationStage, $annotationSource, $annotationSetId, $ignore_duplicates, $ignore_unknown_types);

                $annotations->read();
                $annotations->processAnnotationns();
                $import = $annotations->importAnnotations();
                if ($import !== 'ok') {
                    $this->info("Import error");
                    $this->info($import['error']);
                    throw new Exception($import['error']);
                }
            }
        }
    }
}
