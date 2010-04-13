<?php

class CAction {
	
	var $variables = array();
	var $refs = array();
	
	function __construct(){
		
	}
	
	function set($name, $value){
		$this->varaibles[$name] = $value;	
	}
	
	function set_by_ref($name, &$object){
		$this->refs[$name] = $object;		
	}
	
	function get($name){
		return $this->variables[$name];
	}
	
	function getVariables(){
		return $this->varaibles;
	}

	function getRefs(){
		return $this->refs;
	}
		
	function redirect($url){
		header("Location: $url");
		ob_clean();
	}
			
	function execute(){}
	
}

?>
