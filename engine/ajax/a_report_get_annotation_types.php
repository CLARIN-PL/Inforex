<?php
/**
 * metoda pobierajaca dostepne typy relacji dla anotacji o zadanym reports_annotations.id
 * 
 */
class Ajax_report_get_annotation_types extends CPage {
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('edit_documents') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	function execute(){
		global $mdb2, $user;
		
		$annotation_id = intval($_POST['annotation_id']);
		
		$sql =  "SELECT DISTINCT name " .
				"FROM annotation_types " .
				"WHERE group_id=(" .
					"SELECT group_id " .
					"FROM annotation_types " .
					"WHERE name=(" .
						"SELECT type " .
						"FROM reports_annotations " .
						"WHERE id={$annotation_id}" .
					")" .
				")"; 
		$result = $mdb2->query($sql)->fetchAll();
		echo json_encode($result);
	}
	
}
?>
