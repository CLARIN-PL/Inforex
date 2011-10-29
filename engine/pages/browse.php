<?php

class Page_browse extends CPage{

	var $isSecure = true;
	var $roles = array();
	var $filter_attributes = array("text","year","month","type","annotation","status", "subcorpus");
	
	function checkPermission(){
		global $corpus;
		return hasCorpusRole('read') && !hasCorpusRole('read_limited') || $corpus['public'];
	}
	
	function execute(){
		global $mdb2, $corpus;
				
		if (!$corpus){
			$this->redirect("index.php?page=home");
		}
		$cid = $corpus['id'];
						
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$p = intval($_GET['p']);	
		$prevReport = intval($_GET['r']);	
		$status	= array_key_exists('status', $_GET) ? $_GET['status'] : $_COOKIE["{$cid}_".'status'];
		$type 	= array_key_exists('type', $_GET) ? $_GET['type'] : $_COOKIE["{$cid}_".'type'];
		$year 	= array_key_exists('year', $_GET) ? $_GET['year'] : $_COOKIE["{$cid}_".'year'];
		$month 	= array_key_exists('month', $_GET) ? $_GET['month'] : $_COOKIE["{$cid}_".'month'];
		$search	= array_key_exists('search', $_GET) ? $_GET['search'] : $_COOKIE["{$cid}_".'search'];
		$search_field= array_key_exists('search_field', $_GET) ? $_GET['search_field'] : explode("|", $_COOKIE["{$cid}_".'search_field']);
		$annotation	= array_key_exists('annotation', $_GET) ? $_GET['annotation'] : $_COOKIE["{$cid}_".'annotation'];
		$subcorpus	= array_key_exists('subcorpus', $_GET) ? $_GET['subcorpus'] : $_COOKIE["{$cid}_".'subcorpus'];
		$filter_order = array_key_exists('filter_order', $_GET) ? $_GET['filter_order'] : $_COOKIE["{$cid}_".'filter_order'];
				
		$search = stripslashes($search);
				
		$statuses = array_filter(explode(",", $status), "intval");
		$types = array_filter(explode(",", $type), "intval");
		$years = array_filter(explode(",", $year), "intval");
		$months = array_filter(explode(",", $month), "intval");
		$subcorpuses = array_filter(explode(",", $subcorpus), "intval");
		$search = strval($search);
		$annotations = ($annotation=="no_annotation") ? $annotation : array_diff(explode(",", $annotation), array(""));
		$search_field = is_array($search_field) ? $search_field : array('title');
		$filter_order = explode(",", $filter_order);		
		$filter_order = is_array($filter_order) ? $filter_order : array();

		if (count($statuses)==0){
			//$statuses = array(2);
		}
		
		if (defined(IS_RELEASE) && $cid==2){
			$years = array(2004);
			$statuses = array(2);
			$months = array();
		}

		// Zapisz parametry w sesjii
		// ******************************************************************************		
		setcookie("{$cid}_".'search', $search);
		setcookie("{$cid}_".'search_field', implode("|", $search_field));
		setcookie("{$cid}_".'type', implode(",",$types));
		setcookie("{$cid}_".'year', implode(",",$years));
		setcookie("{$cid}_".'month', implode(",",$months));
		setcookie("{$cid}_".'subcorpus', implode(",",$subcorpuses));
		setcookie("{$cid}_".'status', implode(",",$statuses));
		setcookie("{$cid}_".'annotation', $annotations=="no_annotation" ? $annotations : implode(",",$annotations)); 
		setcookie("{$cid}_".'status', implode(",",$statuses));

		/*** 
		 * Parametry stronicowania
		 ******************************************************************************/		
		$limit = 100;
		$from = $limit * $p;
		
		/*** 
		 * Przygotuj warunki where dla zapytania SQL
		 ******************************************************************************/
		$		
		$where = array();
		$join = "";
		$select = "";
		$columns = array("lp"=>"Lp.", 
							"id"=>"Id", 
							"title"=>"Nazwa raportu", 
							"status_name"=>"Status"); // lista kolumna do wyświetlenia na stronie
		
		/// Fraza
		if (strval($search)){
			$where_fraza = array();
			if (in_array('title', $search_field))
				$where_fraza[] = "r.title LIKE '%$search%'";
			if (in_array('content', $search_field))
				$where_fraza[] = "r.content LIKE '%$search%'";
			if (count($where_fraza))
				$where['text'] = ' (' . implode(" OR ", $where_fraza) . ') ';
		}

		if (count($years)>0)	$where['year'] = where_or("YEAR(r.date)", $years);			
		if (count($months)>0)	$where['month'] = where_or("MONTH(r.date)", $months);
		if (count($types)>0)	$where['type'] = where_or("r.type", $types);
		if (count($statuses)>0)	$where['status'] = where_or("r.status", $statuses);
		if (count($subcorpuses)>0)	$where['subcorpus'] = where_or("r.subcorpus_id", $subcorpuses);
		
		/// Anotacje
		if ($annotations == "no_annotation"){
			$where['annotation'] = "a.id IS NULL";
			$join = " LEFT JOIN reports_annotations a ON (r.id = a.report_id)";
		}elseif (is_array($annotations) && count($annotations)>0){
			$where['annotation'] = where_or("an.type", $annotations);			
			$join .= " INNER JOIN reports_annotations an ON ( an.report_id = r.id )";
			$group = " GROUP BY r.id";
		}
		
		/// Kolejność
		$order = "r.id ASC";
		
		/// Wczytaj dodatkowe kolumny zależne od korpusu
		if ( $cid == 3 ){
			$join .= " LEFT JOIN reports_ext_3 ext ON (r.id = ext.id)";
			$select .= "ext.*, YEAR(r.date) as year, ";
			$columns["deceased_age"] = "Wiek";
			$columns["deceased_gender"] = "Płeć";
			$columns["deceased_maritial"] = "Status cywilny";
			$columns["source"] = "Sposób zapisu";
			$columns["year"] = "Rok";
			$columns["suicide_place"] = "Miejsce samobójstwa";
			
			$order = "r.title ASC";
		}else{
			$columns["tokenization"] = "Tokenization";
		}
		
		$where_sql = ((count($where)>0) ? "AND " . implode(" AND ", array_values($where) ) : "");
		
		setcookie("{$cid}_".'sql_where', $where_sql);
		setcookie("{$cid}_".'sql_join', $join);
		setcookie("{$cid}_".'sql_group', $group);
		setcookie("{$cid}_".'sql_order', $order);
		
		if ($prevReport){
			$sql = 	"SELECT count(r.id) as cnt" .
					" FROM reports r" .
					" LEFT JOIN reports_types rt ON ( r.type = rt.id )" .
					" LEFT JOIN reports_statuses rs ON ( r.status = rs.id )" .
					" LEFT JOIN users u USING (user_id)" .
					$join .
					" WHERE r.corpora = {$corpus['id']} " .
					" AND r.id<$prevReport ".
					$where_sql .
					$group .
					" ORDER BY $order";	
			$prevCount = intval(db_fetch_one($sql));
			
			$p = (int)($prevCount/$limit);
			$from = $limit * $p;
		}
		
		$sql = 	"SELECT " .
				"	$select " .
				"	r.title, " .
				"	r.status, " .
				"	r.id, " .
				"	r.number, " .
				"	r.tokenization," .
				" 	rt.name AS type_name, " .
				"	rs.status AS status_name, " .
				"	u.screename" .
				" FROM reports r" .
				" LEFT JOIN reports_types rt ON ( r.type = rt.id )" .
				" LEFT JOIN reports_statuses rs ON ( r.status = rs.id )" .
				" LEFT JOIN users u USING (user_id)" .
				$join .
				" WHERE r.corpora = {$corpus['id']} ".
				$where_sql .
				$group .
				" ORDER BY $order" .
				" LIMIT {$from},{$limit}";
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		
		$reportIds = array();
		foreach ($rows as $row){
			array_push($reportIds, $row['id']);
		}
		
		$sql = "SELECT * FROM corpora_flags WHERE corpora_id={$corpus['id']} ORDER BY sort";
		$corporaFlags = db_fetch_rows($sql);
		foreach ($corporaFlags as $corporaFlag){
			$columns["flag".$corporaFlag['corpora_flag_id']]=$corporaFlag;
		}
		
		$sql = "SELECT reports_flags.report_id, reports_flags.corpora_flag_id, reports_flags.flag_id, flags.name " .
				"FROM reports_flags " .
				"LEFT JOIN flags ON " .
					(count($reportIds)>0 ? 
				"report_id " .
					"IN (".implode(",",$reportIds).") " .
					"AND " : "") . "reports_flags.flag_id=flags.flag_id ";
		$reportFlags = db_fetch_rows($sql);
		
		$reportFlagsMap = array();
		foreach ($reportFlags as $reportFlag){
			if ($reportFlagsMap[$reportFlag['report_id']]){
				$reportFlagsMap[$reportFlag['report_id']][$reportFlag['corpora_flag_id']]['name']=$reportFlag['short'];
				$reportFlagsMap[$reportFlag['report_id']][$reportFlag['corpora_flag_id']]['flag_id']=$reportFlag['flag_id'];
			}
			else {
				$reportFlagsMap[$reportFlag['report_id']]=array();
				$reportFlagsMap[$reportFlag['report_id']][$reportFlag['corpora_flag_id']]['name']=$reportFlag['short'];
				$reportFlagsMap[$reportFlag['report_id']][$reportFlag['corpora_flag_id']]['flag_id']=$reportFlag['flag_id'];
			}
		}		
		
		for ($i=0; $i<count($rows); $i++){
			foreach ($corporaFlags as $corporaFlag){
				$rows[$i]["flag".$corporaFlag['corpora_flag_id']]['name']="NIE GOTOWY";			
				$rows[$i]["flag".$corporaFlag['corpora_flag_id']]['flag_id']=-1;			
				if ($reportFlagsMap[$rows[$i]['id']] && $reportFlagsMap[$rows[$i]['id']][$corporaFlag['corpora_flag_id']]){
					$rows[$i]["flag".$corporaFlag['corpora_flag_id']]['name']=$reportFlagsMap[$rows[$i]['id']][$corporaFlag['corpora_flag_id']]['name'];								
					$rows[$i]["flag".$corporaFlag['corpora_flag_id']]['flag_id']=$reportFlagsMap[$rows[$i]['id']][$corporaFlag['corpora_flag_id']]['flag_id'];								
				};
			}
		}
		
		array_walk($rows, "array_walk_highlight", $search);
		
		$sql = "SELECT COUNT(DISTINCT r.id) FROM reports r $join WHERE r.corpora={$corpus['id']} $where_sql";
		if (PEAR::isError($r = $mdb2->query($sql))) 
			die("<pre>{$r->getUserInfo()}</pre>");
		$rows_all = $r->fetchOne();

		// Usuń atrybuty z listy kolejności, dla których nie podano warunku.
		$where_keys = count($where) >0 ? array_keys($where) : array();
		$filter_order = array_intersect($filter_order, $where_keys);
		// Dodaj brakujące atrybuty do listy kolejności
		$filter_order = array_merge($filter_order, array_diff($where_keys, $filter_order) );
		
		//obsluga podkorpusow:
		//$sql = "SELECT * FROM corpus_subcorpora";
		//$subcorpuses = db_fetch_rows($sql);
		
		
		$this->set('columns', $columns);
		$this->set('page_map', create_pagging($rows_all, $limit, $p));
		$this->set('status', $status);
		$this->set('rows', $rows);
		$this->set('p', $p);
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
		$this->set('filter_order', $filter_order);
		$this->set('filter_notset', array_diff($this->filter_attributes, $filter_order));
		$this->set_filter_menu($search, $statuses, $types, $years, $months, $annotations, $filter_order, $subcorpuses);
	}
	
