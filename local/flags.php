<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
$engine = "../engine/";
include($engine . "config.php");
include($engine . "config.local.php");
include($engine . "include.php");
include($engine . "cliopt.php");

mb_internal_encoding("UTF-8");

$opt = new Cliopt();
$opt->addExecute("php set-flags.php -c <CORPUS> -U user:pass@host:port/dbname -f Names=3,4 --flag-to-set \"Name Rel\" --init", "Inicjalizuje flagę Name Rel dla dokumentów oznaczonych jako gotowe i sprawdzone dla flagi Name:");
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("document", "d", "report_id", "report id"));
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus_id", "corpus id"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "subcorpus_id", "subcorpus id"));
$opt->addParameter(new ClioptParameter("flag", "f", "flag name", "filter by a flag"));
$opt->addParameter(new ClioptParameter("flag-to-set", null, "flag name", "name of flag to set"));
$opt->addParameter(new ClioptParameter("status", "v", "id", "flag status id"));
$opt->addParameter(new ClioptParameter("init", null, null, "init only not set flags"));
$config = null;
try {
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
	    			'database' => $dbName);	$config->corpus = $opt->getParameters("corpus");
	$config->subcorpus = $opt->getParameters("subcorpus");
	$config->documents = $opt->getParameters("document");
	$config->flag = $opt->getOptional("flag", null);
	$config->flag_to_set = $opt->getOptional("flag-to-set", null);
	$config->status = $opt->getOptional("status", 1);
	$config->init = $opt->exists("init");
	
	if ( count($config->corpus) == 0 && count($config->subcorpus) == 0 && count($config->documents) == 0 )
		throw new Exception("No corpus, subcorpus nor report id set");
		
	if ( !$config->init ){
		echo "Info: use --init to set the flags\n";
	}
	
	if ( $config->flag ){
		$tmp = explode("=", $config->flag);
		$config->flags[$tmp[0]] = explode(",", $tmp[1]);
	}
	else{
		$config->flags = array();
	}
		
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}
	
/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$db = new Database($config->dsn);
	$GLOBALS['db'] = $db;

	$ids = array();
	$n = 0;
	
	$reports = DbReport::getReports($config->corpus, $config->subcorpus, $config->documents, $config->flags);
	echo "Number of documents: " . count($reports) . "\n";
	echo "Init flag '" . $config->flag_to_set ."'\n";
		
	foreach ( $reports as $report){
		$report_id = $report['id'];
		echo "\r " . (++$n) . " z " . count($reports) . " :  id=$report_id     ";
			
		if ( $config->init )
			init_flag_status($report['corpora'], $report_id, $config->flag_to_set, $config->status, $db);
	}
	
} 


/******************** aux function        *********************************************/
/**
 * Set status if not initiated
 */
function init_flag_status($corpora_id, $report_id, $flag_name, $status, $db){
	$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = ?";
	$corpora_flag_id = $db->fetch_one($sql, array($corpora_id, $flag_name));

	if ($corpora_flag_id){
		$value = intval($db->fetch_one("SELECT flag_id FROM reports_flags WHERE corpora_flag_id = ? AND report_id = ?",
							array($corpora_flag_id, $report_id) ) ); 
		if ( $value == -1 || $value == 0 ){
			$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, ?)",
				array($corpora_flag_id, $report_id, $status));
		}	
	}		
}

/******************** main invoke         *********************************************/
main($config);

echo "done ■\n";
	
?>
