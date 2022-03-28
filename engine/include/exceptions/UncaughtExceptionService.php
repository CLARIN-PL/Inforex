<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class UncaughtExceptionService {

    static public function errorClearLast() {

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

    static public function errorGetLast() {

        $result = error_get_last();
        if(version_compare(phpversion(),'7.0.0','<')) {
            // detect:
            // ["message"]=="Undefined variable: undef_var_with_ambigous_characteristic_name"
            if(is_array($result)
                && ( isset($result["message"])
                    && preg_match('/undef_var_with_ambigous_characteristic_name/',$result["message"])
                )
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
 

    static public function dummyMessage4User() {

        // dummy message masks error details for user
        // and writes general error information
        $NL='';
        $H1Open=''; $H1Close='';
        if (php_sapi_name() !== "cli") {
            $NL = '<br/>';
            $H1Open='<h1>'; $H1Close='</h1>';
        }
        $NL .= "\n";

        print($H1Open."500 Internal Server Error".$H1Close.$NL);
        print("An internal server error has been occurred.".$NL);
        print("Please try again later.".$NL);

    } // dummyMessage4User

    static public function UncaughtException(Exception $e) {

        $NL=''; $BOpen=''; $BClose=''; $PreOpen=''; $PreClose='';
        if (php_sapi_name() !== "cli") {
            $NL = '<br/>'; $BOpen='<b>'; $BClose='</b>'; $PreOpen='<pre>'; $PreClose='<pre/>';
            // there actions for browser environment
            http_response_code(500);
        }
        $NL .= "\n";

        if(ini_get('display_errors')){
            print("Uncaught exception: ".$BOpen. $e->getMessage().$BClose.$NL);
            print($PreOpen." in file ".$e->getFile()." on line ".$e->getLine().$PreClose.$NL);
            print($PreOpen.$e->getTraceAsString().$PreClose.$NL);
        } else {
            print($BOpen."Uncaught exception has been occurred.".$BClose.$NL);
            print("Please try again later.".$NL);
        }
        // komunikat o błędzie do logu - zawsze, chyba że zablokujemy w ogóle
        if(ini_get('log_errors')){
            //error_log("Uncaught exception: ".print_r($e,true));
            error_log("Uncaught exception: ".$e->getMessage()." in file ".$e->getFile()." on line ".$e->getLine()." Trace:".$e->getTraceAsString());
        }
   
        // serviced errors shouldn't be processed again
        self::errorClearLast();

    } // UncaughtException()


} // UncaughtExceptionService class

?>
