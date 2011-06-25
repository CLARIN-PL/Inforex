<?php
/**
 * metoda pobierajaca dostepne typy relacji dla anotacji o zadanym reports_annotations.id
 * 
 */
class Ajax_report_get_annotation_relation_types extends CPage {
		
	function execute(){
		global $mdb2, $user;

		/*if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}*/
		
		$annotation_id = intval($_POST['annotation_id']);
		
		$sql =  "SELECT id, name, description " .
				"FROM relation_types " .
				/*"WHERE annotation_set_id=(" .
					"SELECT group_id " .
					"FROM annotation_types " .
					"WHERE name=(" .
						"SELECT type " .
						"FROM reports_annotations " .
						"WHERE id={$annotation_id}" .
					")" .
				") " .*/
				"WHERE id IN (" .					
					"SELECT relation_type_id " .
					"FROM relations_groups " .
					"WHERE part='source' " .
					"AND (" .
						"annotation_set_id=(" .
							"SELECT group_id " .
							"FROM annotation_types " .
							"WHERE name=(" .
								"SELECT type " .
								"FROM reports_annotations " .
								"WHERE id={$annotation_id}" .
							")" .
						") " .
						"OR " .
						"annotation_subset_id=(" .
							"SELECT annotation_subset_id " .
							"FROM annotation_types " .
							"WHERE name=(" .
								"SELECT type " .
								"FROM reports_annotations " .
								"WHERE id={$annotation_id}" .
							")" .
						") " .
					") " .
				")"; 
		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>
