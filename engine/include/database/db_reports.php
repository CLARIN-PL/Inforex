<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

function db_reports_get_statuses(){
	global $db;
	$list = $db->fetch_rows("SELECT * FROM reports_statuses");
	return $list;
}

function db_reports_get_statuses_with_count(){
	global $db;
	$sql = "SELECT r.status AS status_id, s.status AS status_name, COUNT(r.id) AS count" .
			" FROM reports r" .
			" LEFT JOIN reports_statuses s ON (r.status=s.id)" .
			" GROUP BY r.status";
	$list = $db->fetch_rows($sql);
	return $list;
}

function db_reports_get_types(){
	global $db;
	$list = $db->fetch_rows("SELECT * FROM reports_types");
	return $list;
}

function db_reports_get_search($title_phrase, $content_phrase){
	global $db;
	$list = $db->fetch_rows("SELECT * FROM reports WHERE title LIKE '%{$title_phrase}%'");
	return $list;
}
?>
