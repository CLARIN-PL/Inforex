<?php
class Ajax_relation_get_relation_statistic extends CPage {
	var $isSecure = false;
	function execute(){
		global $db, $corpus;
		$cid = intval($_POST['corpus_id']);
		$rel_type = $_POST['relation_type'];
		$limit_from = intval($_POST['limit_from']);
		$limit_to = intval($_POST['limit_to']);
		$document_id = intval($_POST['document_id']);
		$relation_set_id = intval($_POST['relation_set_id']);
		
		$result = DbCorpusRelation::getRelationList($cid, $rel_type, $relation_set_id, $limit_to, $limit_from, $document_id);
		echo json_encode($result);
	}	
}
?>


