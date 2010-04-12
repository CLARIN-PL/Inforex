<?php

class Page_browse extends CPage{

	var $isSecure = false;
	
	function execute(){
		global $mdb2, $corpus;
				
		if (!$corpus){
			$this->redirect("index.php?page=home");
		}
						
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$p = intval($_GET['p']);		
		$status	= array_key_exists('status', $_GET) ? $_GET['status'] : $_COOKIE['status'];
		$type 	= array_key_exists('type', $_GET) ? $_GET['type'] : $_COOKIE['type'];
		$year 	= array_key_exists('year', $_GET) ? $_GET['year'] : $_COOKIE['year'];
		$month 	= array_key_exists('month', $_GET) ? $_GET['month'] : $_COOKIE['month'];
		$search	= array_key_exists('search', $_GET) ? $_GET['search'] : $_COOKIE['search'];
		$search_field= array_key_exists('search_field', $_GET) ? $_GET['search_field'] : explode("|", $_COOKIE['search_field']);
		$annotation	= array_key_exists('annotation', $_GET) ? $_GET['annotation'] : $_COOKIE['annotation'];
				
		$statuses = array_filter(explode(",", $status), "intval");
		$types = array_filter(explode(",", $type), "intval");
		$years = array_filter(explode(",", $year), "intval");
		$months = array_filter(explode(",", $month), "intval");		
		$search = strval($search);
		$annotations = ($annotation=="no_annotation") ? $annotation : array_diff(explode(",", $annotation), array(""));
		$search_field = is_array($search_field) ? $search_field : array('title');

		if (defined(IS_RELEASE)){
			$years = array(2004);
			$statuses = array(2);
			$months = array();
		}

		// Zapisz parametry w sesjii
		// ******************************************************************************		
		setcookie('search', $search);
		setcookie('search_field', implode("|", $search_field));
		setcookie('type', implode(",",$types));
		setcookie('year', implode(",",$years));
		setcookie('month', implode(",",$months));
		setcookie('status', implode(",",$statuses));
		setcookie('annotation', $annotations=="no_annotation" ? $annotations : implode(",",$annotations)); 

		/*** 
		 * Parametry stronicowania
		 ******************************************************************************/		

		$limit = 100;
		$from = $limit * $p;
		
		/*** 
		 * Przygotuj warunki where dla zapytania SQL
		 ******************************************************************************/		
		$where = array();
		$join = "";
		
		$where[] = "r.corpora = {$corpus['id']}";
		
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
				$where_month[] = "MONTH(r.date)=$month";
			}
			$where[] = "(" . implode(" OR ", $where_month) . ")";
		}
			
		/// Fraza
		if (strval($search)){
			$where_fraza = array();
			if (in_array('title', $search_field))
				$where_fraza[] = "r.title LIKE '%$search%'";
			if (in_array('content', $search_field))
				$where_fraza[] = "r.content LIKE '%$search%'";
			if (count($where_fraza))
				$where[] = ' (' . implode(" OR ", $where_fraza) . ') ';
		}
			
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
		
		/// Anotacje
		if ($annotations == "no_annotation"){
			$where[] = "a.id IS NULL";
			$join = " LEFT JOIN reports_annotations a ON (r.id = a.report_id)";
		}elseif (is_array($annotations) && count($annotations)>0){
			$where_annotation = array();
			foreach ($annotations as $annotation){
				$where_annotation[] = "an.type='$annotation'";			
			}
			$where[] = "(" . implode(" OR ", $where_annotation) . ")";			
			$join .= " INNER JOIN reports_annotations an ON ( an.report_id = r.id )";
			$group = " GROUP BY r.id";
		}
		
		$where = ((count($where)>0) ? " WHERE " . implode(" AND ", $where) : "");
		setcookie('sql_where', $where);
		setcookie('sql_join', $join);
		setcookie('sql_group', $group);
		
		$sql = 	"SELECT r.title, r.status, r.id, r.number, rt.name AS type_name, rs.status AS status_name" .
				" FROM reports r" .
				" INNER JOIN reports_types rt ON ( r.type = rt.id )" .
				" INNER JOIN reports_statuses rs ON ( r.status = rs.id )" .
				$join .
				$where .
				$group .
				" ORDER BY r.id ASC" .
				" LIMIT {$from},{$limit}";
		//die($sql);
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		
		array_walk($rows, "array_walk_highlight", $search);
		
		$sql = "" .
				"SELECT COUNT(DISTINCT r.id)" .
				" FROM reports r" .
				$join .
				$where;
		if (PEAR::isError($r = $mdb2->query($sql))) 
			die("<pre>{$r->getUserInfo()}</pre>");
		$rows_all = $r->fetchOne();
			
		// Przygotuj mapę podstron do szybkiej nawigacji
		$page_map = array();
		$pages = (int)floor(($rows_all+$limit-1)/$limit);
		$pi = 0;
		for ( $pi = 0;  $pi < 2 && $pi < $pages; $pi++ ) 
			$page_map[] = array('p'=>$pi, 'text'=>($pi+1), 'selected'=>$pi==$p);
		if ( $p-2 > 2+1 )
			$page_map[] = array('nolink'=>1, 'text'=>"...");
		for ( $pim = max($p-5, $pi); $pim < $p+5+1 && $pim < $pages; $pim++)
			$page_map[] = array('p'=>$pim, 'text'=>($pim+1), 'selected'=>$pim==$p);
		if ( $pages-2 > $p+5+1 )
			$page_map[] = array('nolink'=>1, 'text'=>"...");
		for ( $pi = max($pages-2, $p+5);  $pi < $pages; $pi++ ) 
			$page_map[] = array('p'=>$pi, 'text'=>($pi+1), 'selected'=>$pi==$p);
