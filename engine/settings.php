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

// We need all messages for developer usage
error_reporting(E_ALL);
// our handler isn't depends from above setting

function inforexCentralErrorHandler($level, $message, $file = ’’, $line = 0) {

	//print("[".$level."] ".$message." in ".$file.":".$line."<br/>\n");
	
	// silently drop ugly strict double constructor report in PHP < 7
	if(version_compare(phpversion(),'7.0.0','<')) {
		if(preg_match("/^Redefining already defined constructor for class Config/",$message)) {
			return;
		}
	} // ver < 7.0

	// converts all errors to exceptions
	throw new ErrorException($message, 0, $level, $file, $line);

}  // inforexCentralErrorHandler()

set_error_handler("inforexCentralErrorHandler");

?>
