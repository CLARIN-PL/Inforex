<?php
class Ajax_report_get_relation_statistic extends CPage {
	var $isSecure = false;
	function execute(){
		global $db, $corpus;
		$cid = intval($_POST['corpus_id']);
		$rel_set = intval($_POST['relation_set_id']);
		$limit_from = intval($_POST['limit_from']);
		$limit_to = intval($_POST['limit_to']);
		//SELECT rep.id AS document_id, cor.name AS subcorpus_name, an_sou.text AS source_text, an_sou.type AS source_type, an_tar.text AS target_text, an_tar.type AS target_type FROM relation_sets rs LEFT JOIN relation_types rty ON (rs.relation_set_id=rty.relation_set_id) LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) LEFT JOIN reports_annotations an_sou ON (rel.source_id=an_sou.id) LEFT JOIN reports_annotations an_tar ON (rel.target_id=an_tar.id) LEFT JOIN reports rep ON (rep.id=an_sou.report_id) LEFT JOIN corpora cor ON (cor.id=rep.subcorpus_id) WHERE rep.corpora=? AND rs.relation_set_id=? LIMIT ?, ?;
  		$sql = "SELECT rep.id AS document_id, cor.name AS subcorpus_name, an_sou.text AS source_text, an_sou.type AS source_type, an_tar.text AS target_text, an_tar.type AS target_type " .
  				"FROM relation_sets rs " .
  				"LEFT JOIN relation_types rty ON (rs.relation_set_id=rty.relation_set_id) " .
  				"LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) " .
  				"LEFT JOIN reports_annotations an_sou ON (rel.source_id=an_sou.id) " .
  				"LEFT JOIN reports_annotations an_tar ON (rel.target_id=an_tar.id) " .
  				"LEFT JOIN reports rep ON (rep.id=an_sou.report_id) " .
  				"LEFT JOIN corpora cor ON (cor.id=rep.subcorpus_id) " .
  				"WHERE rep.corpora=? " .
  				"AND rs.relation_set_id=? " .
  				"LIMIT ?, ?;";	
  		
  		$result = $db->fetch_rows($sql, array($cid, $rel_set, $limit_from, $limit_to));
		echo json_encode($result);
	}	
}
?>


