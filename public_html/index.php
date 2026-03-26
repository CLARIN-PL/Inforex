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
$enginePath = realpath(__DIR__ . "/../engine/");
require_once($enginePath."/settings.php");
try{

    // TEMP TO REMOVE
    ini_set('memory_limit', '2048M');

    /*** reset cookies if &resetCOOKIES=1 is in page URL ***/
    CookieResetter::resetAllCookies();
    //DebugLogger::logAllDynamicVariables(); // log all dynamic HTTP variables

	Config::Cfg()->put_path_engine($enginePath);
	Config::Cfg()->put_localConfigFilename(realpath($enginePath."/../config/").DIRECTORY_SEPARATOR."config.local.php");

	if ( !file_exists(Config::Cfg()->get_path_engine() . "/templates_c") ){
		throw new Exception("Folder '" . Config::Cfg()->get_path_engine() . "/templates_c' does not exist");
	}

	if ( Config::Cfg()->get_offline() ){
		$variables = array();
		$inforex = new InforexWeb();
		$inforex->doPage("offline", $variables);
        die(ob_get_clean());
	}

	ob_clean();

	$p = new InforexWeb();
	$db = new Database(Config::Cfg()->get_dsn(), Config::Cfg()->get_log_sql(), Config::Cfg()->get_log_output(), Config::Cfg()->get_db_charset());
	
	$auth = new UserAuthorize(Config::Cfg()->get_dsn());
	$auth->authorize(isset($_POST['logout']) && ($_POST['logout']=="1"));
	$user = $auth->getUserData();

	// federation login is enabled
	if(Config::Cfg()->get_federationLoginUrl()){
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

    // load corpus after finally set $user data
    $corpus = RequestLoader::loadCorpus();

	chdir(__DIR__ . "/../engine/");

	$p->execute();

	print trim(ob_get_clean());
}
catch(Exception $e){
    UncaughtExceptionService::UncaughtException($e);
}