	/**
	 * Ustawia parametry filtrów wg. atrybutów raportów.
	 */
	function set_filter_menu($search, $statuses, $types, $years, $months, $annotations, $filter_order, $subcorpuses){
		global $mdb2, $corpus;

		$sql_where = array();
		$sql_where_parts['text'] = "r.title LIKE '%$search%'";
		$sql_where_parts['type'] = where_or("r.type", $types);
		$sql_where_parts['year'] = where_or("YEAR(r.date)", $years);
		$sql_where_parts['month'] = where_or("MONTH(r.date)", $months);
		$sql_where_parts['status'] = where_or("r.status", $statuses);
		$sql_where_parts['annotation'] = where_or("an.type", $annotations);
		$sql_where_parts['subcorpus'] = where_or("r.subcorpus_id", $subcorpuses);

		$sql_where_filtered_general = implode(" AND ", array_intersect_key($sql_where_parts, array_fill_keys($filter_order, 1)));
		$sql_where_filtered_general = $sql_where_filtered_general ? " AND ".$sql_where_filtered_general : "";
		$sql_where_filtered = array();
		$filter_order_stack = array();
		foreach ($filter_order as $f){
			if (count($filter_order_stack)==0)
				$sql_where_filtered[$f] = "";
			else
				$sql_where_filtered[$f] = " AND ".implode(" AND ", array_intersect_key($sql_where_parts, array_fill_keys($filter_order_stack, 1)));
			$filter_order_stack[] = $f;			
		}

		//******************************************************************
		// Years		
		$sql = "SELECT YEAR(r.date) as id, YEAR(r.date) as name, COUNT(DISTINCT r.id) as count" .
				" FROM reports r " .
				" LEFT JOIN reports_annotations an ON (an.report_id=r.id)" .
				" WHERE r.corpora={$corpus['id']}" .
				( isset($sql_where_filtered['year']) ? $sql_where_filtered['year'] : $sql_where_filtered_general).
				" GROUP BY id" .
				" ORDER BY id DESC";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'id', $years, $filter_order, "year");
		$this->set("years", $rows);		

