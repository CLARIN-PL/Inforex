<?
/**
 * 
 */
class DbStatus{

	static function getAll(){
		global $db;
		$sql = "SELECT * FROM reports_statuses ORDER BY id ASC";
		return $db->fetch_rows($sql);
	}
	
}

?>