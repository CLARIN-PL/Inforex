<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Set flag status for given document unless the flag has been already set.
 * @param $db (Database) database gateway
 * @param $corpora_id (int) Corpus id
 * @param $report_id (int) Report id
 * @param $flag_short_name (string) Short name of corpus flag
 * @param $status (int) One of: FLAG_ID_NOT_*
 */
function set_status_if_not_ready($db, $corpora_id, $report_id, $flag_short_name, $status){
	$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = ?";
	$corpora_flag_id = $db->fetch_one($sql, array($corpora_id, $flag_short_name));

	if ($corpora_flag_id){
		$flag_id = intval($db->fetch_one("SELECT flag_id FROM reports_flags WHERE corpora_flag_id = ? AND report_id = ?",
							array($corpora_flag_id, $report_id) ));
		if ( $flag_id <= 0 ){
			$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, ?)",
				array($corpora_flag_id, $report_id, $status));
		}	
	}		
}

?>