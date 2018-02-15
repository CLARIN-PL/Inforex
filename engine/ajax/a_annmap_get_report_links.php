<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annmap_get_report_links extends CPage {
	var $isSecure = false;
	function execute(){
		global $db;
		$corpusId = intval($_POST['id']);
		$annotationType = $_POST['type'];
		$annotationText = $_POST['text'];

        $filters = $_SESSION['annmap'];
        $ext_table = DbCorpus::getCorpusExtTable($corpusId);

        if ($filters['flags'] != null && $filters['flags']['flag'] != "-" && $filters['flags']['flag_status'] != "-"){
            $flag_active = true;
            $params = array(intval($filters['flags']['flag']), intval($corpusId), intval($annotation_type), intval($filters['flags']['flag_status']));
        } else{
            $flag_active = false;
        }

        if(isset($filters['metadata'])){
            $where_metadata = "";
            $sql_metadata = "";
            foreach($filters['metadata'] as $column => $metadata){
                if($metadata != "0"){
                    $where_metadata .=  " AND ext." . $column . " = '" . $metadata ."'";
                    if($sql_metadata == ""){
                        $sql_metadata = " JOIN " . $ext_table . " ext ON ext.id = r.id ";
                    }
                }
            }
        }

		$sql = "SELECT DISTINCT r.id, r.title" .
				" FROM reports_annotations ra" .
				" JOIN reports r ON ra.report_id=r.id" .
                ($flag_active ? " JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?) " : "") .
                $sql_metadata .
				" WHERE r.corpora=? AND ra.type=? AND ra.text=? " .
                $where_metadata .
                ($flag_active ? " AND rf.flag_id = ? " : "") .
				" ORDER BY r.title, r.id";
		$result = $db->fetch_rows($sql, array($corpusId, $annotationType, $annotationText));
		return $result;
	}
	
}
?>
