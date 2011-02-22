<?php
/**
 * metoda pobierajaca wszystkie relacje (bez rozróżniania typów) pomiedzy jednostkami dla raportu report_id
 * 
 */
class Ajax_report_get_relations extends CPage {
	var $isSecure = false;
	function execute(){
		global $mdb2, $user;

		$report_id = intval($_POST['report_id']);

		$sql = 	"SELECT DISTINCT source_id, target_id " .
				"FROM relations " .
				"WHERE source_id " .
				"IN " .
					"(SELECT id " .
					"FROM reports_annotations " .
					"WHERE report_id={$report_id})"; 

		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>
