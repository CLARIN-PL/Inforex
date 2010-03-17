<?php

ini_set("error_reporting", E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

// Czy strona jest wersją publiczną
define(IS_RELEASE, false);

// Ustaw domyślne kodowanie podczas przetwarzania tekstu
mb_internal_encoding("UTF-8");
		
// Wczytanie konfiguracji skryptu
require_once("config.php");

// Dołączenie bibliotek
ini_set("include_path", ini_get("include_path").":".$conf_global_path . '/pear');
require_once($conf_global_path . '/include.php');

function isCookie(){
	if (isset($_COOKIE["cookies"])){
		return true;
	}elseif ($_GET['r'] && $_GET['r']=="1"){
		return isset($_COOKIE["cookies"]);		
	}else{
		setcookie("cookies",time() +"3600");
		header('Location: '.$_SERVER['PHP_SELF'].'?r=1');
	}
	return ; 
}

$options = array(
    'debug' => 2,
    'result_buffering' => false,
);

// gets an existing instance with the same DSN
// otherwise create a new instance using MDB2::factory()

$mdb2 =& MDB2::singleton($dsn, $options);

if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->loadModule('Extended');
$mdb2->loadModule('TableBrowser');
if (PEAR::isError($r = $mdb2->query("SET CHARACTER SET 'utf8'")))
	die("<pre>[init.php] {$r->getUserInfo()}</pre>");


///// Ustawienia FirePHP
//ob_start();
FB::setEnabled(true);

///// Rozpocznij sesję /////
HTTP_Session2::useCookies(true);
HTTP_Session2::start('gpw');
HTTP_Session2::setExpire(time() + 60 * 60 * 24 * 356 * 2);

/********************************************************************8
 * Autoryzacja użytkownika
 */
$params = array(
            "dsn" => $dsn,
            "table" => "users",
            "usernamecol" => "login",
            "passwordcol" => "password",
            "db_fields" => array("screename")
            );
$auth = new Auth("MDB2", $params, null, false);

if ($_POST['logout']=="1")
	$auth->logout();
else
	$auth->start(); 

/********************************************************************8
 * Wykonaj akcje
 */
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
	$o->set('user', $auth->getAuthData());
	$o->set('page', $page);
	$o->set('release', RELEASE);
	$o->set('cookie', isCookie());
	$o->display($page);	
}else{
	die("File not found: $conf_global_path/pages/{$page}.php");
}

?>
