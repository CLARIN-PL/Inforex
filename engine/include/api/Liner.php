<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Liner2{
	
	var $liner_cmd = null;
	var $model = null;
	var $chunking = null;
	var $cseq = null; 
	
	function __construct($liner_cmd, $model){
		$this->liner_cmd = $liner_cmd;
		$this->model = $model;	
	}
	
	function chunk($text, $input_format="plain:wcrft", $output_format="tuples"){

		$cmd = sprintf("echo %s | %s pipe -ini %s -i %s -o %s", 
			escapeshellarg($text),
			$this->liner_cmd,
			$this->model, 
			$input_format,
			$output_format);
			
		fb($cmd);
			
		ob_start();
		$cmd_result = shell_exec($cmd);		
		$r = ob_get_clean();
		return $cmd_result;			
	}
	
}
?>