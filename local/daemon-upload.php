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
Config::Config()->put_localConfigFilename(realpath($enginePath. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR )."config.local.php");
require_once($enginePath . "/cliopt.php");

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

if (!file_exists(Config::Config()->get_path_secured_data()."/import"))
	mkdir(Config::Config()->get_path_secured_data()."/import");
if (!file_exists(Config::Config()->get_path_secured_data()."/import/corpora"))
	mkdir(Config::Config()->get_path_secured_data()."/import/corpora");

// Główna pętla sprawdzająca żądania w kolejce.
//while (true){
	try{
		$daemon = new TaskUploadDaemon(Config::Config());
		$daemon->tick();
		$daemon = null;
	}
	catch(Exception $ex){
		print "Error: " . $ex->getMessage() . "\n";
		print_r($ex);
	}
//	echo "no tasks, sleeping\n";
	sleep(2);
//}

/**
 * Handle single request from tasks_documents.
 */
class TaskUploadDaemon{

	function __construct($config){
		$this->db = new Database($config->get_dsn(), false);
		$GLOBALS['db'] = $this->db; //how to avoid this??
		$this->verbose = $config->get_verbose();
		$this->path_secured_data = $config->get_path_secured_data();
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
		$this->info("start tick");
		$this->db->execute("START TRANSACTION");
		$sql = "SELECT task_id FROM tasks" .
				" WHERE status = 'new' AND type IN (\"dspace_import\", \"nextcloud_import\") ORDER BY datetime ASC LIMIT 1 FOR UPDATE";

		$task_id = $this->db->fetch_one($sql);
		$this->db->update("tasks", array("status"=>"process"), array("task_id"=>$task_id));
		$this->db->execute("COMMIT");
		if ( $task_id === 0){
			return false;
		}

		$task = $this->db->fetch("SELECT * FROM tasks WHERE task_id = ?", array($task_id));
		if (count($task)>0)
		{
			$this->info($task);
			if ( $task['status'] == "new" ){
				$this->db->update(
					"tasks", 
					array("status"=>"process"), 
					array("task_id"=>$task['task_id']));
			}
		}

		if ( count($task)>0 ){
			echo sprintf("Processing task_id=%d ... ", $task['task_id']);
			$result = $this->process($task);
			if ($result){
				$this->db->update("tasks",
					array("status"=>"done"),
					array("task_id"=>$task['task_id']));
				echo "done\n";
			}
			else{ 
				$message = "Error while importing documents"; 
				$this->db->update("tasks",
					array("status"=>"error",
							"message"=>$message),
					array("task_id"=>$task['task_id']));
				echo sprintf("error: %s\n", $message); 
			}
		}	
		return false;
	}

