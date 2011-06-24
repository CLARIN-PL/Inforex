<?php

ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors", 1);
ini_set("output_buffering", 0);

ob_start();

$stamp_start = time();

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
// Ustaw domyślne kodowanie podczas przetwarzania tekstu
mb_internal_encoding("UTF-8");

// Ustaw timezone na potrzeby Smarty
date_default_timezone_set("Europe/Warsaw");
		
/********************************************************************8
 * Dołącz pliki.
 */
// Wczytanie konfiguracji skryptu
require_once("config.php");

if (!file_exists("config.local.php"))
	die("<center><b><code>config-local.php</code> file not found!</b><br/> Create it and set up the configuration of <i>Inforex</i>.</center>");
else
	require_once("config.local.php");

// Dołączenie podstawowych plików systemu
require_once($config->path_engine . '/include.php');

/********************************************************************8
 * Wczytaj parametry z URL
 */
$annotation_id = isset($_REQUEST['annotation_id']) ? intval($_REQUEST['annotation_id']) : 0; 
$report_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : (isset($_REQUEST['report_id']) ? intval($_REQUEST['report_id']) : 0); 
$corpus_id = isset($_GET['corpus']) ? intval($_GET['corpus']) : 0; 
$relation_id = isset($_REQUEST['relation_id']) ? intval($_REQUEST['relation_id']) : 0; 


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
            "dsn" => $config->dsn,
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
	$user['role']['loggedin'] = "User is loggedin to the system";
	foreach ($roles as $role){
		$user['role'][$role['role']] = $role['description'];
	}
	
	UserActivity::log($user['user_id']);
}

/********************************************************************8
 * Wczytaj korpus
 */
// Obejście na potrzeby żądań, gdzie nie jest przesyłany id korpusu tylko raportu lub anotacji
if ($corpus_id==0 && $report_id==0 && $annotation_id)
	$report_id = db_fetch_one("SELECT report_id FROM reports_annotations WHERE id = ?", $annotation_id);
if ($corpus_id==0 && $report_id>0)
	$corpus_id = db_fetch_one("SELECT corpora FROM reports WHERE id = ?", $report_id);
if ($relation_id>0)	
	$corpus_id = db_fetch_one("SELECT corpora FROM relations r JOIN reports_annotations a ON (r.source_id = a.id) JOIN reports re ON (a.report_id = re.id) WHERE r.id = ?", $relation_id);

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
if ($action && file_exists($config->path_engine . "/actions/a_{$action}.php"))
{
	include($config->path_engine . "/actions/a_{$action}.php");
	$class_name = "Action_{$action}";
	$o = new $class_name();

	// Autoryzuj dostęp do akcji.
	if ($o->isSecure && !$auth->getAuth()){
		// Akcja wymaga autoryzacji, która się nie powiodła.
		fb("Auth required");
	}else{
		// Sprawdź dodatkowe ograniczenia dostępu do akcji.
		if ( ($permission = $o->checkPermission()) === true )
		{
			$page = $o->execute();	
			$page = $page ? $page : $_GET['page']; 
			
			$variables = array_merge($o->getVariables(), $o->getRefs());
		}else{
			$variables = array('action_permission_denied'=> $permission);
			fb("PERMISSION: ".$permission);
		}		
	}
}else
{
	$page = $_GET['page'];
}

/********************************************************************
 * Process an ajax request in first order. If the is no ajax request then show the page content.
 */
$ajax = $_REQUEST['ajax'];
if ($ajax)
{
	/** Process an ajax request */
	include($config->path_engine . "/ajax/a_{$ajax}.php");
	$class_name = "Ajax_{$ajax}";
	$o = new $class_name();

	if ( $o->isSecure && !$auth->getAuth() ) {
		echo json_encode(array("error"=>"Ta operacja wymaga autoryzacji.", "error_code"=>"ERROR_AUTHORIZATION"));				
	}	
	elseif ( ($permission = $o->checkPermission()) === true ) {
		if (is_array($variables))		
			$o->setVariables($variables);
		$page = $o->execute(); 									//// ToDo: Why the $page is set here?	
	}
	else {
		echo json_encode(array("error"=>$permission));		
	}
}
else
{
	/** Show a page content */
	// If the page is not set the set the default 'home'
	$page = $page ? $page : 'home';

	// If the required module does not exist, change it silently to the default.
	if (!file_exists($config->path_engine . "/pages/{$page}.php"))
		$page = "home"; 

	require_once ($config->path_engine . "/pages/{$page}.php");
	$page_class_name = "Page_{$page}";	
	$o = new $page_class_name();
	if (is_array($variables))	
		$o->setVariables($variables);
	
	/** The user is logged in or the page is not secured */

	// Assign objects to the page		
	$o->set('user', $user);
	$o->set('page', $page);
	$o->set('corpus', $corpus);
	$o->set('release', RELEASE);
	$o->loadAnnotations();

	// Check, if the current user can see the real content of the page
	if ( hasRole('admin') 
			|| isCorpusOwner()
    		|| ( count($o->roles) > 0 
    				&& isset($user)
    				&& count( array_intersect( array_keys($user['role']), $o->roles)) > 0
    				&& $o->checkPermission() === true ) 
			|| ( count($o->roles) == 0 
					&& $o->checkPermission() === true ) ) {
    					  
		/* User can see the page */
		$o->execute();
		
		if (file_exists($config->path_www . "/js/page_{$page}.js")){
			$o->set('page_js_file', $config->url . "/js/page_{$page}.js");
		}
	}
	else{
		
		/** User cannot see the page */
		$page = 'norole';
	}

	$page_generation_time = (time() - $stamp_start);

	$o->set('page_generation_time', $page_generation_time);
	$o->display($page); 			
}

ob_flush();

?>
