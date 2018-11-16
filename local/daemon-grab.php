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

if (!file_exists("{$config->path_secured_data}/grab"))
	mkdir("{$config->path_secured_data}/grab");

// Główna pętla sprawdzająca żądania w kolejce.
//while (true){
	try{
		$daemon = new TaskGrabDaemon($config);
		$daemon->tick();
		//while ($daemon->tick()){
		//};
	}
	catch(Exception $ex){
		print "Error: " . $ex->getMessage() . "\n";
		print_r($ex);
	}
	echo "no tasks, sleeping\n";
	sleep(2);
//}

/**
 * Handle single request from tasks_documents.
 */
class TaskGrabDaemon{

	function __construct($config){
		$this->db = new Database($config->dsn, false);
		$GLOBALS['db'] = $this->db; //how to avoid this??
		$this->verbose = $config->verbose;
		$this->path_secured_data = $config->path_secured_data;
		$this->path_grabber = $config->path_grabber;
		$this->info("new daemon, verbose mode: on");
		$this->MAXIMUM_FILE_SIZE = 2500000; //in bytes		
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
		//$this->db->execute("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;");
		$this->db->mdb2->query("START TRANSACTION");
		$sql = "SELECT *" .
				" FROM tasks t" .
				" WHERE type = 'grab'" . 
				" AND status = 'new'" . 
				" ORDER BY datetime ASC LIMIT 1" .
				" FOR UPDATE";
		$task = $this->db->fetch($sql);
		//sleep(10);
		if ($task === null){
			//$this->db->execute("COMMIT");
			$this->db->mdb2->query("COMMIT");
			return false;
		}
		$this->info($task);
		if ( $task['status'] == "new" )
			$this->db->update(
					"tasks", 
					array(
							"status"=>"process",
							"datetime_start"=>date('Y-m-d H:i:s')), 
					array("task_id"=>$task['task_id']));
		//$this->db->execute("COMMIT");
		$this->db->mdb2->query("COMMIT");

		print_r($task);
		$result = $this->process($task);
		$result = 1;
		if ($result)
			$this->db->update("tasks",
					array("status"=>"done"),
					array("task_id"=>$task['task_id']));
		else
			$this->db->update("tasks",
					array("status"=>"error",
							"message"=>"Error while importing documents"),
					array("task_id"=>$task['task_id']));			
		return false;
	}

	/**
	  Task processing. Example task description:
	  INSERT INTO tasks (`datetime`, `type`, `parameters`, `corpus_id`, `user_id`, 
	  `max_steps`, `current_step`, `status`) VALUES (now(), 'grab', 
	  '{"url" : "www.fronda.pl/a/czy-byl-zamach,49182.html"}', 8, 12, 100, 1, 'new'); 
	 */
	function process($task){
		$result = true;
		$task_id = $task['task_id'];
		$task_parameters = json_decode($task['parameters'], true);
		$corpus_id = intval($task['corpus_id']);
		$user_id = $task['user_id'];
		$this->info("grab task id: {$task_id}");
		$grab_data_path = "{$this->path_secured_data}/grab/{$task_id}";
		$this->info("creating new directory: {$grab_data_path}");
		if (mkdir($grab_data_path) === false){
			$this->db->update(
					"tasks",
					array("status"=>"error",
							"message"=>"Error while creating directory"),
					array("task_id"=>$task_id));			
			throw new Exception("Error while creating directory: {$grab_data_path}");
		}

		$url = $task_parameters['url'];
		
		$command = $this->path_grabber . '/get_site.sh "' . addslashes($url) . '" ' . $task_id; 
		$this->info($command);
		system($command);

		$new_corpus_directory = new RecursiveDirectoryIterator($grab_data_path . "/ccl");
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
		if (count($ccl_array) == 0){
			$this->db->update(
					"tasks",
					array("status"=>"error",
							"message"=>"Archive does not contain *.ccl files / Website is protected against web crawling or does not have blocks with minimal amount of text"),
					array("task_id"=>$task_id));				
			throw new Exception("Archive does not contain *.ccl files: {$zip_url}");			
		}
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
			$r->title = $url . " : " . basename($ccl_path);
			$r->status = 1; //nieznany
			$r->date = "now()";
			$r->source = "dspace";
			$r->author = "dspace";			
			$i += 1;
			if (filesize($ccl_path) > $this->MAXIMUM_FILE_SIZE){
				$r->content = "source file is too large (over {$this->MAXIMUM_FILE_SIZE} bytes)";
				$r->save();
				$this->info("import error - file too large");
				$this->db->insert("tasks_reports",
						array("task_id"=>$task_id,
								"report_id"=>$r->id,
								"status"=>"error",
								"message"=>"source file is too large (over {$this->MAXIMUM_FILE_SIZE} bytes)"));
				$result = false;
				continue;
			}			
			try {
				$import = new WCclImport();
				$import_result = $import->importCcl($r, $ccl_path);	
				//insert new_report_id into tasks_reports
				//update current step
				$this->db->update(
						"tasks",
						array("current_step"=>$i),
						array("task_id"=>$task_id));
				if ($import_result){
					//successfull import
					$this->info("ok");
					$this->db->insert("tasks_reports",
							array("task_id"=>$task_id, 
									"report_id"=>$r->id, 
									"status"=>"done",
									"message"=>"successfully imported document: {$r->title}"));
				}				
				else {
					$this->info("import error");
					$this->db->insert("tasks_reports",
							array("task_id"=>$task_id, 
									"report_id"=>$r->id, 
									"status"=>"error",
									"message"=>"error while processing document"));
					$result = false;
				}
			}
			catch (Exception $ex){
				$this->info("import error #2");
				$this->info("Exception: " . $ex->getMessage());
				$this->db->insert("tasks_reports",
						array("task_id"=>$task_id, 
								"report_id"=>$r->id, 
								"status"=>"error",
								"message"=>"error while processing document"));
				$result = false;
			}
		}
		//delete directory
		//$this->info("done - press any key to delete...");
		//fgetc(STDIN);
		//$this->info("cleaning tmp disk data");
		//system("rm -rf {$new_corpus_path}");
		if ($result)
			$this->info("successfully finished task id: {$task_id}");
		else 
			$this->info("errors while processing task id: {$task_id}");
		sleep(2);
		return $result;
	}
}

?>