	/**
	  Task processing. Example task description:
	  INSERT INTO tasks (`datetime`, `type`, `parameters`, `corpus_id`, `user_id`, 
	  `max_steps`, `current_step`, `status`) VALUES (now(), 'dspace_import', 
	  '{"path" : "/var/www/html/share/ccl.zip"}', 8, 12, 100, 1, 'new'); 
	 */
	function process($task){
		$result = true;
		$task_id = $task['task_id'];
		$task_parameters = json_decode($task['parameters'], true);
		$corpus_id = intval($task['corpus_id']);
		$user_id = $task['user_id'];
		
		// Utwórz katalog na pliki ccl
		$corpus_dir = sprintf("%s/ccls/corpus%04d", Config::Config()->get_path_secured_data(), $corpus_id);
		if ( !file_exists($corpus_dir) ){
			$this->info("Create folder: $corpus_dir");
			// for recursive call, umask guarantee all created
			// subdirs has right permissions
			$oldumask = umask(0);
			if(mkdir($corpus_dir, 01777, true)===false){
				throw new Exception("Error while creating directory: {$corpus_dir}");
			};
			umask($oldumask); 
		}		
		if($task["type"]=="nextcloud_import"){
            $this->info("nextcloud-import task id: {$task_id}");
		}

		$this->info("dspace-import task id: {$task_id}");
		$new_corpus_path = "{$this->path_secured_data}/import/corpora/{$corpus_id}";
		$this->info("creating new directory: {$new_corpus_path}");		
		//currently it allows only unique directory with `corpus_id` name (no parallel uploads to the same corpus) 
		if ( !file_exists($new_corpus_path) ){
			// Emits an E_WARNING level error if the directory already exists.
			// Emits an E_WARNING level error if the relevant permissions prevent creating the directory. 
                        // for recursive call, umask guarantee all created
                        // subdirs has right permissions
                        $oldumask = umask(0);
                        if(mkdir($new_corpus_path, 01777, true)===false){
                                $this->db->update(
                                        "tasks",
                                        array("status"=>"error",
                                                        "message"=>"Error while creating directory"),
                                        array("task_id"=>$task_id)
                                );
                                throw new Exception("Error while creating directory: {$new_corpus_path}");
                        };
                        umask($oldumask);
		} // !file_exists($new_corpus_path)
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
		else {
			$this->db->update(
					"tasks",
					array("status"=>"error",
							"message"=>"Error while extracting archive"),
					array("task_id"=>$task_id));			
			throw new Exception("Error while extracting: {$zip_path}");
		}
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
		if (count($ccl_array) == 0){
			$this->db->update(
					"tasks",
					array("status"=>"error",
							"message"=>"Archive does not contain *.ccl files"),
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
		
		/* Pobierz aktualną listę podkorpusów */
		$subcorpora = array();
		foreach ( DbCorpus::getCorpusSubcorpora(intval($corpus_id)) as $row ){
			$subcorpora[strtolower($row['name'])] = $row['subcorpus_id'];
		}
		
		foreach($ccl_array as $ccl_path){
			//upload file -> new report -> get_id
			$this->info("processing: {$ccl_path}");
			$title = basename($ccl_path);
			$subcorpus_id = null;

			/* Sprawdź, czy nazwa pliku zawiera nazwę podkorpusu */
			$parts = explode("-", $title);
			if ( count($parts) > 1 ){
				$subcorpus = $parts[0];
				$title = $parts[1];
				
				if ( !isset($subcorpora[strtolower($subcorpus)]) ){
					$subcorpus_id = DbCorpus::createSubcopus($corpus_id, $subcorpus, "");
					$subcorpora[strtolower($subcorpus)] = $subcorpus_id;
				} else {
					$subcorpus_id = $subcorpora[strtolower($subcorpus)];
				}
			}

			//Get the filename without the extension.
			$filename = pathinfo($title, PATHINFO_FILENAME);
										
			$r = new TableReport();
			$r->corpora = intval($corpus_id);
			$r->user_id = intval($user_id); //ner
			$r->format_id = 2; //plain
			$r->type = 1; //nieokreślony
			$r->title = $title;
			$r->status = 2; //Accepted
			$r->date = "now()";
			$r->source = "dspace";
			$r->author = "dspace";
			$r->content = "";
			$r->filename = $filename;

			if ( $subcorpus_id != null ) $r->subcorpus_id = $subcorpus_id;
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
									"message"=>"Document {$r->title} was successfully imported",
									"status"=>"done"));
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
				
				// Utwórz kopię w katalogu secured_data/ccls/corpusxxxx
				$ccl_path_target = sprintf("%s/%08d.xml", $corpus_dir, $r->id);
				copy($ccl_path, $ccl_path_target);				
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

        DbReport::insertEmptyReportExt($r->id);

		$this->info("cleaning tmp disk data");
		system("rm -rf {$new_corpus_path}");
		if ($result)
			$this->info("successfully finished task id: {$task_id}");
		else 
			$this->info("errors while processing task id: {$task_id}");
		sleep(2);
		return $result;
	}
}

?>