		//******************************************************************
		// Subcorpuses		
		$sql = "SELECT r.subcorpus_id as id, cs.name, COUNT(DISTINCT r.id) as count" .
				" FROM reports r " .
				" LEFT JOIN corpus_subcorpora cs ON (r.subcorpus_id=cs.subcorpus_id)" .
				" LEFT JOIN reports_annotations an ON (an.report_id=r.id)" .
				" WHERE r.corpora={$corpus['id']}" .
				( isset($sql_where_filtered['subcorpus']) ? $sql_where_filtered['subcorpus'] : $sql_where_filtered_general).
				" GROUP BY cs.name" .
				" ORDER BY cs.name ASC";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'id', $subcorpuses, $filter_order, "subcorpus");
		$this->set("subcorpuses", $rows);	


		//******************************************************************
		//// Statuses
		$sql = "SELECT s.id, s.status as name, COUNT(DISTINCT r.id) as count" .
				" FROM reports r" .
				" LEFT JOIN reports_statuses s ON (s.id=r.status)" .
				" LEFT JOIN reports_annotations an ON (an.report_id=r.id)" .
				" WHERE corpora={$corpus['id']}" .
				( isset($sql_where_filtered['status']) ? $sql_where_filtered['status'] : $sql_where_filtered_general).
				" GROUP BY r.status" .
				" ORDER BY `s`.`order`";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		prepare_selection_and_links($rows, 'id', $statuses, $filter_order, "status");
		$this->set("statuses", $rows);		

		//******************************************************************
		//// Types
		$sql = "SELECT t.id, t.name, COUNT(DISTINCT r.id) as count" .
				" FROM reports r" .
				" LEFT JOIN reports_types t ON (t.id=r.type)" .
				" LEFT JOIN reports_annotations an ON (an.report_id=r.id)" .
				" WHERE r.corpora={$corpus['id']}" .
				( isset($sql_where_filtered['type']) ? $sql_where_filtered['type'] : $sql_where_filtered_general).
				" GROUP BY t.name" .
				" ORDER BY t.name ASC";		
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		array_walk($rows, "array_map_replace_spaces");
		prepare_selection_and_links($rows, 'id', $types, $filter_order, "type");
		$this->set("types", $rows);		
		
		//******************************************************************
		//// Treść
		$content = array();
		$content[] = array("name" => "bez treści", "link" => "no_content");
		$this->set("content", $content);
		
		//// Types
		$sql = "SELECT an.type as id, an.type as name, COUNT(DISTINCT r.id) as count" .
				" FROM reports_annotations an" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" WHERE r.corpora={$corpus['id']}" .
				( isset($sql_where_filtered['annotation']) ? $sql_where_filtered['annotation'] : $sql_where_filtered_general).
				" GROUP BY name" .
				" ORDER BY name ASC";
		if (PEAR::isError($r = $mdb2->query($sql))){
			die("<pre>".$r->getUserInfo()."</pre>");
		}
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		array_walk($rows, "array_map_replace_spaces");
		prepare_selection_and_links($rows, 'id', $annotations, $filter_order, "annotation");

