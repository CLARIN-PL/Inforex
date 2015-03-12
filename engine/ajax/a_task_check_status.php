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
		global $corpus, $db, $user;
		
		$task_id = intval($_POST['task_id']);
		 		
		$task = $db->fetch("SELECT * FROM tasks WHERE task_id=?", array($task_id));
		$queue = $db->fetch_one("SELECT count(*) FROM tasks_reports WHERE task_id = ? AND status = 'new'", array($task_id));
		$processed = $db->fetch_one("SELECT count(*) FROM tasks_reports WHERE task_id = ? AND status != 'new'", array($task_id));
		$errors = $db->fetch_one("SELECT count(*) FROM tasks_reports WHERE task_id = ? AND status = 'error'", array($task_id));
		$percent = sprintf("%3.0f", $task['current_step']*100.0/$task['max_steps']);
		 		
		return array('queue'=>$queue, 'processed'=>$processed, 'errors'=>$errors, 'percent'=>$percent, 'task'=>$task);
	}	
	
} 

?>
