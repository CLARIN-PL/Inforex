<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_list_total extends CPage{
	
	function execute(){
		global $mdb2;
		
		$status = HTTP_Session2::get('status');
		
		$sql = "" .
				"SELECT YEAR(r.date) as year, " .
				"       MONTH(r.date) as month, " .
				"       COUNT(r.id) as count, " .
				"       COUNT(s.id) as s," .
				"		SUM(IF(s.id=2, 1, 0)) as sz," .
				"		SUM(IF(s.id=2 AND r.formated=1, 1, 0)) as szf" .
				" FROM reports r" .
				" LEFT JOIN reports_statuses s ON ( r.status = s.id AND s.id <> 1 )" .
				($status?" WHERE r.status=$status":"") .
				" GROUP BY year, month";
		
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);

		$statuses = db_reports_get_statuses_with_count();
		
		$this->set('statuses', $statuses);
		$this->set('rows', $rows);			
		$this->set('status', $status);
	}
}
 
?>