//		1:10
//		p-5:p+5
//		n-10:n
			
		$this->set('page_map', $page_map);
		$this->set('status', $status);
		$this->set('rows', $rows);
		$this->set('p', $p);
		$this->set('pages', $pages);
		$this->set('total_count', number_format($rows_all, 0, ".", " "));
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('from', $from+1);
		$this->set('search', $search);
		$this->set('search_field_title', in_array('title', $search_field));
		$this->set('search_field_content', in_array('content', $search_field));
		$this->set('type', $type);
		$this->set('type_set', $type!="");
		$this->set('annotation_set', $annotations == "no_annotation");
		
		$this->set_filter_menu($search, $statuses, $types, $years, $months, $annotations, $where);
	}
	
	/**
	 * Ustawia parametry filtrów wg. atrybutów raportów.
	 */
	function set_filter_menu($search, $statuses, $types, $years, $months, $annotations, $where){
		global $mdb2, $corpus;
		//// Years
		$sql = "SELECT YEAR(date) as year, COUNT(*) as count" .
				" FROM reports " .
				" WHERE corpora={$corpus['id']}" .
				" GROUP BY year" .
				" ORDER BY year DESC";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'year', $years);
		$this->set("years", $rows);		

		//// Months
		$sql = "SELECT MONTH(date) as month, COUNT(*) as count" .
				" FROM reports" .
				" WHERE corpora={$corpus['id']}" .
				" GROUP BY month" .
				" ORDER BY month DESC";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'month', $months);
		$this->set("months", $rows);		

		//// Statuses
		$sql = "SELECT s.id, s.status as name, COUNT(*) as count" .
				" FROM reports r" .
				" LEFT JOIN reports_statuses s ON (s.id=r.status)" .
				" WHERE corpora={$corpus['id']}" .
				" GROUP BY r.status" .
				" ORDER BY `s`.`order`";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'id', $statuses);
		$this->set("statuses", $rows);		

		//// Types
		$sql = "SELECT t.id, t.name, COUNT(*) as count" .
				" FROM reports r" .
				" LEFT JOIN reports_types t ON (t.id=r.type)" .
				" WHERE" .
				"   1=1" .
				" AND r.corpora={$corpus['id']}" .
				    (is_array($years) && count($years) ? " AND " . where_or("YEAR(r.date)", $years) : "" ) .
				    (is_array($types) && count($types) ? " AND " . where_or("r.type", $types) : "") .
				    (is_array($months) && count($months) ? " AND " . where_or("MONTH(r.date)", $months) : "") .
				    (isset($search) && $search!="" ? " AND r.title LIKE '%$search%'" : "") .
				" GROUP BY t.name" .
				" ORDER BY t.name ASC";		
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		array_walk($rows, "array_map_replace_spaces");
		prepare_selection_and_links($rows, 'id', $types);
		$this->set("types", $rows);		
		
		//// Treść
		$content = array();
		$content[] = array("name" => "bez treści", "link" => "no_content");
		$this->set("content", $content);
		
		//// Anotacje
		$annotations_list = array();
		//// Types
