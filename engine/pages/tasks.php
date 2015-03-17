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
		return true;
		//return hasCorpusRole(CORPUS_ROLE_READ) 
		//	&& hasCorpusRole(CORPUS_ROLE_BROWSE_ANNOTATIONS);
	}
	
	function execute(){		
		global $corpus, $db;
		
		$sql = "SELECT t.*, count(r.task_id) AS documents, u.screename" .
				" FROM tasks t" .
				" JOIN users u USING (user_id)" .
				" LEFT JOIN tasks_reports r USING (task_id)" .
				" WHERE t.corpus_id = ?" .
				" GROUP BY t.task_id" .
				" ORDER BY `datetime` DESC";
		
		$tasks = $db->fetch_rows($sql, array($corpus['id']));
		
		$this->set("tasks", $tasks);
	}
}


?>
