<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_update_annotation_wsd extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji <small>[checkPermission]</small>.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$annotation_id = intval($_POST['annotation_id']);
		$value = strval($_POST['value']);

		$sql_select = "SELECT ata.id" .
				" FROM annotation_types_attributes ata" .
				" JOIN reports_annotations an ON (an.type = ata.annotation_type)" .
				" WHERE an.id = ?" .
				"  AND ata.name = 'sense'";
		$attribute_id = db_fetch_one($sql_select, array($annotation_id));
		
		/* Usuń wszystkie wartości dla tego atrybutu — system nie wspiera wielu decyzji dla 
		 * jednego atrybutu.
		 */
		db_execute("DELETE FROM reports_annotations_attributes WHERE" .
				" annotation_id = ? AND annotation_attribute_id = ?",
				array($annotation_id, $attribute_id));
		
		$sql_replace = "REPLACE reports_annotations_attributes" .
				" SET annotation_id = ?, annotation_attribute_id = ?, value = ?, user_id = ?";
		db_execute($sql_replace, array($annotation_id, $attribute_id, $value, $user['user_id']));
		
		return;
	}
	
}
?>
