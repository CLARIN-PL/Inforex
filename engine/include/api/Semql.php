<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Semql{

	var $semql_path = null;
	
	function __construct($semql_path){
		$this->semql_path = $semql_path;
		if ( !file_exists($this->getExec()))
			throw new Exception("Path to semqell-analyze.py is incorrect.\nCheck: '{$this->getExec()}'");
	}
	
	function getExec(){
		return $this->semql_path . "/semquel-analyze.py";
	}
	
	function getPath(){
		return $this->semql_path;
	}
	
	function analyze($text){
		$cmd = "echo " . escapeshellarg($text) ." | {$this->getExec()} -j -a base -i {$this->getPath()}/data/transformations.bin";
		return exec_shell_asserted($cmd);
	}
		
}