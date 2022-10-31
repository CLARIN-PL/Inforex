<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_add_annotation_relation extends CPageCorpus {
	
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
    }

    function execute(){
		global $user, $db;

        // 20221017 tymczasowy monitoring operacji dodawania relacji
        $this->debugLog(' Ajax_report_add_annotation_relation','> execute()');
        $this->debugLog(':user',$user); 

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
			return;
		}

		$relation_type_id = intval($_POST['relation_type_id']);
		$source_id = intval($_POST['source_id']);
		$target_id = intval($_POST['target_id']);
		$user_id = intval($user['user_id']);
		$working_mode = $_POST['working_mode'];
        $this->debugLog(':relation_type_id',$relation_type_id);
        $this->debugLog(':source_id',$source_id);
        $this->debugLog(':target_id',$target_id);
        $this->debugLog(':user_id',$user_id);
        $this->debugLog(':working_mode',$working_mode);

		//Insert as 'agreement' when the working mode is relation_agreement or agreement. Otherwise, insert as 'final'.
        if($working_mode != "final"){
            $working_mode = "agreement";
        }
        $this->debugLog(':working_mode after tunning',$working_mode);
		
		$sql = "SELECT * FROM relations " .
				"WHERE relation_type_id=? AND source_id=? AND target_id=? AND user_id = ? AND stage = 'final'";
        $this->debugLog(':1st sql',$sql);
		$result = $db->fetch_one($sql, array($relation_type_id, $source_id, $target_id, $user_id));
        $this->debugLog(':1st result - count',$result);

		if (count($result)==0){
			$sql = "INSERT INTO relations (relation_type_id, source_id, target_id, date, user_id, stage) " .
					"VALUES (?,?,?,now(),?,?)";
            $this->debugLog(':2nd sql',$sql);
			$db->execute($sql, array($relation_type_id, $source_id, $target_id, $user_id, $working_mode));
			$relation_id = $db->last_id();
            $this->debugLog(':2nd result - lastId',$relation_id);
		} else {
			throw new Exception("Relacja w bazie już istnieje!");
		}
		$sql = "SELECT name FROM relation_types " .
				"WHERE id=? ";
        $this->debugLog(':3rd sql',$sql);
        $result = $db->fetch_one($sql, array($relation_type_id));
        $this->debugLog(':3rd result',$result);
        $result = array("relation_id"=>$relation_id, "relation_name"=>$result);
        $this->debugLog(':method returned',$result);		
        return $result;

		//return array("relation_id"=>$relation_id, "relation_name"=>$db->fetch_one($sql, array($relation_type_id)));
	}
	
}
