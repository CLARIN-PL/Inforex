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
define("PARAM_DOCUMENT", "document");

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));
$opt->addParameter(new ClioptParameter(PARAM_DOCUMENT, "d", "id", "Document id"));

try {
    ini_set('memory_limit', '1024M');
    $opt->parseCli($argv);
    $documentId = $opt->getRequired(PARAM_DOCUMENT);

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
    $loader->load($documentId);
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

    function load($report_id)
    {
        $doc = $this->db->fetch("SELECT * FROM reports WHERE id=?", array($report_id));
        echo "Processing " . $report_id . "\n";
        $content = $doc["content"];
        $htmlStr = new HtmlStr2($content, true);

        // $sql = "SELECT * FROM reports_annotations WHERE report_id = ?";
        $sql = "SELECT rao.id, rao.from, rao.to, rao.text as `txt`, att.name as `type` from reports r" .
            " left join reports_annotations_optimized rao on r.id = rao.report_id" .
            " left join annotation_types att on att.annotation_type_id = rao.type_id" .
            " where r.id = ? and rao.stage=\"final\"" .
            " order by rao.from";


        $ans = $this->db->fetch_rows($sql, array($doc['id']));
        foreach ($ans as $a) {

            $type = explode("_", $a['type']);
            $subtype = count($type) > 1 ? sprintf(' subtype="%s"', $type[1]) : "";
            //$coresp = $coresp !== null ? sprintf(' corresp="%s"', $coresp) : "";
            try {
                $htmlStr->insertTag(intval($a['from']),
                    sprintf("<rs xml:id=\"%s\" type=\"%s\"%s%s/>", $a['id'], $type[0], $subtype), $a['to'] + 1,
                    sprintf("</rs>"), TRUE);
            } catch (Exception $ex) {
                $this->page->set("ex", $ex);
            }
        }
        echo "Result: \n";

        $content = $htmlStr->getContent();

        echo $content;

    }
}