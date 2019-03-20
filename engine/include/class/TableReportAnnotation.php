<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
 class TableReportAnnotation extends ATable{
 	
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

	 /**
	  * @var TableReportAnnotationLemma
	  */
 	var $_meta_lemma = null;

 	var $_meta_shared_attributes = array();

 	var $_meta_type_name = null;

 	function getId(){
 		return $this->id;
	}

 	function setReportId($report_id){
		$this->report_id = $report_id;
	}

	function getReportId(){
 		return $this->report_id;
	}
	
	function setFrom($from){
		$this->from = $from;
	}

	function getFrom(){
 		return $this->from;
	}
	
	function setTo($to){
		$this->to = $to;
	}

	function getTo(){
 		return $this->to;
	}
	
	function setTypeId($type_id){
		$this->type_id = $type_id;
	}
	
	function setType($type){
 		$this->_meta_type_name = $type;
	}
	
	function getType(){
 		return $this->_meta_type_name;
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

	function getText(){
 		return $this->text;
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

	function getStage(){
 		return $this->stage;
	}
	
	function setSource($source){
		$this->source = $source;
	}

	function getLength(){
 		return $this->to - $this->from + 1;
	}

	function getMetaSharedAttributes(){
 		return $this->_meta_shared_attributes;
	}

	function setMetaSharedAttributes($attributes){
 		$this->_meta_shared_attributes = $attributes;
	}

	 /**
	  * @param TableReportAnnotationLemma $lemma
	  */
	function setMetaLemma($lemma){
 		$this->_meta_lemma = $lemma;
	}

	function getMetaLemma(){
 		return $this->_meta_lemma;
	}

	function save(){
 		parent::save();
 		if ( $this->_meta_lemma != null ){
 			if ($this->_meta_lemma->getReportAnnotationId() == null ){
 				$this->_meta_lemma->setReportAnnotationId($this->getId());
			}
			$this->_meta_lemma->save();

 			if ($this->_meta_shared_attributes != null ){
 				foreach ($this->_meta_shared_attributes as $attribute){
 					$attribute->setAnnotationId($this->getId());
 					$attribute->save();
				}
			}
		}
	}
}