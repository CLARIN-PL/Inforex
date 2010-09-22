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
 
?>