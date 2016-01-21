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

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));

/******************** parse cli *********************************************/

$formats = array();
$formats['xml'] = 1;
$formats['plain'] = 2;
$formats['premorph'] = 3;

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
			$config->dsn['phptype'] = 'mysql';
			$config->dsn['username'] = $dbUser;
			$config->dsn['password'] = $dbPass;
			$config->dsn['hostspec'] = $dbHost . ":" . $dbPort;
			$config->dsn['database'] = $dbName;
		}else{
			throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
		}
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
		$daemon = new TaskDaemon($config->dsn, $config->verbose); 	
		while ($daemon->tick()){};	
	}
	catch(Exception $ex){
		print "Error: " . $ex->getMessage() . "\n";
		print_r($ex);
	}
	sleep(2);
}
	

/******************** main function       *********************************************/
// Process all files in a folder
function tick ($config){

} 

/**
 * Handle single request from tasks_documents.
 */
class TaskDaemon{
	
	function __construct($dsn, $verbose){
		$this->db = new Database($dsn, false);
		$this->verbose = $verbose;
		$this->info("Verbose mode: On");		
	}
	
	/**
	 * Print message if verbose mode is on.
	 */
	function info($message){
		if ( $this->verbose ){
			echo $message . "\n";
		}		
	}
	
	/**
	 * Check the queue for new request.
	 */
	function tick(){
		$this->db->execute("BEGIN");
	
		$sql = "SELECT t.*, tr.report_id" .
				" FROM tasks t" .
				" LEFT JOIN tasks_reports tr ON (tr.task_id=t.task_id AND tr.status = 'new')" .
				" WHERE t.type IN ('liner2', 'update-ccl') AND t.status <> 'done' AND t.status <> 'error'" .
				" ORDER BY datetime ASC LIMIT 1";
		$task = $this->db->fetch($sql);
		$this->info($task);
			
		if ( $task === null ){
			return false;
		}
	
		if ( $task['status'] == "new" ){
			$this->db->update("tasks", array("status"=>"process"), array("task_id"=>$task['task_id']));
		}
		
		if ( $task['status'] == "process" && !$task['report_id'] ){
			$this->db->update("tasks", array("status"=>"done"), array("task_id"=>$task['task_id']));		
		}
		
		if ( $task['report_id'] ){	
			$this->db->update("tasks_reports", 
					array("status"=>"process"), 
					array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
		}
				
		$this->db->execute("COMMIT");
		
		if ( $task['report_id'] ){		
			print_r($task);
			$params = json_decode($task['parameters'], true);
			$model = $params['model'];
			$annotation_set_id = $params['annotation_set_id'];
			try{
				if ( $task['type'] == "liner2" ){
					$anns_count = $this->processLiner2($task['report_id'], $task['user_id'], $model, $annotation_set_id);
					$message = sprintf("Number of recognized annotations: %d", $anns_count);
				}
				else if ( $task['type'] == "update-ccl" ){
					$this->processUpdateCcl($task['report_id']);
					$message = "The ccl was updated";
				}
	
				$this->db->update("tasks_reports", 
						array("status"=>"done", "message"=>$message), 
						array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
				
				$this->db->execute("UPDATE tasks SET current_step=current_step+1 WHERE task_id = ?",
						array($task['task_id']));
				return true;
			}
			catch(Exception $ex){
				$this->info("Exception: " . $ex->getMessage());
				
				if ( $ex->getMessage() == "TIMEOUT" ){
					$this->db->update("tasks_reports", 
							array("status"=>"new"), 
							array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
				}
				else{
					$this->db->update("tasks_reports", 
							array("status"=>"error"), 
							array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));				
				}
			}			
		}	
			
		return false;		
	}

	/**
	 * 
	 */
	function processLiner2($report_id, $user_id, $model, $annotation_set_id){
		$content = $this->db->fetch_one("SELECT content FROM reports WHERE id = ?", array($report_id));
		$content = strip_tags($content);
		$content = custom_html_entity_decode($content);
		
		$wsdl = "http://kotu88.ddns.net/nerws/ws/nerws.wsdl";
			
		$liner2 = new WSLiner2($wsdl);	
		$tuples = $liner2->chunk($content, "PLAIN:WCRFT", "TUPLES", $model);
		
		if (preg_match_all("/\((.*),(.*),\"(.*)\"\)/", $tuples, $matches, PREG_SET_ORDER)){
			print "Number of annotations: " . count($matches) . "\n";
			foreach ($matches as $m){
				$annotation_type = strtolower($m[2]);
				list($from, $to) = split(',', $m[1]);
				$ann_text = trim($m[3], '"');
					
				// Todo: kwerendy do przepisania przy użyciu mdb2.
				$sql = "SELECT `id` FROM `reports_annotations` " .
						"WHERE `report_id`=? AND `type`=? AND `from`=? AND `to`=?";
				if (count($this->db->fetch_rows($sql, array($report_id, $annotation_type, $from, $to)))==0){					
					$sql = "INSERT INTO `reports_annotations_optimized` " .
							"(`report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`,`source`) VALUES " .
							'(?, (SELECT annotation_type_id FROM annotation_types WHERE name=? AND group_id=?), ?, ?, ?, ?, now(), "new", "bootstrapping")';
					$params = array($report_id, $annotation_type, $annotation_set_id, $from, $to, $ann_text, $user_id);
					$this->db->execute($sql, $params);
				}
			}
			return count($matches);
		}
		return 0;
	}
	
	/**
	 * 
	 */
	function processUpdateCcl($report_id){
		global $config;
		$row = $this->db->fetch("SELECT content, corpora FROM reports WHERE id = ?", array($report_id));
		$content = $row['content'];
		$corpus_id = $row['corpora'];
		$content = strip_tags($content);
		$content = custom_html_entity_decode($content);
		
		$wsdl = "http://kotu88.ddns.net/nerws/ws/nerws.wsdl";
			
		$liner2 = new WSLiner2($wsdl);	
		$ccl = $liner2->chunk($content, "PLAIN:WCRFT", "CCL", "ner-names");

		$corpus_dir = sprintf("%s/ccls/corpus%04d", $config->path_secured_data, $corpus_id);
		if ( !file_exists($corpus_dir) ){
			$this->info("Create folder: $corpus_dir");
			mkdir($corpus_dir);
		}
		
		$ccl_file = sprintf("%s/%08d.xml", $corpus_dir, $report_id);
		file_put_contents($ccl_file, $ccl);
		
		return true;
	}		
}	
	
/******************** main invoke         *********************************************/
main($config);
?>
