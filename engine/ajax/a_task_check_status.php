<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_task_check_status extends CPage {
		
	function checkPermission(){
		global $user, $corpus;
		return true;
	} 
	
	function execute(){
		global $corpus, $db, $user, $config;
		
		$task_id = intval($_POST['task_id']);
		 		
		$queue = $db->fetch_one("SELECT COUNT(*) FROM tasks t JOIN tasks_reports r ON (t.task_id=r.task_id AND r.status IN ('new','process'))" .
				" WHERE t.status IN ('new','process') AND t.task_id<?", array($task_id)); 
		 		
		$task = $db->fetch("SELECT task_id, type, status, current_step, max_steps, description, message, datetime_start" .
				"  FROM tasks WHERE task_id=?", array($task_id));
		$documents = $db->fetch_one("SELECT count(*) FROM tasks_reports WHERE task_id = ? AND status = 'new'", array($task_id));
		$documents_status = $db->fetch_rows("SELECT * FROM tasks_reports WHERE task_id = ? ORDER BY report_id", array($task_id));
		$processed = $db->fetch_one("SELECT count(*) FROM tasks_reports WHERE task_id = ? AND status != 'new'", array($task_id));
		$errors = $db->fetch_one("SELECT count(*) FROM tasks_reports WHERE task_id = ? AND status = 'error'", array($task_id));
		$percent = sprintf("%3.0f", $task['max_steps'] == 0 ? 0 : $task['current_step']*100.0/$task['max_steps']);
		
		$data = array();
		$data['documents'] = $documents;
		$data['processed'] = $processed;
		$data['errors'] = $errors;
		$data['percent'] = $percent;
		$data['task'] = $task;
		$data['queue'] = intval($queue);
		$data['documents_status'] = $documents_status;
		
		if ($task['type'] == "grab"){
			$task_status_path = "{$config->path_secured_data}/grab/{$task_id}/status.txt";
			if (file_exists($task_status_path)){
				$task_status_file = fopen($task_status_path, "r");
				$data['grab_status'] = intval(fgets($task_status_file));
				fclose($task_status_file);
			}
			else 
				$data['grab_status'] = 0;
		}
		
				 		
		return $data;
	}	
	
} 

?>
