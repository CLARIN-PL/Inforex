<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbReportEvent{
	
	static function addEvent($report_id, $event_type_id, $user_id){
		global $db;
		$sql = "INSERT INTO reports_events (report_id, event_type_id, user_id, creation_time) " .
				"VALUES ($report_id, $event_type_id, $user_id, now())";
		
		db_execute($sql);
		//$db->execute($sql);
	}
	
}
