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
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

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
	ini_set('memory_limit', '1024M');
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
					"DB URI is incorrect. Given '$uri', but expected" .
					" 'user:pass@host:port/name'");
		$dsn = array();
		$dsn['phptype'] = 'mysql';
		$dsn['username'] = $dbUser;
		$dsn['password'] = $dbPass;
		$dsn['hostspec'] = $dbHost . ":" . $dbPort;
		$dsn['database'] = $dbName;
		Config::Config()->put_dsn($dsn);
	}
	Config::Config()->put_verbose($opt->exists("verbose"));
		
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	print("\n");
	return;
}

try{
	$daemon = new TaskExport(Config::Config());
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
		$this->db = new Database($config->get_dsn(), false);
		$GLOBALS['db'] = $this->db;

		$this->verbose = $config->get_verbose();
		$this->path_exports = $config->get_path_exports();

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
      * Format string with export task information
      * from database data array
      */ 
    private function prepareExportTaskInfo($recordArray){

        $result = 'no task information';
        if(is_array($recordArray)) {
            $result = "EXPORT ID = ".$recordArray["export_id"]
                      ." DESCRIPTION = ".$recordArray["description"]
                      ." SELECTORS = '".$recordArray["selectors"]
                      ."' EXTRACTORS = '".$recordArray["extractors"]
                      ."'";
        }
        return $result;

    } // prepareExportTaskInfo()

	/**
	 * Check the queue for new request.
	 */
	function tick(){
		$this->db->execute("START TRANSACTION");
		$sql = "SELECT * FROM exports WHERE status = 'new' " .
				" ORDER BY datetime_submit ASC LIMIT 1 FOR UPDATE";
		$task = $this->db->fetch($sql);
		if (count($task) == 0){
			$this->db->execute("COMMIT");
			return false;
		}
		$this->info($this->prepareExportTaskInfo($task));
		if ( $task['status'] == "new" ){
			$this->db->update(
					"exports", 
					array(	"status"=>"process",
							"datetime_start"=>date('Y-m-d H:i:s')), 
					array("export_id"=>$task['export_id']));
		}
		$this->db->execute("COMMIT");

		$selectors = array_filter(explode("\n",trim($task['selectors'])));
		$extractors = array_filter(explode("\n",trim($task['extractors'])));
		$indices = array_filter(explode("\n",trim($task['indices'])));
		
		$this->process($task['export_id'], $task['corpus_id'], $selectors, $extractors, $indices, $task['tagging']);

		$message = "Eksport zakończony";
		$status = "done";

		$this->db->update("exports",
					array("status"=>$status, "message"=>$message, "datetime_finish"=>date('Y-m-d H:i:s')),
					array("export_id"=>$task['export_id']));
	}

	/**
	 * Przetworzenie zadania eksportu korpusu
	 * @param $task_id Identyfikator zadania.
	 * @param $corpus_id Identyfikator korpusu, w kontekście którego odbywa się eksport.
	 * @param $selectors Lista selektorów dokumentów
	 * @param $extractors Lista ekstraktorów elementów (anotacje, lematy, relacje)
	 * @param $indices Lista indeksów do utworzenia
	 * @param $tagging String tagging method from ['tagger', 'final', 'final_or_tagger', 'user:{id}']
	 */
	function process($task_id, $corpus_id, $selectors, $extractors, $indices, $tagging){

		$output_folder = "/tmp/inforex_export_{$task_id}";
		$exporter = new CorpusExporter();
		$exporter->exportToCcl($output_folder, $selectors, $extractors, $indices, $task_id, $tagging);
		echo "packing...\n";

		shell_exec("7z a {$output_folder}.7z $output_folder");
		shell_exec("mv {$output_folder}.7z {$this->path_exports}");
		echo "finished.\n\n";

		return true;
	}
}

?>
