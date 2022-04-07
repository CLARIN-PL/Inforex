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
            print("<h1>500 Internal Server Error</h1><br/>\n");
        }
        $NL .= "\n";

        print("An internal server error has been occurred.".$NL);
        print("Please try again later.".$NL);

    } // dummyMessage4User

    static private function exceptionMsgCLIStr(Exception $e) {

        $msg = "Uncaught exception: ".$e->getMessage()."\n"
               ." in file ".$e->getFile()." on line ".$e->getLine()."\n"
               .$e->getTraceAsString()."\n";
        return $msg;

    } // exceptionMsgCLIStr()

    static private function exceptionMsgHTMLStr(Exception $e) {

        $msg = "Uncaught exception: <b>".$e->getMessage()."</b>\n"
            ."<pre> in file ".$e->getFile()." on line ".$e->getLine()."</pre>\n"
            ."<pre>".$e->getTraceAsString()."</pre>\n";
        return $msg;

    } // exceptionMsgHTMLStr()

    static private function exceptionMessage(Exception $e) {

        if(php_sapi_name() == 'cli') {
            print(self::exceptionMsgCLIStr($e));
        } else {
            // there actions for browser environment only
            http_response_code(500);
            print(self::exceptionMsgHTMLStr($e));
        }

    } // exceptionMessage()

    static public function UncaughtException(Exception $e) {

        if(ini_get('display_errors')){
            self::exceptionMessage($e);
        } else {
            self::dummyMessage4User(); 
        }

        // logging information to logfile always, if not disallow
        if(ini_get('log_errors')){
            error_log("Uncaught exception: ".$e->getMessage()." in file ".$e->getFile()." on line ".$e->getLine()." Trace:".$e->getTraceAsString(),0);
        }
   
        // serviced errors shouldn't be processed again
        self::errorClearLast();

    } // UncaughtException()


} // UncaughtExceptionService class

?>
