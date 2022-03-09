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

function errorClearLast() {

       if(version_compare(phpversion(),'7.0.0','<')) {
               // for PHP5 there is no error_clear_last()
               // hint: dummy handler convert all errors from level 0
               //  to well recognizable specific error
               //  below former handler is pushed to internal stack
               set_error_handler('var_dump', 0);
               // call dummy handler for error undef var
               // sets internal register to specific error
               // @ for not console reporting
               @$undef_var_with_ambigous_characteristic_name;
               // restore ( pop ) previous handler from internal stack
               restore_error_handler();
       } else {
               error_clear_last();
       }

} // errorClearLast()

function errorGetLast() {

        $result = error_get_last();
        if(version_compare(phpversion(),'7.0.0','<')) {
                // detect:
                // ["message"]=="Undefined variable: undef_var_with_ambigous_characteristic_name"
		if(is_array($result)
                   && ( isset($result["message"])
                        && preg_match('/undef_var_with_ambigous_characteristic_name/',$result["message"])                                                                             )
                  ) {
                        // error was reseted by errorClearLast()
                        return null;
                } else {
                        return $result;
                }
        } else {
                return $result;
        }

}  // errorGetLast()
 
function inforexCentralErrorHandler($level, $message, $file = ’’, $line = 0) {

    //print("inforexCentralErrorHandler \n");
    // mask errors on error_reporting() settings
    // here we have them all
    $systemErrorReporting = error_reporting();
    if( ($systemErrorReporting & $level) == 0 ) {
        // this error level is masked by system settings
	errorClearLast();
        return;
    }   

	// silently drop ugly strict double constructor report in PHP < 7
	if(version_compare(phpversion(),'7.0.0','<')) {
		if(preg_match("/^Redefining already defined constructor for class Config/",$message)) {
			errorClearLast();
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
            dummyMessage4User();
        } // display_errors
    } // ! CLI

    // serviced errors shouldn't be processed again
    errorClearLast();

} // inforexInitialExceptionHandler()

function dummyMessage4User() {

    // dummy message masks error details for user
    // and writes general error information
    $NL='';
    if (php_sapi_name() !== "cli") {
        $NL = '<br/>';
    } 
    $NL .= "\n";
    print("<h1>500 Internal Server Error</h1>".$NL);
    print("An internal server error has been occurred.".$NL);
    print("Please try again later.".$NL);

} // dummyMessage4User

set_exception_handler("inforexInitialExceptionHandler");

function inforexShutdownFunction() {

    //print("inforexShutdownFunction \n");
    // returns null or array( "type"=>, "message"=>, "file"=>, "line"=> )
    // if earlier inforexInitialExceptionHandler was called, there not
    // will be reported twice 
    $error = errorGetLast();
    if ($error !== null) {
        /*
        $e = new ErrorException(
            $error["message"], 0, $error["type"], $error["file"], $error["line"]
        );
        inforexInitialExceptionHandler($e);
        */
        // dummy message if not errors displayed by system
        if (!ini_get("display_errors")) {
            dummyMessage4User();
        }
    }

} // inforexShutdownFunction()  

function isPHPUnitRunning() {

	// detect if code running under PHPUnit control
	//  two constatnts are defined by this system
	if (! defined('PHPUNIT_COMPOSER_INSTALL') && ! defined('__PHPUNIT_PHAR__')) {
    		// is not PHPUnit run
    		return False;
	} else {
		return True;
	}

} // isPHPUnitRunning()

if(! isPHPUnitRunning()) {
	// under PHPUnit this one not works
	register_shutdown_function("inforexShutdownFunction");
}

?>
