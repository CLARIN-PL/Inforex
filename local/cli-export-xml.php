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
        $sql = "SELECT * FROM reports_annotations WHERE report_id = ?";
        $ans =  $this->db->fetch_rows($sql, array($doc['id']));
        foreach ($ans as $a){
            try{
                $htmlStr->insertTag(intval($a['from']), sprintf("<anb id=\"%d\" type=\"%s\"/>", $a['id'], $a['type']), $a['to']+1, sprintf("<ane id=\"%d\"/>", $a['id']), TRUE);
            }
            catch(Exception $ex){
                $this->page->set("ex", $ex);
            }
        }
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportIdWitCTagSorted($report_id));
        echo "Result: \n";

        $content = $htmlStr->getContent();
        $metadata = "<document>" . "\n".
                    "<body>" . "\n".
                    "<metadata>" . "\n".
	                "<author></author>" . "\n".
	                "<author_gender></author_gender>". "\n".
	                "<title></title>". "\n".
	                "<text_type></text_type>". "\n".
	                "<period></period>" . "\n".
	                "<first_edition_year></first_edition_year> " . "\n".
	                "<source_text_year></source_text_year>" . "\n".
	                "<release_location></release_location>" . "\n".
	                "<source_text_url></source_text_url>" . "\n".
	                "<act_number></act_number>" . "\n".
	                "<scean_number></scean_number>" . "\n".
	                "<characters>" . "\n".
		            "<character></character>" . "\n".
                    "</characters>" . "\n" .
                    "</metadata>";

        $tag1open = "<message><author></author><content>";
        $tag1close = "</content></message>";

        $content = str_replace("utf8", "utf-8", $content);
        $content = str_replace("<body>", $metadata, $content);
        $content = str_replace("</body>", "</body>" . "\n" . "</document>", $content);
        $content = str_replace("<subtitle>",$tag1open, $content);
        $content = str_replace("</subtitle>", $tag1close, $content);
        $content = str_replace("<out>", $tag1open, $content);
        $content = str_replace("</out>", $tag1close, $content);
        echo $content;

    }
}