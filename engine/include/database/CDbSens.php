<?

class DbSens{
	
	static function getSensList($fields=null){
		global $db;
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM annotation_types_attributes " .
				" ORDER BY annotation_type";

		return $db->fetch_rows($sql);
	}
	
	static function getSensDataById($sens_id,$fields=null){
		global $db;
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM annotation_types_attributes_enum " .
				" WHERE annotation_type_attribute_id=? ";

		return $db->fetch_rows($sql,array($sens_id));
	}
	
}
?>