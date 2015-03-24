<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
 class CTask extends ATable{
 	
 	var $_meta_table = "tasks";
 	var $_meta_key = "task_id";
 	
 	var $task_id = null;
 	var $datetime = null;
 	var $type = null;
 	var $description = null;
 	var $parameters = null;
 	var $corpus_id = null;
 	var $user_id = null;
 	var $max_steps = null;
 	var $current_step = null;
 	var $status = null;
 	var $message = null;
}
 
?>
