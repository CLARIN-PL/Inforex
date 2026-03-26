<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath . DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath . DIRECTORY_SEPARATOR . 'include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/") . DIRECTORY_SEPARATOR . "config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();

/******************** set configuration   *********************************************/
const PARAM_DOCUMENT = "document";
const PARAM_CORPUS = "corpus";
const PARAM_OUTPUT_PATH = "output_path";

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));
$opt->addParameter(new ClioptParameter(PARAM_DOCUMENT, "d", "id", "Document id"));
$opt->addParameter(new ClioptParameter(PARAM_OUTPUT_PATH, "p", "out", "output path"));
$opt->addParameter(new ClioptParameter(PARAM_CORPUS, "c", "corpus", "Corpus id"));

try {
    ini_set('memory_limit', '1024M');
    $opt->parseCli($argv);
    $documentId = $opt->getOptional(PARAM_DOCUMENT, "");
    $corpusId = $opt->getOptional(PARAM_CORPUS, "");
    $out_path = $opt->getRequired(PARAM_OUTPUT_PATH);

    $dbHost = "db";
    $dbUser = "inforex";
    $dbPass = "password";
    $dbName = "inforex";
    $dbPort = "3306";

    if ($opt->exists("db-uri")) {
        $uri = $opt->getRequired("db-uri");
        if (preg_match("/(.+):(.+)@(.*):(.*)\/(.*)/", $uri, $m)) {
            $dbUser = $m[1];
            $dbPass = $m[2];
            $dbHost = $m[3];
            $dbPort = $m[4];
            $dbName = $m[5];
            Config::Config()->put_dsn(array(
                'phptype' => 'mysql',
                'username' => $dbUser,
                'password' => $dbPass,
                'hostspec' => $dbHost . ":" . $dbPort,
                'database' => $dbName
            ));
        } else {
            throw new Exception("DB URI is incorrect. Given '$uri', but expected 'user:pass@host:port/name'");
        }
    }
    Config::Config()->put_verbose($opt->exists("verbose"));
} catch (Exception $ex) {
    print "!! " . $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    die("\n");
}

try {
    $loader = new CclLoader(Config::Config()->get_dsn(), Config::Config()->get_verbose());
    $loader->processDocuments($corpusId, $documentId, $out_path);
} catch (Exception $ex) {
    print "Error: " . $ex->getMessage() . "\n";
    print_r($ex);
}
sleep(1);

/**
 * Handle single request from tasks_documents.
 */
class CclLoader
{

    function __construct($dsn, $verbose)
    {
        $this->db = new Database($dsn, false);
        $GLOBALS['db'] = $this->db;
        $this->verbose = $verbose;
    }

    /**
     * Print message if verbose mode is on.
     */
    function info($message)
    {
        if ($this->verbose) {
            echo $message . "\n";
        }
    }
    function processDocuments($corpora_id, $report_id, $out_path){
        if($corpora_id != "") {
            $documents = $this->db->fetch_rows("SELECT * FROM reports WHERE corpora=?", array($corpora_id));
            foreach ($documents as $doc) {
                $this->parseDocument($doc, $out_path);
            }
        }
        if($report_id != "") {
            $doc = $this->db->fetch("SELECT * FROM reports WHERE id=?", array($report_id));
            $this->parseDocument($doc, $out_path);
        }
    }
    function parseDocument($doc, $out_path)
    {
        echo "Processing " . $doc["id"] . "\n";
        $content = $doc["content"];

        if( $doc["format_id"] == 1 || $doc["format_id"] == 3) {
            $this->parseXmlContent($content, $doc, $out_path);
        } else {
            $this->parseTextContent($content, $doc, $out_path);
        }

    }

    /**
     * @param $content
     * @param $doc
     * @param $out_path
     * @return void
     * @throws Exception
     */
    public function parseXmlContent($content, $doc,  $out_path)
    {
        $htmlStr = new HtmlStr2($content, true);
        $sql = "SELECT * FROM reports_annotations ra WHERE ra.report_id = ? AND ra.group =1 AND ra.stage ='final'";
        $ans = $this->db->fetch_rows($sql, array($doc['id']));
        foreach ($ans as $a) {
            try {
                $htmlStr->insertTag(intval($a['from']), sprintf("<anb id=\"%d\" type=\"%s\"/>", $a['id'], $a['type']), $a['to'] + 1, sprintf("<ane id=\"%d\"/>", $a['id']), TRUE);
            } catch (Exception $ex) {
                echo 'Caught exception: ',  $ex->getMessage(), "\n";
            }
        }
        $htmlStr = ReportContent::insertTokensWithTag($htmlStr, DbToken::getTokenByReportIdWitCTagSorted($doc['id']));

        $content = $htmlStr->getContent();
        $content = str_replace('"""', '\'"\'', $content);
        $content = str_replace('&', '&amp;', $content);

        $path = $out_path . "/" . $doc['id'] . ".txt";
        $this->saveFileToDisk($path, $content);
    }

    public function parseTextContent($content, $doc, $out_path)
    {
        $htmlStr = new HtmlStr2($content, true);
        $sql = "SELECT * FROM reports_annotations ra WHERE ra.report_id = ? AND ra.group =1 AND ra.stage ='final'";
        $ans = $this->db->fetch_rows($sql, array($doc['id']));
        foreach ($ans as $a) {
            try {
                $htmlStr->insertTag(intval($a['from']), sprintf("<anb id=\"%d\" type=\"%s\"/>", $a['id'], $a['type']), $a['to'] + 1, sprintf("<ane id=\"%d\"/>", $a['id']), TRUE);
            } catch (Exception $ex) {
                echo 'Caught exception: ',  $ex->getMessage(), "\n";
            }
        }
        $htmlStr = ReportContent::insertTokensWithTag($htmlStr, DbToken::getTokenByReportIdWitCTagSorted($doc['id']));
        $content = $htmlStr->getContent();

        $data =
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
            "<body>\n" . $content . "\n</body>\n";

        $path = $out_path . "/" . $doc['id'] . ".txt";
        $this->saveFileToDisk($path, $data);
    }
    function saveFileToDisk($filePath, $data, $mode = 'w') {
        // Get the directory path from the file path
        $directoryPath = dirname($filePath);

        // Check if the directory exists, if not, create it
        if (!is_dir($directoryPath)) {
            // Attempt to create the directory with 0755 permissions (read/write/execute for owner, read/execute for others)
            if (!mkdir($directoryPath, 0755, true)) {
                return "Failed to create directory.";
            }
        }

        // Open the file with the specified mode ('w' for write, 'a' for append)
        $file = fopen($filePath, $mode);

        // Check if the file was opened successfully
        if ($file === false) {
            return "Failed to open file.";
        }

        // Write data to the file
        $result = fwrite($file, $data);

        // Close the file
        fclose($file);

        // Check if the write operation was successful
        if ($result === false) {
            return "Failed to write to file.";
        } else {
            return "File saved successfully at $filePath.";
        }
    }
}