//		$annotations_list[] = array("name" => "bez anotacji", "link" => "no_annotation", "count" => $count_no_annotation, "selected" => ($annotations == "no_annotation"));
		$this->set("annotations", $rows);
		
	}
}

/**
 * Przygotuj dla każdej pozycji odpowiedni link i kolejność sortowania. 
 */
function prepare_selection_and_links(&$rows, $column, $values, $filter_order, $attribute_name=""){
	$filter_order = is_array($filter_order) ? $filter_order : array();
	// Policz, ile atrybutów jest aktywnych
	$selected_all = true;
	$selected_any = false;
	$selected_count = 0;
	foreach ($rows as $id=>$row){
		$rows[$id]['selected'] = in_array($row[$column], $values) || count($values)==0;
		
		$selected_all = $rows[$id]['selected'] && $selected_all;
		$selected_any = $rows[$id]['selected'] || $selected_any;
		$selected_count += $rows[$id]['selected'] ? 1 : 0;
	}

	foreach ($rows as $id=>$row){
		if ($rows[$id]['selected']){
			if (count($values)==0)
				 $years_in_link = array($row[$column]);
			else
				$years_in_link = array_diff($values, array($row[$column]));

			// Kolejność sortowania
			if ($selected_count == 1) // tylko ta opcja jest zaznaczona
				$rows[$id]['filter_order'] = implode(",",$filter_order);
			elseif ($selected_all)
				$rows[$id]['filter_order'] = implode(",",array_filter(array_merge($filter_order, array($attribute_name)), "strval"));			
			else
				$rows[$id]['filter_order'] = implode(",",$filter_order);
			
		}else{
			$years_in_link = array_merge($values, array($row[$column]));
			if ($selected_any)
				$rows[$id]['filter_order'] = implode(",",$filter_order);
			else
				$rows[$id]['filter_order'] = implode(",",array_filter(array_merge($filter_order, array($attribute_name)), "strval"));
		}
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
	if (count($ors)>0)	
		return "(" . implode(" OR ", $ors) . ")";
	else
		return "";
}

/**
 * Tworzy stronicowanie.
 */
function create_pagging($rows_all, $limit, $p){
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
	return $page_map;	
}
?>
