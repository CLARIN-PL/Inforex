<?php
class DBReportPerspective{
	
	static function get_corpus_perspectives($corpus_id, $user){
		$sql = "SELECT * FROM `corpus_and_report_perspectives` c JOIN `report_perspectives` p ON (c.perspective_id = p.id) WHERE `corpus_id`=? AND `access` = 'public'";
		if ( isset($user['role']['loggedin']) )
			$sql = "SELECT * FROM `corpus_and_report_perspectives` c JOIN `report_perspectives` p ON (c.perspective_id = p.id) WHERE `corpus_id`=? AND (`access` != 'role')";
		$rows = db_fetch_class_rows("ReportPerspective", $sql, array($corpus_id));
		return $rows;
	}
}
?>
