<?php
/*
 * Created on 2009-02-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
// Server configuration
 
$conf_global_path = '/home/czuk/gpw/engine';

define('GLOBAL_PATH_SQL_BACKUP', '/home/czuk/nlp/gpwc/sql');
define('GLOBAL_PATH_REPORTS_HTML', '/home/czuk/nlp/gpwc/html');
define('GLOBAL_PATH_REPORTS_HTML', '/home/czuk/nlp/gpwc/txt');

define('PATH_PUBLIC_HTML', "/var/www/gpw");
define('PATH_ENGINE', "/home/czuk/gpw/engine");
define('TAKIPI_WSDL', "http://nlp.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl");
define('TAKIPI_WSDL', "http://plwordnet.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl");

$dsn = array(
    'phptype'  => 'mysql',
    'username' => 'gpw',
    'password' => 'gpw',
    'hostspec' => 'localhost',
    'database' => 'gpw',
);

define('RELEASE', 1);

// Load local configuratio if avaiable 

if (file_exists("config.local.php"))
	include_once("config.local.php");
?>
