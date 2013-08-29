<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
 class CReportAnnotation extends ATable{
 	
 	var $_meta_table = "reports_annotations_optimized";
 	var $_meta_key = "id";
 	var $_meta_stmt = null;
 	
 	var $id = null;
 	var $report_id = null;
 	var $from = null;
 	var $to = null;
 	var $type_id = null;
 	var $text = null;
 	var $user_id = null;
 	var $creation_time = null;
 	var $stage = null;
 	var $source = null;
 	
 	function setReportId($report_id){
		$this->report_id = $report_id;
	}
	
	function setFrom($from){
		$this->from = $from;
	}
	
	function setTo($to){
		$this->to = $to;
	}
	
	function setTypeId($type_id){
		$this->type_id = $type_id;
	}
	
	function setType($type){
                global $db;
                $sql = 'SELECT annotation_type_id FROM annotation_types WHERE name=?';
                $type_id = (int) $db->fetch_one($sql, array($type));
		$this->type_id = $type_id;
	}
	
	function getType(){
                global $db;
                $sql = 'SELECT name FROM annotation_types WHERE annotation_type_id=?';
                $type = (int) $db->fetch_one($sql, array($this->type_id));
		return $type;
	}
	
	function __get($name){
            $return = null;
            if ($name === 'type') {
                $return = $this->getType();
            } else {
                throw new Exception('Cannot get value from undefined variable ("'.$name.'")');
            }
            return $return;
	}
	
	function __set($name, $value){
            if ($name === 'type') {
                $this->setType($value);
            } else {
                throw new Exception('Cannot assign value to undefined variable ("'.$name.'")');
            }
	}
	
	function setText($text){
		$this->text = $text;
	}
	
	function setUserId($user_id){
		$this->user_id = $user_id;
	}
	
	function setCreationTime($time){
		$this->creation_time = $time;
	}
	
	function setStage($stage){
		$this->stage = $stage;
	}
	
	function setSource($source){
		$this->source = $source;
	}
}
 
 ?>
