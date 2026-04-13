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
                $db->insert("tasks_reports", array("task_id" => $task_id, "report_id" => $documentId));
            }
		}

		return array("task_id"=>$task_id, "document_count"=>$count);
	}


	function parseTask($taskDescription){
		$taskName = null;
		$taskParams = array();

        switch ($taskDescription){
            case "lpmn-postagger":
                $taskName = "lpmn-postagger";
                $taggerType = strtolower($this->getRequestParameter('tagger', 'morphodita'));
                $language = strtolower($this->getRequestParameter('language', 'pl'));
                $tagset = strtolower($this->getRequestParameter('tagset', $taggerType === 'spacy' ? 'ud' : 'nkjp'));

                $this->validateLpmnPostagger($taggerType, $language, $tagset);

                $taskParams["tagger_type"] = $taggerType;
                $taskParams["language"] = $language;
                $taskParams["tagset"] = $tagset;
                $taskParams["tagset_id"] = $this->getOrCreateTagsetId($tagset);
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

	    function validateLpmnPostagger($taggerType, $language, $tagset){
	        if (!in_array($taggerType, array('morphodita', 'ptag', 'archeopteryx', 'llm-pos-tagger', 'spacy'), true)) {
	            throw new Exception("Unsupported LPMN tagger type: " . $taggerType);
	        }

	        if ($taggerType === 'morphodita') {
	            if ($language !== 'pl') {
	                throw new Exception("MorphoDita supports only the pl language");
	            }
	            if (!in_array($tagset, array('nkjp', 'sgjp'), true)) {
	                throw new Exception("MorphoDita supports only these tagsets: nkjp,sgjp");
	            }
	        }

	        if ($taggerType === 'ptag') {
	            if ($language !== 'pl') {
	                throw new Exception("PTag supports only the pl language");
	            }
	            if ($tagset !== 'nkjp') {
	                throw new Exception("PTag supports only the nkjp tagset");
	            }
	        }

	        if ($taggerType === 'archeopteryx') {
	            if ($language !== 'pl') {
	                throw new Exception("Archeopteryx supports only the pl language");
	            }
	            if ($tagset !== 'nkjp') {
	                throw new Exception("Archeopteryx supports only the nkjp tagset");
	            }
	        }

	        if ($taggerType === 'llm-pos-tagger') {
	            if ($language !== 'pl') {
	                throw new Exception("LLM POS Tagger supports only the pl language");
	            }
	            if ($tagset !== 'nkjp') {
	                throw new Exception("LLM POS Tagger supports only the nkjp tagset");
	            }
	        }

	        if ($taggerType === 'spacy') {
            if (!in_array($language, array('en', 'de', 'pl', 'ru', 'pt', 'fr', 'es'), true)) {
                throw new Exception("spaCy supports only these languages: en,de,pl,ru,pt,fr,es");
            }
            if ($tagset !== 'ud') {
                throw new Exception("spaCy supports only the ud tagset");
            }
        }
    }

    function getOrCreateTagsetId($tagset){
        global $db;

        $tagsetId = DbTagset::getTagsetId($tagset);
        if ($tagsetId) {
            return $tagsetId;
        }

        if ($tagset !== 'ud') {
            throw new Exception("Tagset '" . $tagset . "' not found");
        }

        $db->execute("INSERT INTO tagsets (name) VALUES (?)", array($tagset));

        return DbTagset::getTagsetId($tagset);
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
