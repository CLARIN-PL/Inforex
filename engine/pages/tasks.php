<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_tasks extends CPage{

	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return isCorpusOwner(); 
	}
	
	function execute(){		
		global $corpus, $db;
		
		$task_id = intval($_GET['task_id']);
		$corpus_id = intval($corpus['id']);
		
		$this->set("task", $this->getTask($task_id));
		$this->set("task_id", $task_id);
		$this->set("tasks", $this->getTasks($corpus_id));
	}
	
	/**
	 * Return tasks for $corpus_id.
	 */
	function getTasks($corpus_id){
		global $db;
		$sql = "SELECT t.*, count(r.task_id) AS documents, u.screename" .
				" FROM tasks t" .
				" JOIN users u USING (user_id)" .
				" LEFT JOIN tasks_reports r USING (task_id)" .
				" WHERE t.corpus_id = ?" .
				" GROUP BY t.task_id" .
				" ORDER BY `datetime` DESC";
		
		return $db->fetch_rows($sql, array($corpus_id));		
	}
	
	/**
	 * Return task for $task_id and $corpus_id.
	 */
	function getTask($task_id){
		global $db;
		
		$sql = "SELECT * FROM tasks WHERE task_id=?";
		return $db->fetch($sql, array($task_id));
		
	}
}


?>
