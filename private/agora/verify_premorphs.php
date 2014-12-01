<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Adam Kaczmarek, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Skrypt sprawdza poprawność formatu premorph dla plików we wskazanym katalogu.
 */

$engine = realpath(dirname(__FILE__) . "/../../engine/");

require($engine . "/include/database/CDbReport.php");

include($engine . "/config.php");
include($engine . "/config.local.php");
include($engine . "/include.php");
include($engine . "/cliopt.php");


function getParameters($argv){
	$params = null;
	$opt = new Cliopt();
	$opt->addParameter(new ClioptParameter("folder", "f", "PATH", "path to a folder with premorph files"));

	try{
		$opt->parseCli($argv);		
		$params->folder = $opt->getRequired("folder");
		
		if (!file_exists($params->folder)){
			throw new Exception("Katalog '{$config->folder}' nie istnieje");
		}
				
		return $params;
		
	}catch(Exception $ex){
		print "!! ". $ex->getMessage() . " !!\n\n";
		$opt->printHelp();
		die("\n");
	}	
}

function runScript($argv){
	global $config;
	$params = getParameters($argv);
	
	$handle = opendir($params->folder);
	$file_xsd = $config->path_engine."/resources/synat/premorph.xsd";
	
	if ( !$handle ){
		die("ERROR: Wystąpił problem z otwarie katalogu '{$config->folder}'.");
	}
	
	$file_loaded = 0;
	$file_error = 0;
	
	while ( false !== ($file = readdir($handle))){
		if ($file == "." || $file == "..") continue;
		
		$file_loaded++;
		$filename = realpath($params->folder . DIRECTORY_SEPARATOR . $file);

		$content = file_get_contents($filename);
		$parse = HtmlParser::validateXmlWithXsd($content, $file_xsd);

		if ( count($parse) > 0 ){
			$file_error++;
			print "Błąd w '$filename'\n";
			foreach ($parse as $error)
				print sprintf(" Error [%d:%d] %s\n", $error['line'], $error['col'], $error['description']);
		}		

	}	
	
	print "Przetworzono plików: $file_loaded\n";
	print "Plików z błędem    : $file_error\n";
}


runScript($argv);

?>
