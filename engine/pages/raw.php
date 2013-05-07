<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_raw extends CPage{
	
	function execute(){
		global $mdb2;
				
		$id 	= intval($_GET['id']);

		$sql = "" .
				"SELECT date" .
				" FROM reports" .
				" WHERE id = {$id}";
		$date = $mdb2->query($sql)->fetchOne();
		
		$y = date("Y", strtotime($date));
		$m = date("m", strtotime($date));
		
		$file_path = GLOBAL_PATH_REPORTS_HTML . DIR_SEP . $y . DIR_SEP . $m . DIR_SEP . sprintf("%s_%s_%d.txt", $y, $m, $id); 
		
		header('Content-Type: text/html; charset=UTF-8');
		$this->set('html', file_get_contents($file_path));
	}
}


?>
