<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_semquel_get_sql extends CPage {
	var $isSecure = false;

	function execute(){
	
		$sql = $_POST['semquel'];
		$db2 = new Database(Config::Config()->get_relation_marks_db());
		
		return $db2->fetch_rows($sql);
	}	
}
