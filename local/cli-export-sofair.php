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
const PARAM_OUTPUT_PATH = "output_path";

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));
$opt->addParameter(new ClioptParameter(PARAM_DOCUMENT, "d", "id", "Document id"));
$opt->addParameter(new ClioptParameter(PARAM_OUTPUT_PATH, "p", "out", "output path"));

try {
    ini_set('memory_limit', '1024M');
    $opt->parseCli($argv);
    $documentId = $opt->getRequired(PARAM_DOCUMENT);
    $output_path = $opt->getRequired(PARAM_OUTPUT_PATH);

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

    $loader->load($documentId,  $output_path);

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

    function load($report_id,  $output_path)
    {
        $doc = $this->db->fetch("SELECT r.*, crp.name as 'subcrp' FROM reports r" .
                                     " left join corpus_subcorpora crp on crp.subcorpus_id = r.subcorpus_id WHERE r.id=?",
                                      array($report_id));
        echo "Processing " . $report_id . "\n";
        $content = $doc["content"];
        $htmlStr = new HtmlStr2($content, true);

        $sql = "SELECT rao.id, rao.from, rao.to, rao.text as `txt`, att.name as `type` from reports r" .
            " left join reports_annotations_optimized rao on r.id = rao.report_id" .
            " left join annotation_types att on att.annotation_type_id = rao.type_id" .
            " where r.id = ? and rao.stage=\"final\"" .
            " order by rao.from";

        $ans = $this->db->fetch_rows($sql, array($doc['id']));
        $sql_relations = "SELECT rel.target_id from relations rel" .
            " where rel.source_id = ? and rel.stage=\"final\"";

        foreach ($ans as $a) {

            $relation = $this->db->fetch_one($sql_relations, array($a['id']));

            $type = explode("_", $a['type']);
            $subtype = count($type) > 1 ? sprintf(' subtype="%s"', $type[1]) : "";
            $relation = $relation !== null ? sprintf(' corresp="%s"', $relation) : "";
            try {
                $htmlStr->insertTag(intval($a['from']),
                    sprintf("<rs xml:id=\"%s\" type=\"%s\"%s%s/>", $a['id'], $type[0], $subtype, $relation),
                    $a['to'] + 1,
                    "</rs>", TRUE);
            } catch (Exception $ex) {
                $this->page->set("ex", $ex);
            }
        }
        $output_path = $output_path . "/" . $doc["subcrp"] . "/" . $doc["title"];
        echo "Saving file:: " . $output_path;
        $this->saveFileToDisk($output_path, $htmlStr->getContent());
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