<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
$engine = realpath(dirname(__FILE__) . "/../engine/");
include($engine . "/config.php");
include($engine . "/config.local.php");
include($engine . "/include.php");
include($engine . "/cliopt.php");
include($engine . "/clioptcommon.php");

mb_internal_encoding("utf-32");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to a folder with documents"));
$opt->addParameter(new ClioptParameter("format", "F", "format", "document format; one of: plain, xml, premorph"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "subcorpus ID"));
$opt->addParameter(new ClioptParameter("user", "u", "id", "user ID"));

/******************** parse cli *********************************************/
$config = new stdClass();

try{
	$opt->parseCli($argv);

    $config->dsn = CliOptCommon::parseDbParameters($opt, "localhost", "root", null, "gpw", "3306");
	$config->folder = $opt->getRequired("folder");
	$config->subcorpus = intval($opt->getRequired("subcorpus"));
	$config->update = $opt->exists("update");
	$config->insert = $opt->exists("insert");
	$config->cleaned = $opt->exists("cleaned");
	$config->user = intval($opt->getRequired("user"));
	
	if (!isset($formats[$config->format])){
		throw new Exception("Incorrect document format '{$config->format}'");
	}

}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

function uploadTxtDocuments(){

}


/******************** main invoke         *********************************************/
//main($config);