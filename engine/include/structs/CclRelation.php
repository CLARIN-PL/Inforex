<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class CclRelation{
	var $name = null;
	var $set = null;
	var $fromSentence = null;
	var $fromChannel = null;
	var $toSentence = null;
	var $toChannel = null;	
	var $fromType = null;
	var $toType = null;

	function getName(){
		return $this->name;
	}
	
	function getSet(){
		return $this->set;
	}
	
	function getFromSentence(){
		return $this->fromSentence;
	}
	
	function getToSentence(){
		return $this->toSentence;
	}
	
	function getFromChannel(){
		return $this->fromChannel;
	}
	
	function getToChannel(){
		return $this->toChannel;
	}
	
	function getFromType(){
		return $this->fromType;
	}	

	function getToType(){
		return $this->toType;
	}

	function setName($name){
		$this->name = $name;
	}
	
	function setSet($set){
		$this->set = $set;
	}
	
	function setFromSentence($fromSentence){
		$this->fromSentence = $fromSentence;
	}
	
	function setToSentence($toSentence){
		$this->toSentence = $toSentence;
	}
	
	function setFromChannel($fromChannel){
		$this->fromChannel = $fromChannel;
	}
	
	function setToChannel($toChannel){
		$this->toChannel = $toChannel;
	}
	
	function setFromType($fromType){
		$this->fromType = $fromType;
	}	

	function setToType($toType){
		$this->toType = $toType;
	}		
	
} // CclRelation class
	
?>
