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
	$enginePath = realpath(__DIR__ . "/../engine/");
	require_once($enginePath."/settings.php");
	require_once($enginePath.'/include.php');
    
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
	/********************************************************************/

	$p = new InforexWeb();
	$db = new Database(Config::Config()->get_dsn(), Config::Config()->get_log_sql(), Config::Config()->get_log_output(), Config::Config()->get_db_charset());
	
	$auth = new UserAuthorize(Config::Config()->get_dsn());
	$auth->authorize(isset($_POST['logout']) && ($_POST['logout']=="1"));
	$user = $auth->getUserData();

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

    // load corpus after finally set $user data
    $corpus = RequestLoader::loadCorpus();

	chdir(__DIR__ . "/../engine/");

	$p->execute();

	print trim(ob_get_clean());
}
catch(Exception $e){
	//print "Unexpected exception: <b>" . $e->getMessage() . "</b>";
	//print "<pre>".$e->getTraceAsString()."</pre>";
    //print trim(ob_get_clean());

    $NL=''; $BOpen=''; $BClose=''; $PreOpen=''; $PreClose='';
    if (php_sapi_name() !== "cli") {
        $NL = '<br/>'; $BOpen='<b>'; $BClose='</b>'; $PreOpen='<pre>'; $PreClose='<pre/>';
    }
    $NL .= "\n";
    if(ini_get('display_errors')){
        print("Uncaught exception: ".$BOpen. $e->getMessage().$BClose.$NL);
        print($PreOpen." in file ".$e->getFile()." on line ".$e->getLine().$PreClose.$NL);
        print($PreOpen.$e->getTraceAsString().$PreClose.$NL);
    } else {
        print($BOpen."Uncaught exception has been occurred.".$BClose.$NL);
        print("Please try again later.".$NL);
    }
    // komunikat o błędzie do logu - zawsze, chyba że zablokujemy w ogóle
    if(ini_get('log_errors')){
        //error_log("Uncaught exception: ".print_r($e,true));
        error_log("Uncaught exception: ".$e->getMessage()." in file ".$e->getFile()." on line ".$e->getLine()." Trace:".$e->getTraceAsString());
    }

}
