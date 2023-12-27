<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Server configuration
 */  
date_default_timezone_set("Europe/Warsaw");
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set("display_errors", 1);
ini_set("output_buffering", 0);
ini_set("short_open_tag",1);
setlocale(LC_CTYPE, "en_US.UTF-8");

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'include.php');

set_error_handler("ErrorService::errorHandler");
/*if(! PHPUnitTools::isPHPUnitRunning()) {
	// under PHPUnit this one not works
	register_shutdown_function("ErrorService::shutdownFunction");
}*/
?>
