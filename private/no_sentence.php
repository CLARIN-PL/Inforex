<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Adam Kaczmarek, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Uwaga - przed użyciem skryptu należy dodać flagę o wartości short='Sent'
 */

$engine = "../engine/";

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

		process(loadReportList($config));
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
		echo "in: ".$ex->getFile().", line: ". $ex->getLine()." (tokenize.php:110)\n";
		exit();
	}
	$GLOBALS['db'] = $db;
}

function loadReportList($config){
	return DbReport::getReports($config->corpus, $config->subcorpus, $config->documents);
}

function loadReport($id){
	return DbReport::getReportById($id);
}

function hasSentenceTags($report){
	return strpos($report["content"], "<sentence>") > 0;
}

function checkSentenceTags($report){
	//$parse = HtmlParser::parseXml($report["content"]);
	//$parse = HtmlParser::validateXmlWithXsd($report["content"], "../engine/resources/synat/premorph.xsd");
	$content = $report["content"];
	// Usuń znaki nowych linii
	$content = preg_replace( "/\r|\n/", "", $content);
	// Usuń zawartość tagów <sentence>
	$content = preg_replace("/(<sentence>)(.*)?(<\/sentence>)/","", $content);
	// Usuń pozostałe białe znaki
	$content = trim(strip_tags($content));

	#echo "Start".$content."ENd";

	// Jeśli w treści coś pozostało - to coś jest źle
	return $content == "";
}


function checkReportSentenceTags($report){
	if(hasSentenceTags($report)){
		return checkSentenceTags($report);
	}
	else{
		return true;
	}
}

function process($reportList){
	foreach( $reportList as $report){
		if(!checkReportSentenceTags($report)){
			print "[".(checkReportSentenceTags($report)?"OK":"BAD")."] Document (".$report["id"].") \n";
			DbReport::updateFlagByShort($report["id"], "Sent", "do poprawy");
		}
	}
}
runScript($argv);

?>
