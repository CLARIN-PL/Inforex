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

function inforexCentralErrorHandler($level, $message, $file = ’’, $line = 0) {

    //print("inforexCentralErrorHandler \n");
    // mask errors on error_reporting() settings
    // here we have them all
    $systemErrorReporting = error_reporting();
    if( ($systemErrorReporting & $level) == 0 ) {
        // this error level is masked by system settings
        error_clear_last();
        return;
    }   

	// silently drop ugly strict double constructor report in PHP < 7
	if(version_compare(phpversion(),'7.0.0','<')) {
		if(preg_match("/^Redefining already defined constructor for class Config/",$message)) {
            error_clear_last();
			return;
		}
	} // ver < 7.0

	// converts all not masked errors to exceptions
    //  there are no return from exception to code continue...
    $extended_message = "[".$level."] ".$message." in ".$file.":".$line;
	throw new ErrorException($extended_message, 0, $level, $file, $line);

}  // inforexCentralErrorHandler()

set_error_handler("inforexCentralErrorHandler");

function inforexInitialExceptionHandler ($e) {

    // common handler for all exceptiom before CInforexWebPage was 
    // constructed.
    //print("inforexInitialExceptionHandler \n");

    // Uncaught exception reporting main code
    // reports to http server logs anyway
    error_log($e);
    // on CLI console all was done, do nothing more
    if (php_sapi_name() !== "cli") {
        // there actions for browser environment
        http_response_code(500);
        // if set display_errors write information on screen
        if (ini_get("display_errors")) {
            echo $e;
        } else {
            // dummy message for user
            echo "<h1>500 Internal Server Error</h1>
            An internal server error has been occurred.<br>
            Please try again later.";
        } // display_errors
    } // ! CLI

    // serviced errors shouldn't be processed again
    error_clear_last();

} // inforexInitialExceptionHandler()

set_exception_handler("inforexInitialExceptionHandler");

function inforexShutdownFunction() {

    //print("inforexShutdownFunction \n");
    // returns null or array( "type"=>, "message"=>, "file"=>, "line"=> )
    // if earlier inforexInitialExceptionHandler was called, there not
    // will be reported twice 
    $error = error_get_last();
    if ($error !== null) {
        $e = new ErrorException(
            $error["message"], 0, $error["type"], $error["file"], $error["line"]
        );
        inforexInitialExceptionHandler($e);
    }

} // inforexShutdownFunction()  

register_shutdown_function("inforexShutdownFunction");

?>
