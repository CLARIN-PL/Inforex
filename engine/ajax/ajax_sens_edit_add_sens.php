<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_add_sens extends CPage {
	function execute(){
		$name = $_POST['sensname'];
		$num = $_POST['sensnum'];
		$id = $_POST['sensid'];
		$description = $_POST['description'];
		$name_num = $name . '-' . $num;
		
		$sql = " SELECT * FROM annotation_types_attributes_enum WHERE value=? ";
		
		$result = $this->getDb()->fetch_one($sql, array($name_num));
		
		if(count($result)){
			$error_msg = 'Sens ' . $name_num . ' alredy exist';
			throw new Exception($error_msg);
			return;
		}

		$sql = "INSERT INTO annotation_types_attributes_enum (annotation_type_attribute_id, value, description) VALUES (?, ?, ?)";
		$this->getDb()->execute($sql, array($id,$name_num, $description));
		return;
	}	
}
