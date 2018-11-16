<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Adam Kaczmarek, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Skrypt ładuje plik CCL do wybranego podkorpusu.
 */

$engine = realpath(dirname(__FILE__)) . "/../../engine/";

include($engine . "config.php");
include($engine . "config.local.php");
include($engine . "include.php");
include($engine . "cliopt.php");


function runScript($argv){
	global $config;
	
	$opt = new Cliopt();
	$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
	$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
	$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
	$opt->addParameter(new ClioptParameter("file", "f", "filename", "CCL file name"));
	$opt->addParameter(new ClioptParameter("delete", "d", null, "Delete documents in corpus c"));
	

	try{
		$opt->parseCli($argv);
		if ( $opt->exists("db-uri")){
			$dbHost = "localhost";
			$dbUser = "root";
			$dbPass = null;
			$dbName = "gpw";
			$dbPort = "3306";
			$uri = $opt->getRequired("db-uri");
			if ( preg_match("/(.+):(.+)@(.*):(.*)\/(.*)/", $uri, $m)){
				$dbUser = $m[1];
				$dbPass = $m[2];
				$dbHost = $m[3];
				$dbPort = $m[4];
				$dbName = $m[5];
			} else
				throw new Exception(
						"DB URI is incorrect. Given '$uri', but exptected" .
						" 'user:pass@host:port/name'");		
			$config->dsn['phptype'] = 'mysql';
			$config->dsn['username'] = $dbUser;
			$config->dsn['password'] = $dbPass;
			$config->dsn['hostspec'] = $dbHost . ":" . $dbPort;
			$config->dsn['database'] = $dbName;
		}
		
		initDatabase($config->dsn);
		global $db;
		
		$corpus_id = $opt->getRequired("corpus");
		
		if ($opt->exists("delete")){
			$sql = "DELETE FROM reports WHERE corpora={$corpus_id}";
			$db->execute($sql);
			
			DbReport::cleanAfterDelete();			
			die();
		}
		
		$input_file = $opt->getRequired("file");
		$subcorpus_id = $opt->getRequired("subcorpus");
		

		
		
		$r = new CReport();
		$r->corpora = intval($corpus_id);
		$r->subcorpus_id = intval($subcorpus_id);
		$r->user_id = 12; //ner
		$r->format_id = 2; //plain
		$r->type = 1; //nieokreślony
		$r->title = $input_file; 
		$r->status = 1; //nieznany		
		$r->date = "now()";
		$r->source = "dspace";
		$r->author = "dspace";

		$import = new WCclImport();
		$import->importCcl($r, $input_file);
		
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





runScript($argv);

?>
