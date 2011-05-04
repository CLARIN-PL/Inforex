<?php
class DBReportPerspective{
	
	static function get_corpus_perspectives($corpus_id, $user){
		$sql = "SELECT * FROM `corpus_and_report_perspectives` c JOIN `report_perspectives` p ON (c.perspective_id = p.id) WHERE `corpus_id`=? AND `access` = 'public'";
		$arr = array($corpus_id);
		if ( isset($user['role']['loggedin']) ){
			$arr = array($corpus_id, $corpus_id, $user['user_id']);
			//$sql = "SELECT * FROM `corpus_and_report_perspectives` c JOIN `report_perspectives` p ON (c.perspective_id = p.id) WHERE `corpus_id`=? AND (`access` != 'role')";
			$sql = "SELECT p.*, " .
					"c.corpus_id " .
					"FROM `corpus_and_report_perspectives` c " .
					"JOIN `report_perspectives` p " .
						"ON (c.perspective_id = p.id) " .
						"WHERE `corpus_id`=? " .
						"AND (`access` != 'role') " .
					"UNION " .
					"SELECT p.*, c.corpus_id " .
					"FROM corpus_perspective_roles c " .
					"JOIN report_perspectives p " .
						"ON (c.report_perspective_id=p.id) " .
						"WHERE corpus_id=? " .
						"AND user_id=?";
		}
		$rows = db_fetch_class_rows("ReportPerspective", $sql, $arr);
		//var_dump($rows);
		return $rows;
	}
}
?>
