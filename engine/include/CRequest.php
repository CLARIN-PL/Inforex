<?php
class CRequest{

    /**
     * @return Database
     */
        function getDb(){
            // ToDo: the reference to the database gateway should be passed through the constructor.
            global $db;
            return $db;
    }

    function getRequestParameter($name, $default=""){
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }

    function getRequestParameterBoolean($name){
        return isset($_REQUEST[$name]) && $_REQUEST[$name];
    }

    function getRequestParameterRequired($name){
        if ( isset($_REQUEST[$name]) ) {
            return  $_REQUEST[$name];
        } else {
            throw new CRequestException("Missing parameter in the request: $name");
        }
    }

}
