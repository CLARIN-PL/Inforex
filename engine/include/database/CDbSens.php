<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbSens{
	
	static function getSenseList($fields=null){
		global $db;
		$sql = "SELECT " .
				(!is_null($fields) ? $fields : " ata.*, at.name AS 'annotation_name' " ) .
				" FROM annotation_types_attributes ata 
				  JOIN annotation_types at ON ata.annotation_type_id = at.annotation_type_id " .
				" ORDER BY ata.annotation_type_id;";

        $senseList = $db->fetch_rows($sql);
		return $senseList;
	}
	
	static function getSensDataById($sens_id, $fields=null){
		global $db;
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM annotation_types_attributes_enum " .
				" WHERE annotation_type_attribute_id=? ";

		return $db->fetch_rows($sql,array($sens_id));
	}
	
}
?>