<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_update_relations_type extends CPage {
	var $isSecure = false;
	function execute(){
		global $db;
		$relation_id = intval($_POST['relation_id']);
		$relation_type = intval($_POST['relation_type']);
		
		$sql = "UPDATE `relations` SET `relation_type_id` = ? WHERE `relations`.`id` = ?";
		$db->execute($sql, array($relation_type, $relation_id));		
		
		return;
	}	
}
?>