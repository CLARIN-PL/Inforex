<?php

ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED);
//ini_set("display_errors", 0);
ini_set("output_buffering", 0);


ob_start();

/********************************************************************8
 * Ustaw funkcję formatującą wyjątki
 */
function custom_exception_handler($exception){
	echo "<h1 style='background:red; color:white; margin: 0px'>Exception</h1>";
	echo "<pre style='border: 1px solid red; padding: 5px; background: #FFE1D0; margin: 0px'>";
	print_r($exception);
	echo "</pre>";
}
set_exception_handler('custom_exception_handler');

/********************************************************************/
// Czy strona jest wersją publiczną
define(IS_RELEASE, false);

// Ustaw domyślne kodowanie podczas przetwarzania tekstu
mb_internal_encoding("UTF-8");

// Ustaw timezone na potrzeby Smarty
date_default_timezone_set("Europe/Warsaw");
		
/********************************************************************8
 * Dołącz pliki.
 */
// Wczytanie konfiguracji skryptu
require_once("config.php");


// Dołączenie bibliotek
//ini_set("include_path", ini_get("include_path").":/home/czuk/PEAR");
require_once($conf_global_path . '/include.php');

/********************************************************************8
 * Wczytaj parametry z URL
 */
$annotation_id = isset($_REQUEST['annotation_id']) ? intval($_REQUEST['annotation_id']) : 0; 
$report_id = isset($_REQUEST['report_id']) ? intval($_REQUEST['report_id']) : 0; 
$corpus_id = isset($_GET['corpus']) ? intval($_GET['corpus']) : 0; 


/********************************************************************8
 * Połączenie z bazą danych
 */
$options = array(
    'debug' => 2,
    'result_buffering' => false,
);

$mdb2 =& MDB2::singleton($dsn, $options);

if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->loadModule('Extended');
$mdb2->loadModule('TableBrowser');
db_execute("SET CHARACTER SET 'utf8'");


/********************************************************************8
 * Aktywuj FireBug-a
 */
FB::setEnabled(true);

/********************************************************************8
 * Rozpocznij sesję
 */
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
            "db_fields" => array("user_id", "screename")
            );
$auth = new Auth("MDB2", $params, null, false);

if ($_POST['logout']=="1")
	$auth->logout();
else
	$auth->start(); 

$user = $auth->getAuthData();
// Pobierz role użytkownika
if ($user){
	$roles = db_fetch_rows("SELECT * FROM users_roles us JOIN roles USING (role) WHERE user_id=".$user['user_id']);
	$user->role = null;
	foreach ($roles as $role){
		$user['role'][$role['role']] = $role['description'];
	}
}

/********************************************************************8
 * Wczytaj korpus
 */
// Obejście na potrzeby żądań, gdzie nie jest przesyłany id korpusu tylko raportu lub anotacji
if ($corpus_id==0 && $report_id==0 && $annotation_id)
	$report_id = db_fetch_one("SELECT report_id FROM reports_annotations WHERE id = ?", $annotation_id);
if ($corpus_id==0 && $report_id>0)
	$corpus_id = db_fetch_one("SELECT corpora FROM reports WHERE id = ?", $report_id);
$corpus = db_fetch("SELECT * FROM corpora WHERE id=".intval($corpus_id));
// Pobierz prawa dostępu do korpusu dla użytkowników
if ($corpus){
	$roles = db_fetch_rows("SELECT *" .
			" FROM users_corpus_roles ur" .
			" WHERE ur.corpus_id = ?", array($corpus['id']));
	$corpus['role'] = array();
	foreach ($roles as $role)
		$corpus['role'][$role['user_id']][$role['role']] = 1;
}

/********************************************************************8
 * Wykonaj akcje
 */
$action = $_POST['action'];
if ($action && file_exists("$conf_global_path/actions/a_{$action}.php")){
	include("$conf_global_path/actions/a_{$action}.php");
	$class_name = "Action_{$action}";
	$o = new $class_name();

	// Autoryzuj dostęp do akcji.
	if ($o->isSecure && !$auth->getAuth()){
		// Akcja wymaga autoryzacji, która się nie powiodła.
		fb("Auth required");
	}else{
		// Sprawdź dodatkowe ograniczenia dostępu do akcji.
		if ( ($permission = $o->checkPermission()) === true ){
			$page = $o->execute();	
			$page = $page ? $page : $_GET['page']; 
			
			$variables = array_merge($o->getVariables(), $o->getRefs());
		}else{
			$variables = array('action_permission_denied'=> $permission);
			fb("PERMISSION: ".$permission);
		}		
	}
}else{
	$page = $_GET['page'];
}

$top_menu = array("home", "download", "ner", "backup", "corpus", "user_roles", "import", "tracker");
$page = ($corpus || in_array($page, $top_menu)) ? ( $page ? $page : 'corpus') : 'home';

/********************************************************************8
 * Wygeneruj stronę lub żądanie AJAX
 */
$ajax = $_REQUEST['ajax'];
if ($ajax){
	include("$conf_global_path/ajax/a_{$ajax}.php");
	$class_name = "Ajax_{$ajax}";
	$o = new $class_name();

	if ( $o->isSecure && !$auth->getAuth() ){
		echo json_encode(array("error"=>"Ta operacja wymaga autoryzacji."));				
	}	
	elseif ( ($permission = $o->checkPermission()) === true ){
		$o->setVariables($variables);
		$page = $o->execute();	
	}else{
		echo json_encode(array("error"=>$permission));		
	}
	
//	echo json_encode(array("error"=>"Ta funkcjonalność wymaga logowania"));
}elseif (file_exists("$conf_global_path/pages/{$page}.php")){
	include("$conf_global_path/pages/{$page}.php");
	$class_name = "Page_{$page}";	
	$o = new $class_name();
	$o->setVariables($variables);
	
	if ($o->isSecure && !$auth->getAuth()){
		include("$conf_global_path/pages/login.php");
		$o = new Page_login();
		$o->display("login");
	}
	else{
		$o->execute();
		$o->set('user', $user);
		$o->set('page', $page);
		$o->set('corpus', $corpus);
		$o->set('release', RELEASE);
		
		if (file_exists("{$conf_www_path}/js/page_{$page}.js")){
			$o->set('page_js_file', "{$conf_www_url}/js/page_{$page}.js");
		}
		$o->display($page);
	}	
}else{
	die("Moduł <b>{$page}</b> nie istnieje");
}

ob_flush();

?>
