<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_task_new extends CPage {
		
	function checkPermission(){
		global $user, $corpus;
		return true;
	} 
	
	function execute(){
		global $corpus, $db, $user;
		
		$task = strval($_POST['task']);
		$documents = strval($_POST['documents']);
		$flag = $_POST['flag'];
		$status = $_POST['status'];
		
		$parts = explode(":", $task);
		$task = $parts[0];
		$params = array();
		for ($i=1; $i<count($parts); $i++){
			$kv = explode("=", $parts[$i]);
			if (count($kv)==2){
				$params[$kv[0]] = $kv[1];
			}
		}
		$params_json = json_encode($params);
		
		$docs = $this->getDocuments($corpus['id'], $documents, $flag, $status);

		
		$data = array();
		$data['user_id'] = $user['user_id'];
		$data['corpus_id'] = $corpus['id'];
		$data['type'] = $task;
		$data['parameters'] = $params_json;
		$data['max_steps'] = count($docs);
		$data['current_step'] = 0;


		$db->insert("tasks", $data);
		$task_id = $db->last_id();


		ChromePhp::log($docs);
		if ( count($docs) > 0 ){
			$values = array();
			foreach ($docs as $docid){
				$values[] = array($task_id, $docid);
			}
					
			$db->insert_bulk("tasks_reports", array("task_id", "report_id"), $values);
		}

		return array("task_id"=>$task_id);
	}	
	
	/**
	 * Create a list of documents on which the task will be performed.
	 */
	function getDocuments($corpus_id, $documents, $flag, $status){
		global $db;

		if ( $documents == "all" ){
			$sql = "SELECT id FROM reports WHERE corpora = ?";
			$docs = $db->fetch_ones($sql, "id", array($corpus_id));
		}else{
            $sql = "SELECT r.id FROM reports_flags rf JOIN reports r ON r.id = rf.report_id WHERE (r.corpora = ? AND rf.corpora_flag_id = ? AND rf.flag_id = ?)";
            $docs = $db->fetch_ones($sql, "id",  array($corpus_id, $flag, $status));
		}

		return $docs;
	}
} 

?>
