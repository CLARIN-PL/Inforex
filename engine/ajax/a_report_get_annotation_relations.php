<?php
/**
 * metoda pobierajaca relacje dla anotacji o zadanym source_id z tabeli relations
 * 
 */
class Ajax_report_get_annotation_relations extends CPage {
		
	function execute(){
		global $mdb2, $user;

		$annotation_id = intval($_POST['annotation_id']);
		
		$sql =  "SELECT rr.id, relation_types.name, rr.target_id, reports_annotations.text, reports_annotations.type " .
				"FROM ((SELECT * FROM relations WHERE source_id={$annotation_id}) rr " .
				"JOIN relation_types  ON rr.relation_type_id=relation_types.id) " .
				"JOIN reports_annotations ON rr.target_id=reports_annotations.id";

		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>
