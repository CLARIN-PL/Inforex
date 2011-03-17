<?php
class Ajax_annmap_get_report_links extends CPage {
	var $isSecure = false;
	function execute(){
		//sleep(1);
		global $mdb2;
		$corpusId = intval($_POST['id']);
		$annotationType = $_POST['type'];
		$annotationText = $_POST['text'];
		
		$sql = "SELECT DISTINCT r.id, r.title" .
				" FROM reports_annotations ra" .
				" JOIN reports r ON ra.report_id=r.id" .
				" WHERE r.corpora={$corpusId} AND ra.type=\"{$annotationType}\" AND ra.text=\"{$annotationText}\"" .
				" ORDER BY r.title, r.id";
		$result = db_fetch_rows($sql);
		
		/*$mdb2->query("UPDATE reports SET type=$type WHERE id=$id");			
		if (PEAR::isError($r = $mdb2->query("SELECT name FROM reports_types WHERE id=$type")))
			die("<pre>{$r->getUserInfo()}</pre>");
		$type_name = $r->fetchOne();		
		$type_name = "tralala";			*/
		//echo json_encode(array("success"=>"1", "corpusId"=>$corpusId, annotationType=>$annotationType, annotationText=>$annotationText));
		echo json_encode($result);
	}
	
}
?>
