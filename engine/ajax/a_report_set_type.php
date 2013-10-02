<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_set_type extends CPage {
	
	function execute(){
		global $mdb2;
		$type = intval($_POST['type']);
		$id = intval($_POST['id']);
		$mdb2->query("UPDATE reports SET type=$type WHERE id=$id");			
		if (PEAR::isError($r = $mdb2->query("SELECT name FROM reports_types WHERE id=$type")))
			die("<pre>{$r->getUserInfo()}</pre>");
		$type_name = $r->fetchOne();				
		return array("type_name"=>$type_name);
	}
	
}
?>
