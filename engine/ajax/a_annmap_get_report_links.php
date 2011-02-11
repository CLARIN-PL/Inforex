<?php
class Ajax_annmap_get_report_links extends CPage {
	
	function execute(){
		global $mdb2;
		$corpusId = intval($_POST['id']);
		$annotationType = $_POST['type'];
		$annotationText = $_POST['text'];
		
		/*$mdb2->query("UPDATE reports SET type=$type WHERE id=$id");			
		if (PEAR::isError($r = $mdb2->query("SELECT name FROM reports_types WHERE id=$type")))
			die("<pre>{$r->getUserInfo()}</pre>");
		$type_name = $r->fetchOne();		
		$type_name = "tralala";			*/
		echo json_encode(array("success"=>"1", "corpusId"=>$corpusId, annotationType=>$annotationType, annotationText=>$annotationText));
	}
	
}
?>
