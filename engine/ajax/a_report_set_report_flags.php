<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_set_report_flags extends CPage {
	
	/*function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak praw <small>[checkPermission]</small>.";
	}*/
	
	function execute(){
		global $db, $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$report_id = intval($_POST['report_id']);
		$cflag_id = intval($_POST['cflag_id']);
                $flag_id = intval($_POST['flag_id']);
                $user_id = $_SESSION['_authsession']['data']['user_id'];
                
                $params = array('corpora_flag_id' => $cflag_id, 
                                'report_id'       => $report_id,
                                'flag_id'         =>$flag_id);
               
                
                //Masowa zmiana statusu flagi 
                if(isset($_POST['multiple']) === true){
                    $corpus_id = ($_POST['corpus_id']);
                    
                    $sqlSelect = "SELECT uc.report_id as id FROM users_checkboxes uc
                                  JOIN reports r ON uc.report_id = r.id 
                                  WHERE (r.corpora = ".$corpus_id." AND uc.user_id = ".$user_id.")";
                    $records = db_fetch_rows($sqlSelect);
                    
                    
                    foreach($records as $record){
                        $document_ids[] = $record['id'];
                    }
                    
                    
                    if(empty($document_ids)){
                        return;
                    }
                    
                    foreach($document_ids as $document){
                        $params['report_id'] = $document;
                        $db->replace("reports_flags", $params);
                    }
             
                }
                //Zmiana jednej flagi
                else {
                    if ($flag_id){		
                        $db->replace("reports_flags", $params);
                    }
                    else {
                            $sql = "DELETE FROM reports_flags WHERE corpora_flag_id={$cflag_id} AND report_id={$report_id}";
                            $result = db_execute($sql);
                    }
                }

		return;
	}
	
}
?>
