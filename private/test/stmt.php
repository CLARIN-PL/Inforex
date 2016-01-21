<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Adam Kaczmarek, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * 
 */

$engine = realpath(dirname(__FILE__) . "/../../engine/");

include($engine . "/config.php");
include($engine . "/config.local.php");
include($engine . "/include.php");

$db = new Database($config->get_dsn(), $config->get_log_sql(), $config->get_log_output());

$i=0;

while(true){
//	$sth = $db->mdb2->prepare("SELECT * FROM users WHERE user_id = ?");
//	if (MDB2::isError($sth)){
//		var_dump($sth);
//		die();
//	}
//	$sth->execute(array($i));
//	$sth->free();
	$db->fetch("SELECT * FROM users WHERE user_id = ?", array($i));
	$i++;
	if ($i%100000==0){
		print $i."\n";
	}
}


?>
