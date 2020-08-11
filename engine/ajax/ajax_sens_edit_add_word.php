<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_add_word extends CPage {
	function execute(){
		$name = $_POST['wordname'];
		$wsd_name = "wsd_" . $name;
		
		$sql = "INSERT INTO annotation_types (name, description, group_id, annotation_subset_id) VALUES (?, '', 2, 21)";
			
		$this->getDb()->execute($sql, array($wsd_name));

		$annotation_type_id = $this->getDb()->last_id();
		
		$error = $this->getDb()->errorInfo();
		if(isset($error[0])){
			$error_msg = 'Word ' . $name . ' alredy exist';
			throw new Exception($error_msg);
			return;
		}
		
			
		$sql = "INSERT INTO annotation_types_attributes (annotation_type_id, name, type) VALUES (?, 'sense', 'radio')";
		$this->getDb()->execute($sql, array($annotation_type_id));
		
		$rows_id = $this->getDb()->last_id();
		return array("rows_id" => $rows_id);
	}	
}
