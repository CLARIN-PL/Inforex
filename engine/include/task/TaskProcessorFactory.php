<?php


class TaskProcessorFactory{

    function create($task){
        switch ($task->getType()){
            case "upload_zip_txt":
                return new TaskProcessorUploadZipTxt($task);
        }
    }

}