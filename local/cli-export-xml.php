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

define("PARAM_DB_URI", "db-uri");
define("PARAM_DOCUMENT", "document");

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter(PARAM_DB_URI, "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter(PARAM_DOCUMENT, "d", "id", "id of the document"));

/******************** parse cli *********************************************/
try{
    /** Parse cli parameters */
    $opt->parseCli($argv);
    Config::Config()->put_dsn(array(
        'phptype' => 'mysql',
        'username' => 'inforex',
        'password' => 'password',
        'hostspec' => 'db' . ":" . '3306',
        'database' => 'inforex'
    ));

    $dsn = CliOptCommon::parseDbParameters($opt, Config::Config()->get_dsn());

    $documentIds = $opt->getParameters(PARAM_DOCUMENT);
    $corpusId = null;

    /** Setup database  */
    $GLOBALS['db'] = new Database($dsn,false);
    $logger = new GroupedLogger();
    var_dump($GLOBALS['db']);
    /** Validate parameters  */
    CliOptCommon::validateDocumentId($documentIds, true);
    echo "Parameters validation... OK\n";

    $corpusId = 158;
    $report = DbReport::getReportById($documentIds);
    /** Process the request  */

    //$htmlStr = ReportContent::getHtmlStr($report);
    //$htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($report['id']));

    $content = $report['content'];
    echo "Orginal xml\n";
    var_dump($content);

    $htmlStr = new HtmlStr2($content, true);
    $sql = "SELECT * FROM reports_annotations WHERE report_id = ?";
    $ans =  $GLOBALS['db']->fetch_rows($sql, array($report['id']));
    foreach ($ans as $a){
        try{
            $htmlStr->insertTag(intval($a['from']), sprintf("<anb id=\"%d\" type=\"%s\"/>", $a['id'], $a['type']), $a['to']+1, sprintf("<ane id=\"%d\"/>", $a['id']), TRUE);
        }
        catch(Exception $ex){
            $this->page->set("ex", $ex);
        }
    }

    $content = $htmlStr->getContent();
    var_dump($content);

}catch(Exception $ex){
    print "!! ". $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    print("\n");
    return;
}
