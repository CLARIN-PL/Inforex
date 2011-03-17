<?php
/**
 * 
 */
class Ajax_report_update_annotation_wsd extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji <small>[checkPermission]</small>.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}

		$annotation_id = intval($_POST['annotation_id']);
		$value = strval($_POST['value']);

		$sql_select = "SELECT ata.id" .
				" FROM annotation_types_attributes ata" .
				" JOIN reports_annotations an ON (an.type = ata.annotation_type)" .
				" WHERE an.id = ?" .
				"  AND ata.name = 'sense'";
		$attribute_id = db_fetch_one($sql_select, array($annotation_id));
		
		$sql_replace = "REPLACE reports_annotations_attributes" .
				" SET annotation_id = ?, annotation_attribute_id = ?, value = ?";
		db_execute($sql_replace, array($annotation_id, $attribute_id, $value));
		
		$json = array("success"=>1);		
		echo json_encode($json);
	}
	
}
?>
