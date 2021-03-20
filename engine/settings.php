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
    $extended_message = "[".$level."] ".$message." in ".$file.":".$line;
	throw new ErrorException($extended_message, 0, $level, $file, $line);

}  // inforexCentralErrorHandler()

set_error_handler("inforexCentralErrorHandler");

function inforexInitialExceptionHandler ($e) {

    // common handler for all exceptiom before CInforexWebPage was 
    // constructed.

    // reports to http server logs anyway
    error_log($e);
    http_response_code(500);
    // if set display_errors write information on screen
    if (ini_get(’display_errors’)) {
        echo $e;
    } else {
        // dummy message for user
        echo "<h1>500 Internal Server Error</h1>
        An internal server error has been occurred.<br>
        Please try again later.";
    }

} // inforexInitialExceptionHandler()

set_exception_handler("inforexInitialExceptionHandler");

function inforexShutdownFunction() {

    $error = error_get_last();
    if ($error !== null) {
        $e = new ErrorException(
            $error[’message’], 0, $error[’type’], $error[’file’], $error[’line’]
        );
        nforexInitialExceptionHandler($e);
    }

} // inforexShutdownFunction()  

register_shutdown_function("inforexShutdownFunction");

?>
