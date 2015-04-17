<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
ob_start();
try{
	/********************************************************************8
	 * Dołącz pliki.
	 */
	/* Wczytaj obiekt konfiguracji */
	require_once("../engine/config.php");
	$config = new Config();

	/* Nadpisz domyślną konfigurację przez lokalną konfigurację. */
	if ( file_exists("../engine/config.local.php") )
		include_once("../engine/config.local.php");

	/* Dołącz wszystkie biblioteki */
	require_once($config->get_path_engine() . '/include.php');

	if ( !file_exists($config->get_path_engine() . "/templates_c") ){
		throw new Exception("Folder '" . $config->get_path_engine() . "/templates_c' does not exist");
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
	db_execute("SET CHARACTER SET 'utf8'");
	db_execute("SET NAMES 'utf8'");
	ob_clean();
	/********************************************************************/

	$p = new InforexWeb();
	$db = new Database($config->get_dsn(), $config->get_log_sql(), $config->get_log_output());
	
	$auth = new UserAuthorize($config->get_dsn());
	$auth->authorize($_POST['logout']=="1");
	$user = $auth->getUserData();
	$corpus = RequestLoader::loadCorpus();

	chdir("../engine");
	$p->execute();

	print trim(ob_get_clean());
}
catch(Exception $e){
	print "Unexpected exception: <b>" . $e->getMessage() . "</b>";
	print "<pre>".$e->getTraceAsString()."</pre>";
}

?>
