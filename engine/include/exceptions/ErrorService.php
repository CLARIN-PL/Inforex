<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ErrorService {

    const severities = [
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

    static private function getLevelStr($level) {

        $result = "UNKNOWN ERROR";
        if(array_key_exists($level,self::severities)) {
            $result = self::severities[$level];
        }
        return $result;

    } // getLevelStr()

    static private function errorMsgCLIStr($level,$message,$file,$line) {

        $msg = self::getLevelStr($level)
                    . ' \'' . $message . '\' at '
                    . $file
                    . ' ' . $line . ' Trace: '."\n"
                    . BacktraceService::formatTraceStr(BacktraceService::getBacktrace(4));
        return $msg;

    } // errorMsgCLIStr()

    static private function errorMsgHTMLStr($level,$message,$file,$line) {

        $msg = '<p class="error_backtrace">' . "\n"
               . self::getLevelStr($level) . ' \'' . $message . '\' at ' . $file . ' ' . $line . ' Trace :<br/>'. "\n";
        $msg .= BacktraceService::formatTraceStr(BacktraceService::getBacktrace(4));
        $msg .= '</p>'."\n";
        return $msg;

    } // errorMsgHTMLStr()

    static private function errorMsgStr($level,$message,$file,$line) {

        if(php_sapi_name() == 'cli') {
            return self::errorMsgCLIStr($level,$message,$file,$line);
        } else {
            return self::errorMsgHTMLStr($level,$message,$file,$line);
        }

    } // errorMsgStr()

    static private function throwErrorException($level,$message,$file,$line) {

        //$text = "[".$level."] ".$message." in ".$file.":".$line;
        //throw new ErrorException($text, 0, $level, $file, $line);
 
        $e = new Exception($message, 0);
        // Just remove the current 2 points from the trace
        $reflection = new ReflectionProperty(get_class($e), 'trace');
        $reflection->setAccessible(true);
        $trace = $reflection->getValue($e);
        array_shift($trace);
        array_shift($trace);
        $reflection->setValue($e, $trace);

        $reflection = new ReflectionProperty(get_class($e), 'file');
        $reflection->setAccessible(true);
        $reflection->setValue($e, $file);
        $reflection = new ReflectionProperty(get_class($e), 'line');
        $reflection->setAccessible(true);
        $reflection->setValue($e, $line);

        throw($e);
        
    } // throwErrorException

    static public function errorHandler($level, $message, $file = ’’, $line = 0) {
        // log all to logfile
        if(ini_get('log_errors')){
            $msg = self::getLevelStr($level) 
                    . ' \'' . $message . '\' at ' 
                    . $file 
                    . ' ' . $line . ' Trace: ' 
                    . BacktraceService::formatTraceLogStr(BacktraceService::getBacktrace(2));
            error_log($msg);
        }

        // mask errors on error_reporting() settings
        // here we have them all
        $systemErrorReporting = error_reporting();
        if( ($systemErrorReporting & $level) == 0 ) {
            // this error level is masked by system settings
            UncaughtExceptionService::errorClearLast();
            return;
        }

        // notice, warnings, strict print only if display_errors = 1
        // and continue running program
        switch($level) {
            case E_WARNING      :
            case E_USER_WARNING :
            case E_STRICT       :
            case E_NOTICE       :
            case E_USER_NOTICE  :
            case E_CORE_WARNING :
            case E_COMPILE_WARNING  :
                if(ini_get('display_errors')){
                    print(self::errorMsgStr($level,$message,$file,$line)); 
                } else {
                    // none information in this case
                }
                return;
            break;
            default             :  // fatal errors 
                // call exception - no return
                self::throwErrorException($level,$message,$file,$line);
            break;
        }
         
    } // errorHandler()

    static public function shutdownFunction() {

        // returns null or array( "type"=>, "message"=>, "file"=>, "line"=> )
        // if earlier inforexInitialExceptionHandler was called, there not
        // will be reported twice
        $error = UncaughtExceptionService::errorGetLast();
        if ($error !== null) {
            // dummy message if not errors displayed by system
            if (!ini_get("display_errors")) {
                UncaughtExceptionService::dummyMessage4User();
            } else {
                // no action - system default
            }
        }

    } // shutdownFunction()
 
} // class ErrorService
