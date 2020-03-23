<?php

abstract class ATaskProcessor{

    var $task;

    function __construct($task){
        $this->task = $task;
    }

    function run(){
//        $this->task->setStatus("process");
//        $this->task->update();
        $this->process();
//        $this->task->setStatus("done");
//        $this->task->update();
    }

    function getParameters(){
        $params = $this->task->getParameters();
        return json_decode($params, true);
    }

    abstract function process();

    function log($type, $message){
        echo sprintf("[%s] %s: %s\n", date("Ymd H:i:s"), $type, $message);
    }

    function info($message){
        $this->log("INFO", $message);
    }

    function error($message){
        $this->log("ERROR", $message);
    }

}