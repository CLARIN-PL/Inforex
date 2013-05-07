<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class TeiElements{
	var $name = null;
	var $attributes = array();
	var $teiElements = array();
	var $teiBody = null;
	
	function __construct($name){
		$this->name = $name;
	}
	
	function setName($name){
		$this->name = $name;
	}
	
	function addAttribute($name, $value){
		$this->attributes[$name] = $value;
	}
	
	function setTeiBody($body){
		$this->teiBody = $body;
	}
	
	function addTeiElements($teiElement){
		$this->teiElements[] = $teiElement;
	}
	
	function getName(){
		return $this->name;
	}
	
	function getAttributes(){
		return $this->attributes;
	}
	
	function getAttribute($name){
		return $this->attributes[$name];
	}
	
	function getTeiBody(){
		return $this->teiBody;
	}
	
	function getTeiElements(){
		return $this->teiElements;
	}
	
	function countTeiBody(){
		return count($this->teiBody);
	}
	
	function countTeiElements(){
		return count($this->teiElements);
	}
}
 
class TeiDocument{
	var $teiName = null;
	var $docTitle = null;
	var $teiElements = array();
	
	function __construct($docTitle, $teiName){
		$this->docTitle = $docTitle;
		$this->teiName = $teiName;
	}
	
	function setTeiName($file_name){
		$this->teiName = $file_name;
	}
	
	function setDocTitle($docTitle){
		$this->docTitle = $docTitle;
	}
	
	function addTeiElements($teiElement){
		$this->teiElements[] = $teiElement;
	}
	
	function getTeiName(){
		return $this->teiName;
	}
	
	function getDocTitle(){
		return $this->docTitle;
	}
	
	function getTeiElements(){
		return $this->teiElements;
	}	
}
?>
