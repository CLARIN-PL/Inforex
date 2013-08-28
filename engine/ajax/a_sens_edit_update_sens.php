<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_update_sens extends CPage {
	function execute(){
		global $db;
		$name = $_POST['name'];
		$description = $_POST['description'];
		$sens_name = $_POST['sens_name'];
		
		$sql = " UPDATE annotation_types_attributes_enum SET description='" . $description . "' WHERE value='" . $sens_name . "' ";
		$db->execute($sql);	
		return;
	}	
}
?>