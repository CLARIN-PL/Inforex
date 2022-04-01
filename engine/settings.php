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
	    UncaughtExceptionService::errorClearLast();
        return;
    }   

	// silently drop ugly strict double constructor report in PHP < 7
	if(version_compare(phpversion(),'7.0.0','<')) {
		if(preg_match("/^Redefining already defined constructor for class Config/",$message)) {
			UncaughtExceptionService::errorClearLast();
			return;
		}
	} // ver < 7.0

	// converts all not masked errors to exceptions
    //  there are no return from exception to code continue...
    $severities = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED',
    ];
    $extended_message = "[".$level."] ".$message." in ".$file.":".$line;
    $e = new Exception($extended_message, 0);
    // Just remove the current point from the trace
    $reflection = new ReflectionProperty(get_class($e), 'trace');
    $reflection->setAccessible(true);
    $trace = $reflection->getValue($e);
    array_shift($trace);
    $reflection->setValue($e, $trace);

    $reflection = new ReflectionProperty(get_class($e), 'file');
    $reflection->setAccessible(true);
    $reflection->setValue($e, $file);
    $reflection = new ReflectionProperty(get_class($e), 'line');
    $reflection->setAccessible(true);
    $reflection->setValue($e, $line);

    $text = '';
    $text .= ($severities[$level] ?? $level) . ': ';
    $text .= "$message in $file($line)\n";
    $text .= "Stack trace:\n";
    $text .= $e->getTraceAsString();

    throw($e);

    //throw new ErrorException($text, 0, $level, $file, $line);

    //$extended_message = "[".$level."] ".$message." in ".$file.":".$line;
	//throw new ErrorException($extended_message, 0, $level, $file, $line);

}  // inforexCentralErrorHandler()

function process_error_backtrace($errno, $errstr, $errfile, $errline, $errcontext) {
    if(!(error_reporting() & $errno))
        return;
    switch($errno) {
    case E_WARNING      :
    case E_USER_WARNING :
    case E_STRICT       :
    case E_NOTICE       :
    case E_USER_NOTICE  :
        $type = 'warning';
        $fatal = false;
        break;
    default             :
        $type = 'fatal error';
        $fatal = true;
        break;
    }
    $trace = array_reverse(debug_backtrace());
    array_pop($trace);
    if(php_sapi_name() == 'cli') {
        echo 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
        foreach($trace as $item)
            echo '  ' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()' . "\n";
    } else {
        echo '<p class="error_backtrace">' . "\n";
        echo '  Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
        echo '  <ol>' . "\n";
        foreach($trace as $item)
            echo '    <li>' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()</li>' . "\n";
        echo '  </ol>' . "\n";
        echo '</p>' . "\n";
    }
    if(ini_get('log_errors')) {
        $items = array();
        foreach($trace as $item)
            $items[] = (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()';
        $message = 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ': ' . join(' | ', $items);
        error_log($message);
    }
    if($fatal)
        exit(1);
}

set_error_handler("inforexCentralErrorHandler");
//set_error_handler("process_error_backtrace");
/*
set_error_handler(function(int $severity, string $message, string $file, int $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    $severities = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED',
    ];

    $e = new \Exception;

    // Just remove the current point from the trace
    $reflection = new \ReflectionProperty(get_class($e), 'trace');
    $reflection->setAccessible(true);
    $trace = $reflection->getValue($e);
    array_shift($trace);
    $reflection->setValue($e, $trace);

    $text = '';
    $text .= ($severities[$severity] ?? $severity) . ': ';
    $text .= "$message in $file($line)\n";
    $text .= "Stack trace:\n";
    $text .= $e->getTraceAsString();

    error_log($text);

    if (in_array($severity, [
        E_CORE_ERROR,
        E_ERROR,
        E_RECOVERABLE_ERROR,
        E_PARSE,
        E_COMPILE_ERROR,
        E_USER_ERROR,
    ], true)) {
        http_response_code(500);
        exit(255);
    }

    return true;
});
*/
// Give an extra parameter to the filename
// to save multiple log files
function _fatalog_($extra = false)
{
    static $last_extra;

    // CHANGE THIS TO: A writeable filepath in your system...
    //$filepath = '/var/www/html/sites/default/files/fatal-'.($extra === false ? $last_extra : $extra).'.log';
    $filepath = '../engine/templates_c/fatal-'.($extra === false ? $last_extra : $extra).'.log';

    if ($extra===false) {
        unlink($filepath);
    } else {
        // we write a log file with the debug info
        file_put_contents($filepath, json_encode(debug_backtrace()));
        // saving last extra parameter for future unlink... if possible...
        $last_extra = $extra;
    }
}
/*
Here's an example of how to use it:

// A function which will produce a fatal error
function fatal_example()
{
    _fatalog_(time()); // writing the log
    $some_fatal_code = array()/3; // fatality!
    _fatalog_(); // if we get here then delete last file log
}

Finally to read the contents of the log...

var_dump(json_decode(file_get_contents('/path/to-the-fatal.log')));

*/

function inforexShutdownFunction() {

    //print("inforexShutdownFunction \n");
    // returns null or array( "type"=>, "message"=>, "file"=>, "line"=> )
    // if earlier inforexInitialExceptionHandler was called, there not
    // will be reported twice 
    $error = UncaughtExceptionService::errorGetLast();
    if ($error !== null) {
        /*
        $e = new ErrorException(
            $error["message"], 0, $error["type"], $error["file"], $error["line"]
        );
        inforexInitialExceptionHandler($e);
        */
        // dummy message if not errors displayed by system
        if (!ini_get("display_errors")) {
            UncaughtExceptionService::dummyMessage4User();
        }
    }

} // inforexShutdownFunction()  

if(! PHPUnitTools::isPHPUnitRunning()) {
	// under PHPUnit this one not works
	register_shutdown_function("inforexShutdownFunction");
}

?>
