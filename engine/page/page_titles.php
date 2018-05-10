<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_titles extends CPage{
	
	function execute(){
		global $mdb2;

		$sql = "SELECT title, COUNT(*) AS c FROM reports" .
				" GROUP BY title" .
				" ORDER BY c desc";
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
				
		$this->set('rows', $rows);
	}
}


?>
