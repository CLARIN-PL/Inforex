<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbAnnotationType{

	/**
	 * Returns a map of annotation types which belong to given set.
     * The key is annotation type name, and the value is annotation type id.
     * I.e. array("nam_loc" => 4, "nam_liv" => 5)
	 * @param int $corpu_id 
	 * @return An array of annotation schemas.
	 */
	static function getAnnotationTypesForSetAsNameToIdMap($annotationSetId){
		global $db;		
		$sql = "SELECT t.annotation_type_id, t.name FROM annotation_types t WHERE t.group_id = ?";
		$items = $db->fetch_rows($sql, array($annotationSetId));
		$types = array();
		foreach ($items as $item){
		    $types[$item['name']] = $item['annotation_type_id'];
        }
        return $types;
	}

}