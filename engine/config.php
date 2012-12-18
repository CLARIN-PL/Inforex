<?php
/**
 * Server configuration
 */  
date_default_timezone_set("Europe/Warsaw");
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors", 1);
ini_set("output_buffering", 0);
setlocale(LC_CTYPE, "en_US.UTF-8");		

/**
 * Inforex configuration
 */
class Config {
	var $sid = "gpw";
	var $session_time = 0;
	
	var $path_engine = '/home/czuk/dev/inforex/engine';
	var $path_www 	 = '/var/www/inforex';
	/* Path to a folder outside public avaibalbe space to store data with limited access. */
	var $path_secured_data = '/home/czuk/dev/inforex/secured_data';

	/* Paths to external applications */
	var $path_liner  = '/nlp/eclipse/workspace_inforex/inforex_liner';
	var $path_liner2 = 'path_to_set';
	var $path_nerd   = '/nlp/eclipse/workspace_inforex/inforex_nerd';
	var $path_python = 'python';
	var $path_wcrft  = null;
	var $path_semql  = null;
	var $file_with_rules = null; //wccl-rules transformations-file.ccl
	var $takipi_wsdl = 'http://nlp.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl';
	var $liner_wsdl  = 'http://nlp1.synat.pcss.pl/nerws/nerws.wsdl';
	var $serel_liner_wsdl  = 'http://nlp1.synat.pcss.pl/nerws/nerws.wsdl';
	var $path_wcrft_model = '';

	var $url = 'http://nlp.pwr.wroc.pl/gpw';
	var $dsn = array(
    			'phptype'  => 'mysql',
    			'username' => 'gpw',
    			'password' => 'gpw',
    			'hostspec' => 'localhost',
    			'database' => 'gpw',
				);
					
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
