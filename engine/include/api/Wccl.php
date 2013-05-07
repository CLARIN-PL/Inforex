<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Wccl{
	
	function __construct(){
	}
	
	function run($text, $file_with_rules){		
		$cmd = sprintf("LANG=en_US.utf-8; echo %s | wccl-rules -q -i ccl -o ccl -t nkjp $file_with_rules", escapeshellarg($text));
		return exec_shell_asserted($cmd);
	}
	
}

?>