<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_enum_add extends CPageAdministration {


    function execute(){
		global $db;

		$shared_attribute_id = intval($_POST['shared_attribute_id']);
		$value_str = strval($_POST['value_str']);
		$desc_str = strval($_POST['desc_str']);
		
		$sql = "INSERT INTO shared_attributes_enum (shared_attribute_id, value, description) " .
			"VALUES (?, ?, ?)";
		$db->execute($sql, array($shared_attribute_id, $value_str, $desc_str));
		return;
	}
	
}