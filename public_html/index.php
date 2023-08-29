<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

$loaderName = __DIR__ . "/loader.php";
if(file_exists($loaderName)) {
    require_once($loaderName);
}
ob_start();
try{
    // TEMP TO REMOVE
    ini_set('memory_limit', '2048M');
    /********************************************************************/

    $enginePath = realpath(__DIR__ . "/../engine/");
	require_once($enginePath."/settings.php");
	require_once($enginePath.'/include.php');

    /*** reset cookies if &resetCOOKIES=1 is in page URL ***/
    CookieResetter::resetAllCookies();
    DebugLogger::logAllDynamicVariables(); // log all dynamic HTTP variables

	Config::Config()->put_path_engine($enginePath);
	Config::Config()->put_localConfigFilename(realpath($enginePath."/../config/").DIRECTORY_SEPARATOR."config.local.php");

	if ( !file_exists(Config::Config()->get_path_engine() . "/templates_c") ){
		throw new Exception("Folder '" . Config::Config()->get_path_engine() . "/templates_c' does not exist");
	}

	if ( Config::Config()->get_offline() ){
		$variables = array();
		$inforex = new InforexWeb();
		$inforex->doPage("offline", $variables);
        die(ob_get_clean());
	}

	ob_clean();

	$p = new InforexWeb();
	$db = new Database(Config::Config()->get_dsn(), Config::Config()->get_log_sql(), Config::Config()->get_log_output(), Config::Config()->get_db_charset());
	
	$auth = new UserAuthorize(Config::Config()->get_dsn());
	$auth->authorize(isset($_POST['logout']) && ($_POST['logout']=="1"));
	$user = $auth->getUserData();

	$corpus = RequestLoader::loadCorpus();

	// federation login is enabled
	if(Config::Config()->get_federationLoginUrl()){
		$clarinUser = $auth->getClarinUser();

		// try to connect to local account
		if($clarinUser){
            $user = $auth->getClarinLogin();

            // show initial clarin login page if this is users first time logging with federation login
            if(!$user)
                $_GET['page']='login_clarin';
		}
		// if clarin token not present/ expired
		else{
            $auth->authorize(true);
            $user = null;
		}
    }

	chdir("../engine");

	$p->execute();

	print trim(ob_get_clean());
}
catch(Exception $e){
	print "Unexpected exception: <b>" . $e->getMessage() . "</b>";
	print "<pre>".$e->getTraceAsString()."</pre>";
    print trim(ob_get_clean());
}
