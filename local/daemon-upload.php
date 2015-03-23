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

// Główna pętla sprawdzająca żądania w kolejce.
while (true){
	try{
		$daemon = new TaskUploadDaemon($config);
		while ($daemon->tick()){
		};
	}
	catch(Exception $ex){
		print "Error: " . $ex->getMessage() . "\n";
		print_r($ex);
	}
	echo "no tasks, sleeping\n";
	sleep(2);
}

/**
 * Handle single request from tasks_documents.
 */
class TaskUploadDaemon{

	function __construct($config){
		$this->db = new Database($config->dsn, false);
		$GLOBALS['db'] = $this->db; //how to avoid this??
		$this->verbose = $config->verbose;
		$this->path_secured_data = $config->path_secured_data;
		$this->info("new daemon, verbose mode: on");
	}

	/**
	 * Print message if verbose mode is on.
	 */
	function info($message){
		if ($this->verbose)
			echo $message . "\n";
	}

	/**
	 * Check the queue for new request.
	 */
	function tick(){
		$this->db->execute("BEGIN");
		$sql = "SELECT *" .
				" FROM tasks t" .
				" WHERE type = 'dspace_import'" . 
				" AND status = 'new'" . 
				" ORDER BY datetime ASC LIMIT 1";
		$task = $this->db->fetch($sql);
		if ($task === null)
			return false;
		$this->info($task);
		if ( $task['status'] == "new" )
			$this->db->update(
					"tasks", 
					array("status"=>"process"), 
					array("task_id"=>$task['task_id']));
		$this->db->execute("COMMIT");

		print_r($task);
		$this->process($task);
		$this->db->update("tasks",
				array("status"=>"done"),
				array("task_id"=>$task['task_id']));
		return true;
	}

	/**
	  Task processing. Example task description:
	  INSERT INTO tasks (`datetime`, `type`, `parameters`, `corpus_id`, `user_id`, 
	  `max_steps`, `current_step`, `status`) VALUES (now(), 'dspace_import', 
	  '{"path" : "/var/www/html/share/ccl.zip"}', 8, 12, 100, 1, 'new'); 
	 */
	function process($task){
		$task_id = $task['task_id'];
		$task_parameters = json_decode($task['parameters'], true);
		$corpus_id = intval($task['corpus_id']);
		$user_id = $task['user_id'];
		$this->info("dspace-import task id: {$task_id}");
		$new_corpus_path = "{$this->path_secured_data}/import/corpora/{$corpus_id}";
		$this->info("creating new directory: {$new_corpus_path}");
		//currently it allows only unique directory with `corpus_id` name (no parallel uploads to the same corpus) 
		if (mkdir($new_corpus_path) === false)
			throw new Exception("Error while creating directory: {$new_corpus_path}");
		$zip_path = $task_parameters['path'];
		/*$zip_path = "{$this->path_secured_data}/import/tmp/{$new_corpus_id}.zip";
		$this->info("downloading file: {$zip_url} as {$zip_path}");
		if (file_put_contents($zip_path, fopen($zip_url, 'r')) === false)
			throw new Exception("Error while downloading file: {$zip_url}");*/
		$this->info("extracting archive: {$zip_path}");
		$zip = new ZipArchive();
		if ($zip->open($zip_path) === true){
			$zip->extractTo($new_corpus_path);
			$zip->close();
		}
		else
			throw new Exception("Error while extracting: {$zip_url}");
		//count files in dir
		$new_corpus_directory = new RecursiveDirectoryIterator($new_corpus_path);
		$new_corpus_iterator = new RecursiveIteratorIterator($new_corpus_directory);
		//files must have *.ccl extension
		$new_corpus_regex = new RegexIterator(
				$new_corpus_iterator, 
				'/^.+\.ccl$/i',
				RecursiveRegexIterator::GET_MATCH);
		$ccl_array = array();
		foreach($new_corpus_regex as $ccl_path => $object)
			array_push($ccl_array, $ccl_path);		
		$this->info("number of CCL files: " . count($ccl_array));
		$this->db->update(
				"tasks",
				array("current_step"=>1, "max_steps"=>count($ccl_array)),
				array("task_id"=>$task_id));		
		//update task max_step/current step
		$this->info("importing files");
		$i = 0;
		//for file in dir:
		foreach($ccl_array as $ccl_path){
			//upload file -> new report -> get_id
			$this->info("processing: {$ccl_path}");
			$r = new CReport();
			$r->corpora = intval($corpus_id);
			$r->user_id = intval($user_id); //ner
			$r->format_id = 2; //plain
			$r->type = 1; //nieokreślony
			$r->title = basename($ccl_path);
			$r->status = 1; //nieznany
			$r->date = "now()";
			$r->source = "dspace";
			$r->author = "dspace";			
			
			
			$import = new WCclImport();
			$import->importCcl($r, $ccl_path);	
			$i += 1;
			//insert new_report_id into tasks_reports
			//update current step
			$this->db->update(
					"tasks",
					array("current_step"=>$i),
					array("task_id"=>$task_id));
		
		}
		//delete directory
		//$this->info("done - press any key to delete...");
		//fgetc(STDIN);
		$this->info("cleaning tmp disk data");
		system("rm -rf {$new_corpus_path}");
		$this->info("successfully finished task id: {$task_id}");
		sleep(2);
	}
}

?>
