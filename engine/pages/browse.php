<?php

class Page_browse extends CPage{
	
	function execute(){
		global $mdb2;
				
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$p = intval($_GET['p']);		
		$status	= array_key_exists('status', $_GET) ? $_GET['status'] : HTTP_Session2::get('status');
		$type 	= array_key_exists('type', $_GET) ? $_GET['type'] : HTTP_Session2::get('type');
		$year 	= array_key_exists('year', $_GET) ? $_GET['year'] : HTTP_Session2::get('year');
		$month 	= array_key_exists('month', $_GET) ? $_GET['month'] : HTTP_Session2::get('month');
		$search	= array_key_exists('search', $_GET) ? $_GET['search'] : HTTP_Session2::get('search');
		
		$statuses = explode(",", $status);
		$types = explode(",", $type);
		$years = explode(",", $year);
		$months = explode(",", $month);
		
		$statuses = array_filter($statuses, "intval");
		$years = array_filter($years, "intval");
		$types = array_filter($types, "intval");
		$months = array_filter($months, "intval");
		$search = strval($search);

		// Zapisz parametry w sesjii
		// ******************************************************************************		
		HTTP_Session2::set('search', $search);
		HTTP_Session2::set('type', implode(",",$types));
		HTTP_Session2::set('year', implode(",",$years));
		HTTP_Session2::set('month', implode(",",$months));
		HTTP_Session2::set('status', implode(",",$statuses));

		/*** 
		 * Zapisz parametry w sesjii
		 ******************************************************************************/		

		$limit = 100;
		$from = $limit * $p;
		
		/*** 
		 * Przygotuj warunki where dla zapytania SQL
		 ******************************************************************************/		
		$where = array();
		
		//// Rok
		if (count($years)){
		$where_year = array();
			foreach ($years as $year){
				$where_year[] = "YEAR(r.date)=$year";
			}
			$where[] = "(" . implode(" OR ", $where_year) . ")";
		}

		//// Miesiąc
		if (count($months)){
		$where_month = array();
			foreach ($months as $month){
				$where_month[] = "YEAR(r.date)=$month";
			}
			$where[] = "(" . implode(" OR ", $where_month) . ")";
		}
			
		/// Fraza
		if (strval($search))
			$where[] = "r.title LIKE '%$search%'";
			
		/// Typ
		if (count($types)>0){
			$where_type = array();
			foreach ($types as $type){
				$where_type[] = "r.type=$type";			
			}
			$where[] = "(" . implode(" OR ", $where_type) . ")";
		}

		/// Status
		if (count($statuses)>0){
			$where_status = array();
			foreach ($statuses as $status){
				$where_status[] = "r.status=$status";			
			}
			$where[] = "(" . implode(" OR ", $where_status) . ")";
		}
		
		$where = ((count($where)>0) ? " WHERE " . implode(" AND ", $where) : "");
		HTTP_Session2::set('sql_where', $where);
		
		$sql = "" .
				"SELECT r.*, rt.name AS type_name, rs.status AS status_name" .
				" FROM reports r" .
				" INNER JOIN reports_types rt ON ( r.type = rt.id )" .
				" INNER JOIN reports_statuses rs ON ( r.status = rs.id )" .
				$where .
				" LIMIT {$from},{$limit}";
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
		array_walk($rows, "array_walk_highlight", $search);
		fb($sql);
		$sql = "" .
				"SELECT COUNT(r.id)" .
				" FROM reports r" .
				$where;
		$rows_all = $mdb2->query($sql)->fetchOne();
			
		$this->set('status', $status);
		$this->set('rows', $rows);
		$this->set('p', $p);
		$this->set('pages', (int)floor(($rows_all+$limit-1)/$limit));
		$this->set('total_count', $rows_all);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('from', $from+1);
		$this->set('search', $search);
		
		$this->set_filter_menu($search, $statuses, $types, $years, $months, $where);
	}
	
	/**
	 * Ustawia parametry filtrów wg. atrybutów raportów.
	 */
	function set_filter_menu($search, $statuses, $types, $years, $months, $where){
		global $mdb2;
		//// Years
		$sql = "SELECT YEAR(date) as year, COUNT(*) as count" .
				" FROM reports " .
				" GROUP BY year" .
				" ORDER BY year DESC";
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'year', $years);
		$this->set("years", $rows);		

		//// Months
		$sql = "SELECT MONTH(date) as month, COUNT(*) as count" .
				" FROM reports" .
				" GROUP BY month" .
				" ORDER BY month DESC";
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'month', $months);
		$this->set("months", $rows);		

		//// Statuses
		$sql = "SELECT s.id, s.status as name, COUNT(*) as count" .
				" FROM reports r" .
				" LEFT JOIN reports_statuses s ON (s.id=r.status)" .
				" GROUP BY r.status" .
				" ORDER BY `s`.`order`";
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'id', $statuses);
		$this->set("statuses", $rows);		

		//// Types
		$sql = "SELECT t.id, t.name, COUNT(*) as count" .
				" FROM reports r" .
				" LEFT JOIN reports_types t ON (t.id=r.type)" .
				" GROUP BY t.name" .
				" ORDER BY t.name ASC";
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);				
		array_walk($rows, "array_map_replace_spaces");
		prepare_selection_and_links($rows, 'id', $types);
		$this->set("types", $rows);		
	}
}

function prepare_selection_and_links(&$rows, $column, $values){
	foreach ($rows as $id=>$row){
		$rows[$id]['selected'] = in_array($row[$column], $values) || count($values)==0;			
		if ($rows[$id]['selected'])
			if (count($values)==0)
				 $years_in_link = array($row[$column]);
			else
				$years_in_link = array_diff($values, array($row[$column]));
		else
			$years_in_link = array_merge($values, array($row[$column]));
		sort($years_in_link);
		$rows[$id]['link'] = implode(",",$years_in_link);   
	}
	
}

function array_map_replace_spaces(&$value){
	$value['name'] = str_replace(" ", "&nbsp;", $value['name']);
} 

function array_walk_highlight(&$value, $key, $phrase){
	$value['title'] = str_replace($phrase, "<em>$phrase</em>", $value['title']);
} 

?>
