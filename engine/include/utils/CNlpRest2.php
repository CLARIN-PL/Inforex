<?php
class NlpRest2{

    var $url;
    var $task;
    var $user;
    var $verbose=true;

    function __construct($task, $url="http://ws.clarin-pl.eu/nlprest2/base", $user="Inforex"){
        $this->url = $url;
        $this->task = $task;
        $this->user = $user;
    }

    function processSync($text){
        $docId = $this->upload($text);
        $taskId = $this->startTask($docId);
        for ($i=0; $i<1000; $i++){
            sleep(0.1);
            $status = $this->getStatus($taskId);
            $this->log(json_encode($status));
            if ($status['status'] == 'DONE'){
                $result = $this->downloadTaskResult($taskId);
                return $result;
            }
        }
    }

    function upload($text){
        $options = array(
            'http' => array(
                'header'  => "Content-Type: text/plain\r\n",
                'method'  => 'POST',
                'content' => $text
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($this->url . '/upload/', false, $context);
        if ($result === FALSE) {

        } else {
            return $result;
        }
    }

    function getStatus($taskId){
        $url = $this->url . "/getStatus/" . $taskId;
        $result = file_get_contents($url);
        if ($result === FALSE) {

        } else {
            return json_decode($result, true);
        }
    }

    function startTask($fileId){
        $url = $this->url . "/startTask";
        $data = array("file"=>$fileId, "lpmn"=>$this->task, "user"=>$this->user);
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {

        } else {
            return $result;
        }
    }

    function downloadTaskResult($taskId){
        $status = $this->getStatus($taskId);
        $fileId = $status['value'][0]['fileID'];
        return $this->download($fileId);
    }

    function download($fileId){
        $url = $this->url . "/download" . $fileId;
        $result = file_get_contents($url);
        if ($result === FALSE) {

        } else {
            return $result;
        }
    }

    function log($msg){
        if ( $this->verbose ){
            echo "Logger: $msg\n";
        }
    }
}