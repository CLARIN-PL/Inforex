<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annotation_get_shared_attribute_types_values extends CPage {

	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji atrybutów anotacji.";
	}

	function execute(){
		global $db;
		$annid = intval($_POST['annotation_id']);

		$results = $db->fetch_rows(
			"SELECT rasa.annotation_id, sa.id, sa.name, sa.type, sae.value as possible_value, rasa.value as value " .
			"FROM annotation_types_shared_attributes atsa " .
			"JOIN shared_attributes sa " .
			"ON sa.id = atsa.shared_attribute_id " .
			"AND atsa.annotation_type_id = (select type_id from reports_annotations_optimized where id=?) " .
			"LEFT JOIN shared_attributes_enum sae " .
			"ON sae.shared_attribute_id = sa.id " .
			"LEFT JOIN reports_annotations_shared_attributes rasa " .
			"ON rasa.annotation_id=? " .
			"AND sa.id = rasa.shared_attribute_id ",
			array($annid, $annid));

		$json = array();
		foreach ($results as $result){
			$id = $result["id"];
			$name = $result["name"];
			$possible_value = $result["possible_value"];
			$type = $result["type"];
			$value = $result["value"];
			if (!array_key_exists($id , $json))
				$json[$id] = array(
					"name" => $name,
					"type" => $type,
					"value" => $value,
					"possible_values" => array($possible_value)
				);
			else {
				array_push(
					$json[$id]["possible_values"],
					$possible_value);
			}

		}
		return $json;
	}

}
?>
