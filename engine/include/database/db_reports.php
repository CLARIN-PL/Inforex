<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

function db_reports_get_statuses(){
	global $mdb2;
	$list = $mdb2->query("SELECT * FROM reports_statuses")->fetchAll(MDB2_FETCHMODE_ASSOC);
	return $list;
}

function db_reports_get_statuses_with_count(){
	global $mdb2;
	$sql = "SELECT r.status AS status_id, s.status AS status_name, COUNT(r.id) AS count" .
			" FROM reports r" .
			" LEFT JOIN reports_statuses s ON (r.status=s.id)" .
			" GROUP BY r.status";
	$list = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
	return $list;
}

function db_reports_get_types(){
	global $mdb2;
	$list = $mdb2->query("SELECT * FROM reports_types")->fetchAll(MDB2_FETCHMODE_ASSOC);
	return $list;
}

function db_reports_get_search($title_phrase, $content_phrase){
	global $mdb2;
	$list = $mdb2->query("SELECT * FROM reports WHERE title LIKE '%{$title_phrase}%'")->fetchAll(MDB2_FETCHMODE_ASSOC);
	return $list;
}
?>
