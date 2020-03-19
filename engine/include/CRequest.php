<?php
class CRequest{

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
            throw new Exception("Missing parameter in the request: $name");
        }
    }

}