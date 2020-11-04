<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_delete_word extends CPage {
	function execute(){
		$name = $_POST['name'];
		$wsd_name = "wsd_" . $name;
		$id = $_POST['id'];

        $sql = " SELECT annotation_type_id FROM annotation_types_attributes WHERE id = ?";
        $annotation_type_id = $this->getDb()->fetch_one($sql, array($id));
		
		$sql = "SELECT * FROM reports_annotations WHERE type_id=? ";
		$result = $this->getDb()->fetch_rows($sql, array($annotation_type_id));
		
		if(count($result)){
			$error_msg = 'Word ' . $name . ' have ' . count($result) . ' annotations';
			throw new Exception($error_msg);
			return;
		}
		
		
		$sql = "DELETE FROM annotation_types WHERE annotation_type_id=? ";
		$this->getDb()->execute($sql, array($annotation_type_id));
		$sql = "DELETE FROM annotation_types_attributes WHERE id=? ";
		$this->getDb()->execute($sql, array($id));
		return;
	}	
}
