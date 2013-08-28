<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_get_event_slots extends CPage {
	var $isSecure = false;
	function execute(){
		global $mdb2, $user;
		$event_id = intval($_POST['event_id']);

		$sql = "SELECT reports_events_slots.report_event_slot_id AS slot_id, " .
					  "event_type_slots.event_type_slot_id AS slot_type_id, " .
					  "event_type_slots.name AS slot_type, " .
					  "reports_annotations.id as annotation_id, " .
					  "reports_annotations.type AS annotation_type, " .
					  "reports_annotations.text AS annotation_text " .
					  "FROM reports_events_slots " .
					  "JOIN event_type_slots " .
					  	"ON (reports_events_slots.report_event_id={$event_id} " .
					  	"AND reports_events_slots.event_type_slot_id=event_type_slots.event_type_slot_id) " .
				  	  "LEFT JOIN reports_annotations " .
				  	  	"ON (reports_events_slots.report_annotation_id=reports_annotations.id)";
				  	  	
		$result = db_fetch_rows($sql);
		return $result;
		//echo json_encode($result);
	}
	
}
?>
