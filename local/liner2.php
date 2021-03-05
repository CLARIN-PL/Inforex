<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath. DIRECTORY_SEPARATOR . 'include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" ). DIRECTORY_SEPARATOR ."config.local.php");
require_once($enginePath . "/cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("document", "d", "id", "id of the document"));
$opt->addParameter(new ClioptParameter("ini", "i", "model", "path to liner2 model"));

/******************** parse cli *********************************************/

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
	
	Config::Config()->put_dsn(array(
	    			'phptype'  => 'mysql',
	    			'username' => $dbUser,
	    			'password' => $dbPass,
	    			'hostspec' => $dbHost,
	    			'database' => $dbName));	
	$db = new Database(Config::Config()->get_dsn());
	$db->set_encoding('utf8'); 	
	// SET CHARACTER SET sets only subset of SET NAMES params
	// which is set in Databse constructor
	//$db->execute("SET CHARACTER SET utf8");

	Config::Config()->put_corpus($opt->getParameters("corpus"));
	Config::Config()->put_subcorpus($opt->getParameters("subcorpus"));
	Config::Config()->put_documents($opt->getParameters("document"));
	Config::Config()->put_ini($opt->getRequired("ini"));
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	print("\n");
	return;
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){
	
	global $db;

	$ids = array();
	
	foreach ($config->get_corpus() as $c){
		$sql = sprintf("SELECT * FROM reports WHERE corpora = %d", $c);
		foreach ( $db->fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}

	foreach ($config->get_subcorpus() as $s){
		$sql = sprintf("SELECT * FROM reports WHERE subcorpus_id = %d", $s);
		foreach ( $db->fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}
	
	foreach ($config->get_documents() as $d){
		$ids[$d] = 1;
	}
	
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id     ";

		try{
			$annotations_added = 0;
			$doc = $db->fetch("SELECT * FROM reports WHERE id=?",array($report_id));
			$c = HelperBootstrap::bootstrapPremorphFromLinerModel($report_id, 1, $config->get_ini());			
			echo sprintf("%5d %20s recognized %2d, added %2d\n", $doc['id'], $doc['title'], $c['recognized'], $c['added']);
		}
		catch(Exception $ex){
			echo "---------------------------\n";
			echo "!! Exception !! id = {$doc['id']}";
			echo $ex->getMessage();
			echo "---------------------------\n";
		}
	}
} 

/******************** main invoke         *********************************************/
main(Config::Config());
?>
