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
	var $warnings = array();
	
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

    /**
     * Add an warning occured during execution of the page.
     * @param $warning
     */
	function addWarning($warning){
		$this->warnings[] = $warning;
	}

	function addWarnings($warnings){
		$this->warnings = array_merge($this->warnings, $warnings);
	}

	function getWarnings(){
		return $this->warnings;
	}
			
	function execute(){}
	
}

?>
