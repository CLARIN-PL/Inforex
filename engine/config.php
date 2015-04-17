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
	var $path_engine       = 'ABSOLUTE_PATH_TO:inforex/engine';
	var $path_www          = 'ABSOLUTE_PATH_TO:inforex/public_html';	
	var $path_secured_data = 'ABSOLUTE_PATH_TO:inforex/data';

	var $url = 'http://localhost/inforex';
	var $dsn = array(
    		'phptype'  => 'mysql',
    		'username' => 'inforex',
    		'password' => 'password',
    		'hostspec' => 'localhost',
    		'database' => 'inforex',
	);

	var $liner2_api = array(
		array(
			"name" => "Jednostki identyfikacyjne",
			"type" => "granice nazw własnych, nazw serii, przymiotników pochodzących od nazw własnych",
			"wsdl" => "http://kotu88.ddns.net/nerws/ws/nerws.wsdl",
			"model" => "ner-names",
			"description" => ""),
		array(
			"name" => "Jednostki identyfikacyjne",
			"type" => "adj, loc, org, liv, oth, pro, num, fac, eve",
			"wsdl" => "http://kotu88.ddns.net/nerws/ws/nerws.wsdl",
			"model" => "ner-top9",
			"description" => ""),
		array(
			"name" => "Jednostki identyfikacyjne",
			"type" => "82 szczegółowych kategorii",
			"wsdl" => "http://kotu88.ddns.net/nerws/ws/nerws.wsdl",
			"model" => "ner-n82",
			"description" => ""),
		array(
			"name" => "Wyrażenia temporalne TimeX",
			"type" => "granice wyrażeń temporalnych",
			"wsdl" => "http://kotu88.ddns.net/nerws/ws/nerws.wsdl",
			"model" => "timex1",
			"description" => ""),
		array(
			"name" => "Wyrażenia temporalne TimeX",
			"type" => "date, time, duration, set",
			"wsdl" => "http://kotu88.ddns.net/nerws/ws/nerws.wsdl",
			"model" => "timex4",
			"description" => "")
	);
	
	
	var $wccl_match_enable = false;
	
	var $wccl_match_tester_corpora = array(
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; all (1&ndash;551)", 
					"path"=>"/nlp/corpora/pwr/kpwr-release/kpwr-1.2.2-time-disamb/index_time_train.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; A (1&ndash;100)", "path"=>"/index_time_a.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; B (101&ndash;200)", "path"=>"/index_time_b.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; C (201&ndash;300)", "path"=>"/index_time_c.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; D (301&ndash;551)", "path"=>"/index_time_d.txt"),
			array("name"=>"KPWr 1.2.2 TimeML tune","path"=>"/index_time_tune.txt"),
			array("name"=>"KPWr 1.2.7 TimeML train&ndash; all",	"path"=>"/index_time_train.txt")
		); 	
		
	var $wccl_match_daemon = null;

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
	var $log_output		   = "fb";
	var $path_grabber	   = null;
	
					
	function __construct(){
		$this->session_time = 60 * 60 * 24 * 356 * 2;

		// Setup default paths
		$path_engine = dirname(__FILE__);
		$path_inforex = realpath($path_engine . DIRECTORY_SEPARATOR . '..');

		$this->path_engine       = $path_engine;
		$this->path_www          = $path_inforex . DIRECTORY_SEPARATOR . 'public_html';	
		$this->path_secured_data = $path_inforex . DIRECTORY_SEPARATOR . 'secured_data';
		
		$this->wccl_match_tester_script = $path_engine . "/../apps/wccl/wccl-gateway.py";
		$this->wccl_match_script = $path_engine . "/../apps/wccl/wccl-gateway-run.py";		
	}
	
	function __call($method,$arguments){
		if ( substr($method, 0, 4) == "get_" ){
			$parameter_name = substr($method, 4);
			if ( isset($this->$parameter_name) && $this->$parameter_name !== null )
				return $this->$parameter_name;
			else{
				throw new Exception("Paramter '$parameter_name' not defined in the configuration file.");
			}
		}
		else
			call_user_func_array(array($this,"_".$method),$arguments);		
	}
	
}

/** Create global configuration object */
$config = new Config();
 
?>
