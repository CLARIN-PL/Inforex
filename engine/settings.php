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

// threshold of count types for one set/subset combination
define('MAX_TYPES_LIMIT_THRESHOLD',300);
define('MAX_TYPES_LABEL_INDEX',999999999999); // over autoincrement id
define('MAX_TYPES_NAME_LABEL','...');

set_error_handler("ErrorService::errorHandler");
if(! PHPUnitTools::isPHPUnitRunning()) {
	// under PHPUnit this one not works
	register_shutdown_function("ErrorService::shutdownFunction");
}
?>
