<?php

// Czy strona jest wersją publiczną
define(IS_RELEASE, false);

// Wczytanie konfiguracji skryptu
require_once("config.php");

// Dołączenie bibliotek
require_once($conf_global_path . '/include.php');

// gets an existing instance with the same DSN
// otherwise create a new instance using MDB2::factory()
$mdb2 =& MDB2::singleton($dsn);
if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->loadModule('Extended');

$mdb2->query("SET CHARACTER SET 'utf8'");

///// Ustawienia FirePHP
ob_start();
FB::setEnabled(true);

///// Rozpocznij sesję /////
HTTP_Session2::useCookies(true);
HTTP_Session2::start('gpw');
HTTP_Session2::setExpire(time() + 60 * 60 * 24 * 356 * 2);

///// Wykonaj akcję ///// 
$action = $_REQUEST['action'];
if ($action && file_exists("$conf_global_path/actions/a_{$action}.php")){
	include("$conf_global_path/actions/a_{$action}.php");
	$class_name = "Action_{$action}";
	$o = new $class_name();
	$page = $o->execute();	
}else{
	$page = $_GET['page'];
}

$page = $page?$page:'browse';

///// Wczytaj moduł ///// 
$ajax = $_REQUEST['ajax'];
if ($ajax){
	include("$conf_global_path/ajax/a_{$ajax}.php");
	$class_name = "Ajax_{$ajax}";
	$o = new $class_name();
	$page = $o->execute();	
}elseif (file_exists("$conf_global_path/pages/{$page}.php")){
	include("$conf_global_path/pages/{$page}.php");
	$class_name = "Page_{$page}";
	$o = new $class_name();
	$o->execute();
	$o->set('page', $page);
	$o->set('is_release', IS_RELEASE);
	$o->display($page);	
}else{
	die("File not found: $conf_global_path/pages/{$page}.php");
}

?>
