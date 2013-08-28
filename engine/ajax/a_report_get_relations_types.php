<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_get_relations_types extends CPage {
	var $isSecure = false;
	function execute(){
		global $db;
		$relation_id = intval($_POST['relation_id']);
		$sourcegroupid = intval($_POST['sourcegroupid']);
		$sourcesubgroupid = intval($_POST['sourcesubgroupid']);
		$targetgroupid = intval($_POST['targetgroupid']);
		$targetsubgroupid = intval($_POST['targetsubgroupid']);
		
		$sql = 	"SELECT `relation_type_id` AS `id` FROM `relations` WHERE `id` = {$relation_id}";
		$actual_rel_type = $db->fetch_one($sql);
		

		$sql = 	"SELECT `relation_type_id` as `id` FROM `relations_groups` WHERE (`part` LIKE 'source' AND (`annotation_set_id` = {$sourcegroupid} OR `annotation_subset_id` = {$sourcesubgroupid})) " ; 
 		$result = $db->fetch_rows($sql);

		$source = array();
		foreach($result as $element)
			$source[] = $element['id'];

		$sql = 	"SELECT `relation_type_id` as `id` FROM `relations_groups` WHERE (`part` LIKE 'target' AND (`annotation_set_id` = {$targetgroupid} OR `annotation_subset_id` = {$targetsubgroupid})) " ; 
 		$result = $db->fetch_rows($sql);

		$rel_ids = array();
		foreach($result as $element)
			if (in_array($element['id'], $source))
				$rel_ids[] = $element['id'];
		
		if(!count($rel_ids)){
			throw new Exception("Brak możliwych relacji, które mogą zachodzić między połączonymi anotacjami.");
		}
		else{		
			$sql = 	"SELECT `id`, `name` FROM `relation_types` WHERE `id` IN (". implode(", ", $rel_ids) .")";
			$result = $db->fetch_rows($sql);
		
			foreach($result as $key=>$element){
				if($element['id'] == $actual_rel_type)
					$result[$key]["active"] = true;
				else
					$result[$key]["active"] = false;
			}
			return $result;
		}
	}	
}
?>
