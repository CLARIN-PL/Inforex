<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_list extends CPage{
	
	function execute(){
		global $mdb2;
				
		$status = HTTP_Session2::get('status');
		$p 		= intval($_GET['p']);
		$year 	= intval($_GET['year']);
		$month 	= intval($_GET['month']);
		
		/* 
		 * 
		 */		
		$status = $_POST['status'];
		if (isset($status))
			HTTP_Session2::set('status', $status);
		else
			$status = HTTP_Session2::get('status');

		/*
		 * Ustalenie daty do wyświetlenia
		 */

		if ( !isset($_GET['p']) && HTTP_Session2::get('year') == $year &&  HTTP_Session2::get('month') == $month ){
			$p = HTTP_Session2::get('p');
		}
		
		HTTP_Session2::set('year', $year);
		HTTP_Session2::set('month', $month);
		HTTP_Session2::set('p', $p);
							
		$limit = 25;
		$from = $limit * $p;
		
		$sql = "" .
				"SELECT r.*, rt.name AS type_name, rs.status AS status_name" .
				" FROM reports r" .
				" LEFT JOIN reports_types rt ON ( r.type = rt.id )" .
				" LEFT JOIN reports_statuses rs ON ( r.status = rs.id )" .
				" WHERE MONTH(r.date) = {$month}" .
				"   AND YEAR(r.date) = {$year}" .
				($status?" AND r.status=$status":"").
				" LIMIT {$from},{$limit}";		
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
		
		$sql = "" .
				"SELECT COUNT(id)" .
				" FROM reports" .
				" WHERE MONTH(date) = {$month}" .
				"   AND YEAR(date) = {$year}";
				($status?" AND status=$status":"").
		$rows_all = $mdb2->query($sql)->fetchOne();
		
		$this->set('status', $status);
		$this->set('statuses', $statuses);
		$this->set('rows', $rows);
		$this->set('p', $p);
		$this->set('pages', (int)floor(($rows_all+$limit-1)/$limit));
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('from', $from+1);
	}
}


?>
