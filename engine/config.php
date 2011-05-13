<?php
/*
 * Created on 2009-02-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
// Server configuration

class Config {
	var $path_engine = '/home/czuk/dev/inforex/engine';
	var $path_www = '/var/www/inforex';
	var $path_liner = '/nlp/eclipse/workspace_inforex/inforex_liner';
	var $path_nerd = '/nlp/eclipse/workspace_inforex/inforex_nerd';
	var $path_python = 'python';
	/**
	 * Path to a folder outside public avaibalbe space to store data with limited access.
	 */
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
}
$config = new Config();
 
// Czy strona jest wersją publiczną
//define(IS_RELEASE, false);

?>