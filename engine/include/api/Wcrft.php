<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Wcrft{
	
	var $wcrft_folder = null;
	var $model = null;
	
	function __construct($wcrft_folder){
		$this->wcrft_folder = $wcrft_folder;
		if ( !file_exists($this->getExec()))
			throw new Exception("Path to wcrft.py is incorrect.\nCheck: '{$this->getPath()}'");
	}
	
	function setModel($model){
		$this->model = $model;
	}
	
	function setConfig($config){
		$this->config = $config;
	}

	function getConfig(){
		return $this->config;
	}
	
	function getModel(){
		if ( !isset($this->model) )
			throw new Exception("Path to WCRFT model not set. Use 'setModel(model)'");
		return $this->model;
	}

	function getExec(){
		return $this->wcrft_folder . "/wcrft/wcrft.py";
	}
	
	function getPath(){
		return $this->wcrft_folder;
	}
	
	function tag($text, $input_format="ccl", $output_format="ccl"){
		$maca = "maca-analyse -qs morfeusz-nkjp-official -i plain -o ccl 2>/dev/null";
		$wmbt = sprintf("wcrft %s -d %s -i ccl -A -o ccl - 2>/dev/null", $this->getConfig(), $this->getModel());
		$cmd = sprintf("LANG=en_US.utf-8; echo %s | %s | %s", escapeshellarg($text), $maca, $wmbt);
		return exec_shell_asserted($cmd);
	}
	
}

?>
