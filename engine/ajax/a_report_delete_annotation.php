<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_delete_annotation extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji.";
	}
	
	function execute(){
		global $mdb2;
		$annid = intval($_POST['annotation_id']);

		$row = $mdb2->queryRow("SELECT r.id, r.content FROM reports_annotations ra JOIN reports r ON (r.id=ra.report_id) WHERE ra.id=$annid");
		$id = $row[0];
		$content = $row[1];
		if ($id){
			$content = preg_replace("/<an#$annid:.*?>(.*?)<\/an>/", "$1", $content);
			$mdb2->query("UPDATE reports SET content='".mysql_escape_string($content)."' WHERE id={$id}");
			$mdb2->query("DELETE FROM reports_annotations_optimized WHERE id=$annid");
			$json = array("success" => "ok");		
		}
		else{
			$json = array("error" => 'Anotacja nie istnieje w bazie.');
		}
		echo json_encode($json);
	}
	
}
?>
