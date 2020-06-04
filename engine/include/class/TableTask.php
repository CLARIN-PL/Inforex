<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
 class TableTask extends ATable{
 	
 	var $_meta_table = "tasks";
 	var $_meta_key = "task_id";
 	
 	var $task_id = null;
 	var $datetime = null;
 	var $type = null;
 	var $description = null;
 	var $parameters = null;
 	var $corpus_id = null;
 	var $user_id = null;
 	var $max_steps = 1;
 	var $current_step = 0;
 	var $status = "new";
 	var $message = null;

 	function setCorpusId($corpusId){
 	    $this->corpus_id = $corpusId;
    }

    function getCorpusId(){
 	    return $this->corpus_id;
    }

    function setParameters($parameters){
 	    $this->parameters = $parameters;
    }

    function getParameters(){
 	    return $this->parameters;
    }

    function setUserId($userId){
 	    $this->user_id = $userId;
    }

    function getUserId(){
 	    return $this->user_id;
    }

    function setType($type){
 	    $this->type = $type;
    }

    function setStatus($status){
 	    $this->status = $status;
    }

    function setDescription($description){
 	    $this->description = $description;
    }

    function setMaxSteps($maxSteps){
 	    $this->max_steps = $maxSteps;
    }

    function setCurrentStep($currentStep){
 	    $this->current_step = $currentStep;
    }
}
