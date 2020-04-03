<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

ob_start();
try{
	/********************************************************************/
	$PATH_CONFIG = "../engine";
    $PATH_CONFIG_LOCAL = "../config";

	/* Wczytaj obiekt konfiguracji */
	require_once("$PATH_CONFIG/config.php");
	$config = new Config();

	/* Nadpisz domyślną konfigurację przez lokalną konfigurację. */
	if ( file_exists("$PATH_CONFIG_LOCAL/config.local.php") ) {
        include_once("$PATH_CONFIG_LOCAL/config.local.php");
    }

	/* Dołącz wszystkie biblioteki */
	require_once($config->get_path_engine() . '/include.php');

	if ( !file_exists($config->get_path_engine() . "/templates_c") ){
		throw new Exception("Folder '" . $config->get_path_engine() . "/templates_c' does not exist");
	}

	if ( $config->offline ){
		$variables = array();
		$inforex = new InforexWeb();
		$inforex->doPage("offline", $variables);
        die(ob_get_clean());
	}

	/********************************************************************8
	 * Połączenie z bazą danych (stary sposób, tylko na potrzeby web)
	 */

	$options = array(
		'debug' => 2,
		'result_buffering' => false,
	);

	$mdb2 =& MDB2::singleton($config->get_dsn(), $options);

	if (PEAR::isError($mdb2)) {
		die($mdb2->getMessage());
	}
	$mdb2->loadModule('Extended');
	$mdb2->loadModule('TableBrowser');


	ob_clean();
	/********************************************************************/

	$p = new InforexWeb();
	$db = new Database($config->get_dsn(), $config->get_log_sql(), $config->get_log_output(), $config->get_db_charset());
	
	$auth = new UserAuthorize($config->get_dsn());
	$auth->authorize($_POST['logout']=="1");
	$user = $auth->getUserData();
	$corpus = RequestLoader::loadCorpus();

	// federation login is enabled
	if($config->federationLoginUrl){
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
