<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Parse database URI in the for of 'user:pass@host:port/name' and return as an array
 * array('username'=>..., 'password'=>..., 'hostspec'=>..., 'database'=>...) 
 */
function parse_database_uri($uri){
	if ( preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)){
		$dbUser = $m[1];
		$dbPass = $m[2];
		$dbHost = $m[3];
		$dbName = $m[4];
	}else{
		throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
	}
	
	$dsn = array(
	    			'phptype'  => 'mysql',
	    			'username' => $dbUser,
	    			'password' => $dbPass,
	    			'hostspec' => $dbHost,
	    			'database' => $dbName);
	return $dsn; 	
}

?>