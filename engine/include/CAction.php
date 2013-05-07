<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class CAction {
	
	var $variables = array();
	var $refs = array();
	
	function __construct(){
		
	}
	
	/**
	 * Sprawdź dodatkowe ograniczenia dostępu do funkcji.
	 * @return true - jeżeli ok, string - treść komunikatu, jeżeli brak dostępu
	 */
	function checkPermission(){
		return true;
	}
	
	function set($name, $value){
		$this->variables[$name] = $value;	
	}
	
	function set_by_ref($name, &$object){
		$this->refs[$name] = $object;		
	}
	
	function get($name){
		return $this->variables[$name];
	}
	
	function getVariables(){
		return $this->variables;
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
