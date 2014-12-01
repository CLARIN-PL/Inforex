<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Adam Kaczmarek, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Skrypt przetwarza wskazany korpus lub zestaw dokumentów i usuwa puste chunki lead.
 */

$engine = dirname(__FILE__) . "/../../engine/";

require($engine . 'include/database/CDbReport.php');

include($engine . "config.php");
include($engine . "config.local.php");
include($engine . "include.php");
include($engine . "cliopt.php");


function runScript($argv){
	$opt = new Cliopt();
	$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
	$opt->addParameter(new ClioptParameter("document", "d", "id", "id of the document"));
	$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
	$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));

	try{
		$opt->parseCli($argv);
		
		$dbHost = "localhost";
		$dbUser = "root";
		$dbPass = null;
		$dbName = "gpw";
		$dbPort = "3306";

		if ( $opt->exists("db-uri")){
			$uri = $opt->getRequired("db-uri");
			if ( preg_match("/(.+):(.+)@(.*):(.*)\/(.*)/", $uri, $m)){
				$dbUser = $m[1];
				$dbPass = $m[2];
				$dbHost = $m[3];
				$dbPort = $m[4];
				$dbName = $m[5];
			}else{
				throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
			}
		}
		
		$config->dsn['phptype'] = 'mysql';
		$config->dsn['username'] = $dbUser;
		$config->dsn['password'] = $dbPass;
		$config->dsn['hostspec'] = $dbHost . ":" . $dbPort;
		$config->dsn['database'] = $dbName;
		$config->documents = $opt->getParameters("document");
		$config->subcorpus = $opt->getParameters("subcorpus");
		$config->corpus = $opt->getParameters("corpus");

		initDatabase($config->dsn);
		
		echo "Loading documents ...";
		$reports = DbReport::getReports($config->corpus, $config->subcorpus, $config->documents);
		echo sprintf("Number of documents %d\n", count($reports));
		process($reports);
		
	}catch(Exception $ex){
		print "!! ". $ex->getMessage() . " !!\n\n";
		$opt->printHelp();
		die("\n");
	}
}


function initDatabase($params){
	try{
		$db = new Database($params);
	}catch(Exception $ex){
		echo "Error: 'Database connection failed'\n";
		echo "in: ".$ex->getFile().", line: ". $ex->getLine()."\n";
		exit();
	}
	$GLOBALS['db'] = $db;
}


function removeEmptyLead($content){
	$content = preg_replace("/<chunk id=\"lead\">[\n\r]+<\/chunk>[\n\r]+/", "", $content);
	return $content;
}


function process($reportList){
	$c = 0;
	foreach( $reportList as $report){
		
		$content = $report['content'];
		$content_no_lead = removeEmptyLead($content);		
		
		if ($content != $content_no_lead ){
			print sprintf("id=%s, empty lead\n", $report['id']);
			
			$report = new CReport($report['id']);
			$report->content = $content_no_lead;
			$parse = $report->validateSchema();
			if (count($parse)){
				print_r($parse);
				die();
			}
			$report->save();
			$c++;
		}
		
	}
	echo $c . "\n";
}

runScript($argv);

?>
