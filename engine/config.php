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
			"name" => "Poznańskie Centrum Superkomputerowo-Sieciowe",
			"type" => "56 categories",
			"wsdl" => "http://nlp1.synat.pcss.pl/nerws/nerws.wsdl",
			"description" => 
				'<p style="margin-top: 5px">CRF-based model for <b>recognition of 56 categories of proper names</b> in Polish texts.<br/>
    			The description of the base model can be found in <a href="http://nlp.pwr.wroc.pl/en/publications/107/show/publication">
    			<em>Rich Set of Features for Proper Name Recognition in Polish Texts</em></a>.</p>
    			<p style="margin-top: 5px">Usługa NER jest hostowana na serwerach <a href="http://www.man.poznan.pl">Poznańskie Centrum Superkomputerowo-Sieciowe</a>
    			w ramach współpracy przy realizacji projektu <a href="http://www.synat.pl">SYNAT</a> finansowanego przez
    			Narodowe Centrum Badań i Rozwoju (numer grantu SP/I/1/77065/10).</p>'),
	array(
		"name" => "MUC-like model",
		"type" => "PERson, LOCation, ORGanization and OTHer",
		"wsdl" => "http://156.17.129.133/nerws2/ws/nerws.wsdl",
		"description" => "Uses Liner2.3 with a MUC model from the Liner2 Models Fat Pack."),
    	array(
    		"name" => "Binary model",
    		"type" => "proper names boundaries",
    		"wsdl" => "http://nlp.pwr.wroc.pl/liner2/nerws-binary.wsdl",
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
