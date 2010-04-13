<?php
/*
 * Created on 2009-02-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
// Server configuration

class Config {
	var $path_engine = '/home/czuk/nlp/workspace/GPWKorpusWeb/engine';
	var $takipi_wsdl = 'http://nlp.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl';  
}
$config = new Config();
 
$conf_global_path = '/home/czuk/gpw/engine';
$conf_www_path = '/var/www/gpw';
$conf_www_url = 'http://nlp.pwr.wroc.pl/gpw';

define('GLOBAL_PATH_SQL_BACKUP', '/home/czuk/nlp/gpwc/sql');
define('GLOBAL_PATH_REPORTS_HTML', '/home/czuk/nlp/gpwc/html');
define('GLOBAL_PATH_REPORTS_HTML', '/home/czuk/nlp/gpwc/txt');

define('PATH_PUBLIC_HTML', "/var/www/gpw");
define('PATH_ENGINE', "/home/czuk/gpw/engine");

$dsn = array(
    'phptype'  => 'mysql',
    'username' => 'gpw',
    'password' => 'gpw',
    'hostspec' => 'localhost',
    'database' => 'gpw',
);

define('RELEASE', 0);

// Load local configuratio if avaiable 

if (file_exists("config.local.php"))
	include_once("config.local.php");
?>