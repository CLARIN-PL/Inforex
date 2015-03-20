<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_dspace_import extends CPage {
		
	var $isSecure = false;
		
	function execute(){
		global $corpus, $db, $user, $config;
		
		$email = strval($_POST['email']);
		$name = strval($_POST['name']);
		$path = strval($_POST['path']);

		if ( $email == "" ){
			die(json_encode(array("error"=>"USER_EMAIL_IS_MISSING")));
		}
		if ( $name == "" ){
			die(json_encode(array("error"=>"CORPUS_NAME_IS_MISSING")));
		}
		if ( $path == "" ){
			die(json_encode(array("error"=>"PATH_IS_MISSING")));
		}
		
		$user = $db->fetch("SELECT * FROM users WHERE email = ?", array($email));
		
		if ( $user == null ){
			die(json_encode(array("error"=>"USER_NOT_FOUND")));
		}
		
		$corpus = new CCorpus();
		$corpus->name = $name;
		$corpus->description = "Corpus imported from DSpace";
		$corpus->public = false;
		$corpus->user_id = $user['user_id'];
		$corpus->save();
		
		$task = new CTask();
		$task->user_id = $user['user_id'];
		$task->type = "dspace_import";
		$task->parameters = json_encode(array("path"=>$path));
		$task->corpus_id = $corpus->id;
		$task->max_steps = 100;
		$task->current_step = 0;
		$task->status = "new";
		$task->save();
		
		$url = sprintf("%s?page=tasks&corpus=%d&task_id=%d", $config->url, $corpus->id, $task->task_id);
		 		
		die(json_encode(array("redirect"=>$url)));
	}		
} 

?>
