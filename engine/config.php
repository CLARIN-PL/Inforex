<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Server configuration
 */  
date_default_timezone_set("Europe/Warsaw");
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set("display_errors", 1);
ini_set("output_buffering", 0);
ini_set("short_open_tag",1);
setlocale(LC_CTYPE, "en_US.UTF-8");		

/**
 * Inforex configuration
 */
class Config {
	var $sid = "gpw";
	var $session_time = 0;
	
	/* Inforex basic configuration */
	var $path_engine       = 'inforex/engine';
	var $path_www          = 'inforex/public_html';	
	var $path_secured_data = 'inforex/data';

	var $url = 'http://localhost/inforex';
	var $dsn = array(
    		'phptype'  => 'mysql',
    		'username' => 'root',
    		'password' => 'root',
    		'hostspec' => 'localhost',
    		'database' => 'inforex',
	);

	var $liner2_api = array(
		array(
			"name" => "Jednostki identyfikacyjne",
			"type" => "granice nazw własnych, nazw serii, przymiotników pochodzących od nazw własnych",
			"wsdl" => "http://156.17.128.169/nerws/ws/nerws.wsdl",
			"model" => "ner-names",
			"description" => ""),
		array(
			"name" => "Jednostki identyfikacyjne",
			"type" => "adj, loc, org, liv, oth, pro, num, fac, eve",
			"wsdl" => "http://156.17.128.169/nerws/ws/nerws.wsdl",
			"model" => "ner-top9",
			"description" => ""),
		array(
			"name" => "Jednostki identyfikacyjne",
			"type" => "82 szczegółowych kategorii",
			"wsdl" => "http://156.17.128.169/nerws/ws/nerws.wsdl",
			"model" => "ner-n82",
			"description" => ""),
		array(
			"name" => "Wyrażenia temporalne TimeX",
			"type" => "granice wyrażeń temporalnych",
			"wsdl" => "http://156.17.128.169/nerws/ws/nerws.wsdl",
			"model" => "timex1",
			"description" => ""),
		array(
			"name" => "Wyrażenia temporalne TimeX",
			"type" => "date, time, duration, set",
			"wsdl" => "http://156.17.128.169/nerws/ws/nerws.wsdl",
			"model" => "timex4",
			"description" => "")
	);

	/* Advanced parameters */
	var $path_python       = 'python';
	var $path_liner        = null;
	var $path_liner2       = null;
	var $path_nerd         = null;
	var $path_wcrft        = null;
	var $path_semql        = null;
	var $file_with_rules   = null; 
	var $takipi_wsdl       = null;
	var $liner_wsdl        = null;
	var $serel_liner_wsdl  = null;
	var $path_wcrft_model  = "";
	var $wcrft_config	   = "nkjp_s2.ini";
	var $wcrft2_config	   = "nkjp_e2";
	var $log_sql           = false;
	
					
	function __construct(){
		$this->session_time = 60 * 60 * 24 * 356 * 2;
	}
	
	function __call($method,$arguments){
		if ( substr($method, 0, 4) == "get_" ){
			$parameter_name = substr($method, 4);
			if ( isset($this->$parameter_name) && $this->$parameter_name != null )
				return $this->$parameter_name;
			else
				throw new Exception("Paramter '$parameter_name' not defined in the configuration file.");
		}
		else
			call_user_func_array(array($this,"_".$method),$arguments);		
	}
	
}

/** Create global configuration object */
$config = new Config();
 
?>
