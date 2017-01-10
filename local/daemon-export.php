<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$engine = realpath(dirname(__FILE__) . "/../engine");
include($engine . "/config.php");
include($engine . "/config.local.php");
include($engine . "/include.php");
include($engine . "/cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", 
		"connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));

/******************** parse cli *********************************************/

$formats = array();
$formats['xml'] = 1;
$formats['plain'] = 2;
$formats['premorph'] = 3;

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
	$config->verbose = $opt->exists("verbose");
		
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

try{
	$daemon = new TaskExport($config);
	$daemon->tick();
}
catch(Exception $ex){
	print "Error: " . $ex->getMessage() . "\n";
	print_r($ex);
}

/**
 * Handle single request from tasks_documents.
 */
class TaskExport{

	function __construct($config){
		$this->db = new Database($config->dsn, false);
		$GLOBALS['db'] = $this->db;
		
		$this->verbose = $config->verbose;
		$this->path_exports = $config->path_exports;
		
		if ( !file_exists($this->path_exports) ){
			mkdir($this->path_exports, 0777, true);
		}
		
	}

	/**
	 * Print message if verbose mode is on.
	 */
	function info($message){
		if ($this->verbose){
			echo $message . "\n";
		}
	}

	/**
	 * Check the queue for new request.
	 */
	function tick(){
		$this->db->mdb2->query("START TRANSACTION");
		$sql = "SELECT * FROM exports WHERE status = 'new' " .
				" ORDER BY datetime_submit ASC LIMIT 1 FOR UPDATE";
		$task = $this->db->fetch($sql);
		if ($task === null){
			$this->db->mdb2->query("COMMIT");
			return false;
		}
		$this->info($task);
		if ( $task['status'] == "new" ){
			$this->db->update(
					"exports", 
					array(	"status"=>"process",
							"datetime_start"=>date('Y-m-d H:i:s')), 
					array("export_id"=>$task['export_id']));
		}
		$this->db->mdb2->query("COMMIT");

		print_r($task);
		
		$selectors = array_filter(explode("\n",trim($task['selectors'])));
		$extractors = array_filter(explode("\n",trim($task['extractors'])));
		$indices = array_filter(explode("\n",trim($task['indices'])));
		
		$result = $this->process($task['export_id'], $task['corpus_id'], $selectors, $extractors, $indices);
		
		$message = "Eksport zakończony";
		$status = "done";

		$this->db->update("exports",
					array("status"=>$status, "message"=>$message, "datetime_finish"=>date('Y-m-d H:i:s')),
					array("export_id"=>$task['export_id']));
	}

	/**
	 * Przetworzenie zadania eksportu korpusu
	 * @param $task_id Identyfikator zadania.
	 * @param $corpus_id Identyfikator korpusu, w kontekście którego odbywa się eskport.
	 * @param $selectors Lista selektorów dokumentów
	 * @param $extractors Lista ekstraktorów elementów (anotacje, lematy, relacje)
	 * @param $indices Lista indektów do utworzenia
	 */
	function process($task_id, $corpus_id, $selectors, $extractors, $indices){
				
		$output_folder = "/tmp/inforex_export_{$task_id}";
		$exporter = new CorpusExporter();
		$exporter->exportToCcl($output_folder, $selectors, $extractors, $indices);
		echo "packing...\n";
		
		shell_exec("7z a {$output_folder}.7z $output_folder");
		shell_exec("mv {$output_folder}.7z {$this->path_exports}");
		echo "finished.\n\n";
		
		return true;
	}
}

?>
