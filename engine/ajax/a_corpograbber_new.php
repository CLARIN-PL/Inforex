<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpograbber_new extends CPage {
		
	function checkPermission(){
		global $user, $corpus;
		return true;
	} 
	
	function execute(){
		global $corpus, $db, $user;
		$corpograbber_url = strval($_POST['corpograbber_url']);
		$corpograbber_recursive = strval($_POST['corpograbber_recursive']) == "true";
		$data = array();
		$data['user_id'] = $user['user_id'];
		$data['corpus_id'] = $corpus['id'];
		$data['type'] = "grab";
		$data['parameters'] = json_encode(array(
				"url" => $corpograbber_url,
				"recursive" => $corpograbber_recursive));
		$data['max_steps'] = 100;
		$data['current_step'] = 1;
		$db->insert("tasks", $data);
		$task_id = $db->last_id();
		//INSERT INTO tasks (`datetime`, `type`, `parameters`, `corpus_id`, `user_id`, `max_steps`, `current_step`, `status`) 
		//VALUES (now(), 'grab', '{"url" : "www.fronda.pl"}', 8, 12, 100, 1, 'new'); 
		return array("task_id"=>$task_id);
	}	
	
	/**
	 * Create a list of documents on which the task will be performed.
	 */
	function getDocuments($corpus_id, $documents){
		global $db;
		if ( $documents == "all" ){
			$sql = "SELECT id FROM reports WHERE corpora = ?";
			$docs = $db->fetch_ones($sql, "id", array($corpus_id));
		}else{
			echo "Unknown documents: $documents";
		}
		return $docs;
	}
} 

?>
