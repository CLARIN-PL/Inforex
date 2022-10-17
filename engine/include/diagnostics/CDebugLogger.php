<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DebugLogger {

    private static $logId = null; // unique code for identify one session log
    private static $randomIdSize = 3; // should be const 

    static private function checkID() {

        if(! self::$logId ) {
            self::$logId = substr(md5(microtime()),rand(0,26),
                                self::$randomIdSize);
        }

    } // checkID()

    static public function logVariable($name,$value){

        self::checkID(); // set or check ID
        self::sendMsgLine(self::logPrefix()
                .$name." = ".var_export($value,true));

    } // logVariable()

    static public function logVariableAsJSON($name,$value){

        self::checkID(); // set or check ID
        $valStr = json_encode($value);
        self::sendMsgLine(self::logPrefix()
                .$name." = ".$valStr);

    } // logVariableAsJSON()

    private function logPrefix() {

        return "DEBUG_".self::$logId.":";

    } // logPrefix()

    private function sendMsgLine($msg) {

        // send message to default error log
        error_log($msg);

    } // sendMsgLine()

    static public function logGETVariableAsJSON($variable = null) {

        self::dynamicVar("GET:",$_GET,$variable);

    } // logGETVariableAsJSON()

    static public function logPOSTVariableAsJSON($variable = null) {

        self::dynamicVar("POST:",$_POST,$variable);

    } // logPOSTVariableAsJSON()

    static public function logCookieVariableAsJSON($variable = null) {

        self::dynamicVar("COOKIE:",$_COOKIE,$variable);

    } // logCookieVariableAsJSON()

    static private function dynamicVar($name,$dynVar,$variable=null) {

        self::checkID(); // set or check ID
        if($variable) {
            if(isset($dynVar[$variable])) {
                self::logVariableAsJSON($name
                    .$variable,$dynVar[$variable]);
            } else {
                self::logVariableAsJSON($name.$variable,"<< NOT SET >>");
            }
        } else {
            // if $variable == null log all existing variables
            self::logVariableAsJSON($name,$dynVar);
        }

    } // dynamicVar()

    static public function logAllDynamicVariables() {

        self::checkID(); // set or check ID
        self::logGetVariableAsJSON();
        self::logPostVariableAsJSON();
        self::logCookieVariableAsJSON();

    } // logAllDynamicVariables

} // DebugLogger class

?>
