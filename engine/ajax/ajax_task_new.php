<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_task_new extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);
        $this->anyCorpusRole[] = CORPUS_ROLE_TASKS;
    }

    function execute(){
		global $corpus, $db, $user;
		
		$taskDescription = strval($_POST['task']);
		$documents = strval($_POST['documents']);
		$flag = $_POST['flag'];
		$status = $_POST['status'];
		$count = 0;
		$documentId = $this->getRequestParameter('document_id', null);
		$corpusId = $corpus['id'];

		if( $documentId == null ){
            $count = $this->getDocumentCount($corpusId, $documents, $flag, $status);
        } else{
		    $count = 1;
        }

		list($task, $params) = $this->parseTask($taskDescription);
		
		$data = array();
		$data['user_id'] = $user['user_id'];
		$data['corpus_id'] = $corpus['id'];
		$data['type'] = $task;
		$data['parameters'] = json_encode($params);
		$data['max_steps'] = $count;
		$data['current_step'] = 0;


		$db->insert("tasks", $data);
		$task_id = $db->last_id();

		if ( $count > 0 ){
            if( $documentId == null ) {
                $this->insertDocumentsToTask($corpusId, $documents, $flag, $status, $task_id);
            } else {
                $db->insert_bulk("tasks_reports", array("task_id", "report_id"), array(array($documentId, $task_id)));
            }
		}

		return array("task_id"=>$task_id, "document_count"=>$count);
	}


	function parseTask($taskDescription){
		$taskName = null;
		$taskParams = array();

		switch ($taskDescription){
			case "nlprest2-morphodita":
				$taskName = "nlprest2-tagger";
				$taskParams["nlprest2_task"] = "morphoDita";
				$taskParams["nlprest2_params"] = array("guesser"=>"false", "allforms"=>"true", "model"=>"XXI");
                $taskParams["tagset_id"] = 1;
				break;
			case "nlprest2-wcrft2-morfeusz1":
                $taskName = "nlprest2-tagger";
                $taskParams["nlprest2_task"] = "wcrft2";
                $taskParams["nlprest2_params"] = array("guesser"=>"false", "allforms"=>"true", "morfeusz2"=>"false");
                $taskParams["tagset_id"] = 1;
				break;
            case "nlprest2-wcrft2-morfeusz2":
                $taskName = "nlprest2-tagger";
                $taskParams["nlprest2_task"] = "wcrft2";
                $taskParams["nlprest2_params"] = array("guesser"=>"false", "allforms"=>"true", "morfeusz2"=>"true");
                $taskParams["tagset_id"] = 1;
                break;
            case "nlprest2-en":
                $taskName = "nlprest2-tagger";
                $taskParams["nlprest2_task"] = "spacy";
                $taskParams["nlprest2_params"] = array("lang"=>"en");
                $taskParams["tagset_id"] = 2;
                break;
            case "nlprest2-de":
                $taskName = "nlprest2-tagger";
                $taskParams["nlprest2_task"] = "spacy";
                $taskParams["nlprest2_params"] = array("lang"=>"de");
                $taskParams["tagset_id"] = 3;
                break;
            case "nlprest2-he":
                $taskName = "nlprest2-tagger";
                $taskParams["nlprest2_task"] = "tagger";
                $taskParams["nlprest2_params"] = array("lang"=>"hebrew");
                $taskParams["tagset_id"] = 4;
                break;
            case "nlprest2-ru":
                $taskName = "nlprest2-tagger";
                $taskParams["nlprest2_task"] = "tagger";
                $taskParams["nlprest2_params"] = array("lang"=>"russian");
                $taskParams["tagset_id"] = 5;
                break;
            case "nlprest2-cs":
                $taskName = "nlprest2-tagger";
                $taskParams["nlprest2_task"] = "tagger";
                $taskParams["nlprest2_params"] = array("lang"=>"czech");
                $taskParams["tagset_id"] = 6;
                break;
            case "nlprest2-bg":
                $taskName = "nlprest2-tagger";
                $taskParams["nlprest2_task"] = "tagger";
                $taskParams["nlprest2_params"] = array("lang"=>"bulgarian");
                $taskParams["tagset_id"] = 7;
                break;
			default:
                $parts = explode(":", $taskDescription);
                for ($i=1; $i<count($parts); $i++){
                    $kv = explode("=", $parts[$i]);
                    if (count($kv)==2){
                        $taskParams[$kv[0]] = $kv[1];
                    }
                }
                $taskName = $parts[0];
        }
		return array($taskName, $taskParams);
	}
	
	/**
	 * Create a list of documents on which the task will be performed.
	 */
	function getDocuments($corpus_id, $documents, $flag, $status){
		global $db;

		if ( $documents == "all" ){
			$sql = "SELECT id FROM reports WHERE corpora = ?";
			$docs = $db->fetch_ones($sql, "id", array($corpus_id));
		}else{
            $sql = "SELECT r.id FROM reports_flags rf JOIN reports r ON r.id = rf.report_id WHERE (r.corpora = ? AND rf.corpora_flag_id = ? AND rf.flag_id = ?)";
            $docs = $db->fetch_ones($sql, "id",  array($corpus_id, $flag, $status));
		}

		return $docs;
	}

    function getDocumentCount($corpus_id, $documents, $flag, $status){
        global $db;

        if ( $documents == "all" ){
            $sql = "SELECT COUNT(id) FROM reports WHERE corpora = ?";
            $docs = $db->fetch_one($sql, array($corpus_id));
        }else{
            $sql = "SELECT COUNT(r.id) FROM reports_flags rf JOIN reports r ON r.id = rf.report_id WHERE (r.corpora = ? AND rf.corpora_flag_id = ? AND rf.flag_id = ?)";
            $docs = $db->fetch_one($sql, array($corpus_id, $flag, $status));
        }

        return $docs;
    }

    function insertDocumentsToTask($corpus_id, $documents, $flag, $status, $task_id){
        global $db;
        if ( $documents == "all" ){
            $sql = "SELECT id, $task_id FROM reports WHERE corpora = ?";
            $params = array($corpus_id);
        }else{
            $sql = "SELECT r.id, $task_id FROM reports_flags rf JOIN reports r ON r.id = rf.report_id WHERE (r.corpora = ? AND rf.corpora_flag_id = ? AND rf.flag_id = ?)";
            $params = array($corpus_id, $flag, $status);
        }
        $sql = "INSERT INTO tasks_reports (report_id, task_id) $sql";
        ChromePhp::log($sql);
        $db->execute($sql, $params);
    }
}
