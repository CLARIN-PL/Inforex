<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Nowa wersja skryptu do eksportu korpusu, która pozwala na definicję eksportu
 * wskazanych elementów w oparciu o statusy flag dla dokumentów.
 * 
 * I. Wskazanie dokumentów do eksportu
 * 
 *   document_selector: corpus_id=7&flag:clean=3,4
 * 
 * 
 * II. Wskazanie anotacji do eksportu:
 * 
 *   element: flag_name=statusy => definicja elementów
 * 
 *   np.
 *     names=3,4 => annotation_set_id=1
 *     names=3,4 => annotation_subset_id=3,4; relation_set_id=2
 * 
 * III. Wygenerowanie indeksów:
 * 
 *   index:nazwa => flag_name=statusy
 * 
 *   np. 
 *     index:
 */ 

$engine = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
include($engine . DIRECTORY_SEPARATOR . "config.php");
include($engine . DIRECTORY_SEPARATOR . "config.local.php");
include($engine . DIRECTORY_SEPARATOR . "include.php");
include($engine . DIRECTORY_SEPARATOR . "cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();

//--------------------------------------------------------
//configure parameters
$opt = new Cliopt();
$opt->addExecute("php export-corpus.php ...",null);
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("selector", "s", "description", "opis selektora, np. corpus_id=7&name=3,4"));
$opt->addParameter(new ClioptParameter("extractor", "e", "description", "opis esktraktora anotacji i relacji w zależności od wartości flagi, np. names=3,4:annotation_set_id=1"));
$opt->addParameter(new ClioptParameter("list", "l", "description", "generator listy dokumentów"));
$opt->addParameter(new ClioptParameter("output", "o", "path", "ścieżka do katalogu, w którym ma być zapisany korpus"));


//--------------------------------------------------------
// Parse parameters
$config = new stdClass();
$dns = null;
$config->selectors = array();
try {
	$opt->parseCli($argv);

	// Parsowanie db-uri 
	$uri = $opt->getRequired("db-uri");
	if ( preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)){
		$dbUser = $m[1];
		$dbPass = $m[2];
		$dbHost = $m[3];
		$dbName = $m[4];
		$config->dsn = array('phptype'  => 'mysql', 'username' => $dbUser, 'password' => $dbPass,
    							'hostspec' => $dbHost, 'database' => $dbName);		    			
		
	}else{
		throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
	}
	
	$config->output = $opt->getRequired("output");
	$config->selectors = $opt->getParameters("selector");
	$config->extractors = $opt->getParameters("extractor");
	$config->lists = $opt->getParameters("list");
}
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

//--------------------------------------------------------
 try {
 	$db = new Database($config->dsn);
 	$GLOBALS['db'] = $db;
 	
 	$exporter = new CorpusExporter();
 	$exporter->exportToCcl($config->output, $config->selectors, $config->extractors, $config->lists, null, 'tagger');
 }
 catch(Exception $ex){
	print "\n!! ". $ex->getMessage() . " !!\n";
	die("\n");
}


?>