//		$sql = "SELECT COUNT(*) as count" .
//				" FROM reports r" .
////				" LEFT JOIN reports_types t ON (t.id=r.type)" .
//				" LEFT JOIN reports_annotations a ON (r.id=a.report_id)" .
//				" WHERE" .
//				" a.id IS NULL";
//				"   1=1" .
//				    (is_array($years) && count($years) ? " AND " . where_or("YEAR(r.date)", $years) : "" ) .
//				    (is_array($types) && count($types) ? " AND " . where_or("r.type", $types) : "") .
//				    (is_array($months) && count($months) ? " AND " . where_or("MONTH(r.date)", $months) : "") .
//				    (isset($search) && $search!="" ? " AND r.title LIKE '%$search%'" : "") .
//				    " r.id NOT IN ( SELECT DISTINCT(report_id) FROM reports_annotations ) ";
//				" GROUP BY r.id " .
//				" HAVING COUNT(a.id)"
//		die($sql);

		// Annotations
		$annotations_list = array();
		$sql = "SELECT a.type, a.type as name, COUNT(*) as count" .
				" FROM reports_annotations a" .
				"  JOIN reports r ON (a.report_id=r.id)" .
				" WHERE r.corpora={$corpus['id']}" .
				" GROUP BY a.type";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		array_walk($rows, "array_map_replace_spaces");
		prepare_selection_and_links($rows, 'type', is_array($annotations) ? $annotations : array());
		$annotations_list = $rows;
		//print_r($rows);

		$sql = "SELECT distinct(a.report_id) AS id" .
				" FROM reports_annotations a" .
				"  JOIN reports r ON (a.report_id=r.id)" .
				" WHERE r.corpora={$corpus['id']}";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		$ids = array();
		foreach ($rows as $r) $ids[$r['id']] = 1;
		$sql = "SELECT r.id" .
				" FROM reports r" .
				" LEFT JOIN reports_types t ON (t.id=r.type)" .
				" WHERE" .
				"   1=1" .
				" AND r.corpora={$corpus['id']} " .
				    (is_array($years) && count($years) ? " AND " . where_or("YEAR(r.date)", $years) : "" ) .
				    (is_array($types) && count($types) ? " AND " . where_or("r.type", $types) : "") .
				    (is_array($months) && count($months) ? " AND " . where_or("MONTH(r.date)", $months) : "") .
				    (is_array($statuses) && count($statuses) ? " AND " .where_or("r.status", $statuses) : "") .
				    (isset($search) && $search!="" ? " AND r.title LIKE '%$search%'" : "");
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}		
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		$count_no_annotation = 0;
		foreach ($rows as $r) if (!isset($ids[$r['id']])) $count_no_annotation++;

		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchOne();
		$annotations_list[] = array("name" => "bez anotacji", "link" => "no_annotation", "count" => $count_no_annotation, "selected" => ($annotations == "no_annotation"));
		$this->set("annotations", $annotations_list);
		
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

function where_or($column, $values){
	$ors = array();
	foreach ($values as $value)
		$ors[] = "$column = '$value'";
	return "(" . implode(" OR ", $ors) . ")";
}

?>
