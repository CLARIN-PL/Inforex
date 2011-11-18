<?php
/**
 * Inforex configuration
 */

class Config {
	var $key = "inforex";
	var $session_time = 0;
	
	var $path_engine = '/home/czuk/dev/inforex/engine';
	var $path_www 	= '/var/www/inforex';
	var $path_liner = '/nlp/eclipse/workspace_inforex/inforex_liner';
	var $path_liner2 = 'path_to_set';
	var $path_nerd = '/nlp/eclipse/workspace_inforex/inforex_nerd';
	var $path_python = 'python';
	
	/* Path to a folder outside public avaibalbe space to store data with limited access. */
	var $path_secured_data = '/home/czuk/dev/inforex/secured_data';
	var $takipi_wsdl = 'http://nlp.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl';
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
	
}
$config = new Config();

/**
 * Server configuration
 */ 
 
date_default_timezone_set("Europe/Warsaw");
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors", 1);
ini_set("output_buffering", 0);		
 
?>