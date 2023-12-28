<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Action_upload extends CAction{
		
	function checkPermission(){
		global $user, $corpus;
		if (!isset($user['role']['admin']) && $corpus['user_id']!=$user['user_id'])
			return "You do not have access to this action";
		else
			return true;
	} 
	
	function execute()
    {
        global $user, $corpus;
        $params = array();
        $params["subcorpus_id"] = $this->getRequestParameter("subcorpus_id", null);
        $params["autosplit"] = $this->getRequestParameterBoolean("autosplit");

        $path = $_FILES["files"]["tmp_name"];
        $name = $_FILES['files']['name'];
        $error = $this->validateFile($path, $name);
        if ($error !== null) {
            $this->set("action_error", "The zip file was not found");
            return null;
        }

        if ($_FILES['files']['error']){
            $this->set("action_error", $_FILES['files']['error']);
            return null;
        }

        $newPath = tempnam(Config::Cfg()->get_path_secured_data(). "/import", "upload_zip_");
        move_uploaded_file($path, $newPath);
        chmod($newPath, 0755);
        $params["path"] = $newPath;

        $task = new TableTask();
        $task->setCorpusId($corpus['id']);
        $task->setParameters(json_encode($params));
        $task->setUserId($user['user_id']);
        $task->setType("upload-zip-txt");
        $task->setDescription("Upload $name");
        $task->insert();

        $link = sprintf("index.php?corpus=%d&page=corpus_tasks&task_id=%d", $corpus['id'], $task->getId());
        $message = sprintf("A new task has been created, redirect to <a href='$link'>upload status</a>");
        $this->set("action_performed", $message);
        $this->set("redirect", $link);
        return null;
    }

    function validateFile($path, $name){
        if ($path == null) {
            return "The zip file was not found";
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if ($ext != "zip"){
            return "Invalid file extension. Expected 'zip' but got '$ext'.";
        }

	    return null;
    }

}
