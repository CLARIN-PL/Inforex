<?php
/* 
 * ---
 * Insert tag <sentence> into document
 * ---
 * Created on 2012-02-13 
 */
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
include("../cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("report", "r", "id", "id of the report"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("user", "user", "id", "id of the user"));

/******************** parse cli *********************************************/
$config = null;
try{
	$opt->parseCli($argv);
	
	if ( $opt->exists("db-uri")){
		$uri = $opt->getRequired("db-uri");
		if ( preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)){
			$dbUser = $m[1];
			$dbPass = $m[2];
			$dbHost = $m[3];
			$dbName = $m[4];
		}else{
			throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
		}
	}
	
	$config->dsn = array(
	    			'phptype'  => 'mysql',
	    			'username' => $dbUser,
	    			'password' => $dbPass,
	    			'hostspec' => $dbHost,
	    			'database' => $dbName);	
	
	$config->corpus = $opt->getOptionalParameters("corpus");
	$config->subcorpus = $opt->getOptionalParameters("subcorpus");
	$config->report = $opt->getOptionalParameters("report");
	$config->user = $opt->getOptional("user","1");
	if (!$config->corpus && !$config->subcorpus && !$config->report)
		throw new Exception("No corpus, subcorpus nor report id set");
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** main function       *********************************************/
function main ($config){
	$ids = array();
	$GLOBALS['db'] = new Database($config->dsn);
		
	foreach(DbReport::getReports($config->corpus,$config->subcorpus,$config->report, null) as $row){
		$ids[$row['id']] = $row;
	}
	echo "\r Start set-sentence on " . $config->dsn['hostspec'] . "." . $config->dsn['database'] . " -> " . count($ids) . " documents\n";
		
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id    ";
		ob_flush();
		Premorph::set_sentence_tag($report_id,$config->user);
	}
	echo "\r End set-sentence: " . ($n) . " z " . count($ids) . "\n";
} 

/******************** main invoke         *********************************************/
main($config);
?>
