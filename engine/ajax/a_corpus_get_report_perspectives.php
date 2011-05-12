<?php
class Ajax_corpus_get_report_perspectives extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasRole('corpus_owner'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}
		$corpusId = $_POST['corpus_id'];
		
		/*$sql = "SELECT annotation_sets.annotation_set_id AS id, " .
				"annotation_sets.description, " .
				"annotation_sets_corpora.corpus_id AS cid, " .
				"count(reports_annotations.id) as count_ann " .
				"FROM annotation_sets " .
				"LEFT JOIN annotation_sets_corpora " .
					"ON annotation_sets.annotation_set_id=annotation_sets_corpora.annotation_set_id " .
					"AND annotation_sets_corpora.corpus_id=$corpusId " .
				"LEFT JOIN annotation_types " .
					"ON annotation_sets.annotation_set_id=annotation_types.group_id " .
				"LEFT JOIN reports_annotations " .
					"ON annotation_types.name=reports_annotations.type " .
					"AND reports_annotations.report_id IN " .
						"(SELECT id " .
						"FROM reports " .
						"WHERE corpora=$corpusId) " .
				"GROUP BY annotation_sets.annotation_set_id";*/
		$sql = "SELECT report_perspectives.id, " .
				"report_perspectives.title, " .
				"report_perspectives.order, " .
				"corpus_and_report_perspectives.access, " .
				"corpus_and_report_perspectives.corpus_id AS cid " .
				"FROM report_perspectives " .
				"LEFT JOIN corpus_and_report_perspectives " .
					"ON report_perspectives.id=corpus_and_report_perspectives.perspective_id " .
					"AND corpus_and_report_perspectives.corpus_id=$corpusId";		
		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>
