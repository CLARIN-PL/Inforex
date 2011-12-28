<?php
class Ajax_relation_get_relation_statistic extends CPage {
	var $isSecure = false;
	function execute(){
		global $db, $corpus;
		$cid = intval($_POST['corpus_id']);
		$rel_set = intval($_POST['relation_set_id']);
		$limit_from = intval($_POST['limit_from']);
		$limit_to = intval($_POST['limit_to']);
		$document_id = intval($_POST['document_id']);
		
		$result = DbCorpusRelation::getRelationList($cid, $rel_set, $limit_to, $document_id, $limit_from);
		echo json_encode($result);
	}	
}
?>


