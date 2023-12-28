<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class CclError{
	var $className = null;
	var $functionName = null;
	var $objects = array();
	var $comments = array();
	
	
	function setClassName($className){
		$this->className = $className;
	}
	
	function setFunctionName($functionName){
		$this->functionName = $functionName;
	}
	
	function addObject($key, $value){
		$this->objects[$key] = $value;
	}
	
	function addComment($value){
		$this->comments[] = $value;
	}
	
	
	function getClassName(){
		return $this->className;
	}
	
	function getFunctionName(){
		return $this->functionName;
	}
	
	function getObjects(){
		return $this->objects;
	}
	
	function getComments(){
		return $this->comments;
	}	
	
	
	function __toString(){
		$str =  "---------------------ERROR-------------------------\n";
		$str .= "class:    {$this->className}\n";
		$str .= "function: {$this->functionName}\n";
		$str .= "comments: \n";
		foreach ($this->comments as $comment)
			$str .= "  $comment\n";
		$str .= "objects: \n";		
		
		foreach ($this->objects as $key=>$obj){
			if ($key=="token"){
				$str .= "  Token:\n";
				$str .= "    Orth: {$obj->getOrth()}\n"; 
				$str .= "    From: {$obj->getFrom()}\n";
				$str .= "    To  : {$obj->getTo()}\n";
			}
			elseif (strpos($key, "annotation") === 0){
				$str .= "  Annotation:\n";
				$str .= "    Key : $key \n";
				$str .= "    Type: {$obj['type']}\n"; 
				$str .= "    From: {$obj['from']}\n"; 
				$str .= "    To  : {$obj['to']}\n";
				$str .= "    Text: {$obj['text']}\n"; 
			}
			elseif ($key=="relation"){
				$str .= "  Relation:\n";
				$str .= "    Source id: {$obj['source_id']}\n"; 
				$str .= "    Target id: {$obj['target_id']}\n"; 
			}
			elseif ($key=="message"){
				$str .= "message: $obj";
			}			
			else {
				$str .= "  $key\n";
				$str .= "    build your own user-friendly dump\n";
			}			
		}		
		return $str;
	}
	
} // CclError class

?>
