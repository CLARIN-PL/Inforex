<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_delete extends CPageAdministration {
	
	function execute(){
		global $db;
		$shared_attribute_id = intval($_POST['shared_attribute_id']);
		$sql = "DELETE FROM shared_attributes WHERE id=?";
		$db->execute($sql, array($shared_attribute_id));
		return;
	}
	
}