<?php

require_once "../../engine/include/utils/CNlpRest2.php";

$user="marcinczuk@gmail.com";
$task="morphoDita";
$url="http://ws.clarin-pl.eu/nlprest2/base";
$text = "Ala ma kota, a kot siedzi na drzewie";

$nlp = new NlpRest2($task, $url, $user);

echo "Uploading text...\n";
$docId = $nlp->upload($text);
echo "File id = $docId \n";
echo "Executing task...\n";
$taskId = $nlp->startTask($docId);
echo "Task id = $taskId \n";
for ($i=0; $i<10; $i++){
    $status = $nlp->getStatus($taskId);
    echo "Status: " . json_encode($status) . "\n";
    sleep(1);
    if ($status['status'] == 'DONE'){
        $result = $nlp->downloadTaskResult($taskId);
        echo "Result\n";
        echo $result;
        echo "\n\n";
        break;
    }
}


echo $nlp->processSync($text);