<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_report extends CPage{
	
	//var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $mdb2, $auth, $corpus, $user, $config;
						
		$cid = $corpus['id'];
		$this->cid = $cid;
		
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$id 	= intval($_GET['id']);
		$this->id = $id;
		$p 		= intval($_GET['p']);
		$edit 	= intval($_GET['edit']);
		$subpage = array_key_exists('subpage', $_GET) ? $_GET['subpage'] : $_COOKIE["{$cid}_".'subpage'];
		$this->subpage = $subpage;
		$view = array_key_exists('view', $_GET) ? $_GET['view'] : $_COOKIE["{$cid}_".'view'];
		$where = trim($_COOKIE["{$cid}_".'sql_where']);
		$join = stripslashes($_COOKIE["{$cid}_".'sql_join']);
		$group = stripcslashes($_COOKIE["{$cid}_".'sql_group']);
		$order = stripcslashes($_COOKIE["{$cid}_".'sql_order']);
		
		// Domyślne wartości dla wymaganych
		$order = strlen($order)==0 ? "r.id ASC" : $order; 
        // domyślne sortowanie w przypadku losowej kolejności
        if (substr($order, 0, 5) === 'RAND(') {
             $order = 'r.id ASC';
        }
		
		// Walidacja parametrów
		// ******************************************************************************
		// List dostępnych podstron dla danego korpusu
		$subpages = DBReportPerspective::get_corpus_perspectives($cid, $user);
		
		if ( $subpage == "unassigned"  || $subpage == "noaccess" ||$subpage == "" ){
			$subpage = "preview";
		}
		
		$find = false;
		foreach ($subpages as $s){
			$find = $find || $s->id == $subpage;
		}
		
		if ( !$find && $subpage != ""
				 && ( hasCorpusRole(CORPUS_ROLE_MANAGER) || isCorpusOwner() ) ){
				$this->set("unassigned_subpage", $subpage);
				$subpage = 'unassigned';								
		}		
		else if ( !$find ){
			$perspectives = DBReportPerspective::get_corpus_perspectives($cid, $user);
			$subpage = count($perspectives) > 0 ? strtolower($perspectives[0]->id) : 'noaccess';
		}					

		if (!$id){
			header("Location: index.php?page=browse");
		}
		
		// Zapisz parametry w sesjii
		// ******************************************************************************
		if ( $subpage != "unassigned" ){		
			setcookie("{$cid}_".'subpage', $subpage);
		}
		setcookie('view', $view);
						
		$row = $this->load_report_ext($id, $corpus);
		
		/* Sprawdzenie, czy id raportu jest poprawny */
		if ( !isset($row['id'])){
			$this->set("invalid_report_id", true);
			return;
		}
		
		// Sprawdzenie czy id raportu znajduje się w danym korpusie
		if(!count(DbReport::getReportsByCorpusIdWithParameters($cid,' * ', '', ' AND r.id=' . $id . ' ',''))){
			$corpus_id = DbCorpus::getCorpusByReportId($id);
			
			$new_url = 'index.php?';
			$i = 0;
			foreach($_GET as $key => $values){
				if($i)
					$new_url .= '&';
				if($key == 'corpus')
					$new_url .= 'corpus=' . $corpus_id;
				else
					$new_url .= $key . '=' . $values;
				$i++;
			}

			$this->redirect($new_url);
		}
		
		$access = hasAccessToReport($user, $row, $corpus);
		if ( $access !== true){
			$this->set("page_permission_denied", $access);
			return;
		}	
		
		/* Kontrola dostępu do podstron */
		if (!hasRole("admin") && !isCorpusOwner() ){
			if ( $subpage == "annotator" 
					&& !(hasCorpusRole(CORPUS_ROLE_ANNOTATE) || hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT)) ){
				$subpage = "";
				$this->set("page_permission_denied", "Brak dostępu do edytora anotacji");
			}
			else if ($subpage == "edit" && !hasCorpusRole("edit_documents") ){
				$subpage = "";
				$this->set("page_permission_denied", "Brak dostępu do edytora treści dokumentu");			
			}
		}		
		 		 
		// Dodanie nazwy podkorpusu jeżeli dokument jest do niego przypisany   		 
		if($row['subcorpus_id']){
			$subcorpus_name = $this->get_subcorpus_name($row['subcorpus_id']); 
			$row['subcorpus_name'] = $subcorpus_name;
		}
						
		$this->row = $row;
		
		// Ustal warunki wyboru następnego/poprzedniego
		$fields = explode(" ", $order);
		$column = str_replace("r.", "", $fields[0]);
		$where_next = "r.$column < '{$row[$column]}'";
		$where_prev = "r.$column > '{$row[$column]}'";
		
		$year = date("Y", strtotime($row['date']));
		$month = date("n", strtotime($row['date']));
				
		// Lista adnoatcji
		$annotations = null;
		if ($subpage!="preview"){
			$annotations = db_fetch_rows("SELECT a.*, u.screename" .
					" FROM reports_annotations a" .
					" JOIN annotation_types t " .
						" ON (a.type=t.name)" .
					" LEFT JOIN users u USING (user_id)" .
					" WHERE a.report_id=$id");		
		}
		
		if (!in_array($subpage,array('annotator_anaphora','preview','annotator','autoextension','tokenization')) ){
			$this->set_annotations();
		}
		$this->set_flags();

		$this->set_up_navigation_links($id, $corpus['id'], $where, $join, $group, $order, $where_prev, $where_next);
		$this->set('row', $row); // ToDo: do wycofania, zastąpione przez report
		$this->set('report', $row);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('p', $p);
		$this->set('status', $row['status']);
		$this->set('edit', $edit);
		$this->set('view', $view);
		$this->set('subpage', $subpage);
		$this->set('subpage_file', "inc_report_{$subpage}.tpl");
		$this->set('content_formated', reformat_content($row['content']));
		$this->set('annotations', $annotations);
		
		$this->set('subpages', $subpages);
		$this->set('report_id',$id);
	 	
		// Load and execute the perspective 
		$perspective_class_name = "Perspective".ucfirst($subpage);

		if (class_exists($perspective_class_name)){
			$perspective = new $perspective_class_name($this, $row);
			$perspective->execute();
		}else{
			$perspective_class_name = "Perspective".ucfirst("noaccess");
			$this->set("error", "Perspective $subpage does not exist");
		}
	}

	/**
	 * 
	 */
	function set_up_navigation_links($id, $corpus_id, $where, $join, $group, $order, $where_next, $where_prev)
	{
		global $db;
		
		$reportIds = array();
		foreach (DbReport::getReportsByCorpusId($corpus_id, 'id') as $row)
			array_push($reportIds, $row['id']);
		
		/// Flagi
		$flags_names = DbCorpus::getCorpusFlags($corpus_id);
		$flag_array = array();
		$flags_not_ready_map = array();

		foreach($flags_names as $key => $flag_name){
			$flag_name_str = 'flag_' . str_replace(' ', '_', $flag_name['short']);
			$flag_array[$key]['flag_name'] = $flag_name['short'];
			$flag_array[$key]['no_space_flag_name'] = $flag_name_str;
			$flag_array[$key]['value'] = array_key_exists("{$corpus_id}_".$flag_name_str, $_COOKIE) ? $_COOKIE["{$corpus_id}_".$flag_name_str] : NULL;
			$flags_not_ready_map[$flag_name['short']] = array(); 			 
		}
		
		foreach($flag_array as $key => $value)
			$flag_array[$key]['data'] = array_filter(explode(",", $flag_array[$key]['value']), "intval");

		$flags_count = array(); // Ilość aktywnych flag 
		$flag_not_ready = array(); // Filtrowanie po fladze niegotowy
		foreach($flag_array as $key => $value){
			if (count($flag_array[$key]['data'])){
				$flags_count[] = $key;
				if (in_array('-1', $flag_array[$key]['data'])) $flag_not_ready[] = $flag_array[$key];
			}	
		}
		
		$where_flags = array();
		if(count($flags_count)){ 
			$sql = "SELECT f.flag_id as id FROM flags f WHERE f.flag_id>0 ";  	
			$rows_flags = $db->fetch_rows($sql);
			foreach($rows_flags as $key => $row_flag)
				$rows_flags[$key] = $row_flag['id'];
							
			foreach($flags_count as $value){
				$where_data = array();
				if(in_array('-1', $flag_array[$value]['data'])){
					if(count($flag_array[$value]['data']) > 1){
						foreach($flag_array[$value]['data'] as $data)
							if($data != '-1')
								$where_data[] = $data;
						$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . $this->where_or("f.flag_id", $where_data) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
					}
					else{
						$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . $this->where_or("f.flag_id", array('-1')) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
					}
				}	 
				else{
					$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . $this->where_or("f.flag_id", $flag_array[$value]['data']) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
				}
			}
		
			$sql = "SELECT r.id AS id, cf.short as name ".
					"FROM reports r " .
  					"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  					"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
    				"WHERE r.id IN  ('". implode("','",$reportIds) ."') ";
			$rows_flags_not_ready = $db->fetch_rows($sql);
  			
			foreach ($rows_flags_not_ready as $row_flags_not_ready)
				$flags_not_ready_map[$row_flags_not_ready['name']][] = $row_flags_not_ready['id'];
			
			foreach($flag_not_ready as $flag_not){
				$reports_ids_flag_not_ready[$flag_not['flag_name']] = array();
				foreach($reportIds as $repId)
					if(!in_array($repId,$flags_not_ready_map[$flag_not['flag_name']]))
						if(!in_array($repId,$reports_ids_flag_not_ready[$flag_not['flag_name']]))
							$reports_ids_flag_not_ready[$flag_not['flag_name']][] = $repId;
			}
			$reportIdsByFlags = $reportIds;
			foreach($flags_count as $flags_where){
				if(isset($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']]))
					foreach($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']] as $key => $flag_not_ready_rep)
						if(!in_array($flag_not_ready_rep,$reportIdsByFlags))
							unset($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']][$key]);
					
				$sql = "SELECT r.id AS id  ".
	  					"FROM reports r " .
  						"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  						"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  						"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
	  					"WHERE r.id IN  ('". implode("','",$reportIdsByFlags) ."') " .
	  					$where_flags[$flag_array[$flags_where]['no_space_flag_name']] .
  						" GROUP BY r.id " .
  						" ORDER BY r.id ASC " ;
				$rows_flags = $db->fetch_rows($sql);
				$reportIdsByFlags = array();
				foreach ($rows_flags as $row)
					array_push($reportIdsByFlags, $row['id']);				
				
				if(isset($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']]))
					foreach($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']] as $flag_not_ready_rep)
						if(!in_array($flag_not_ready_rep, $reportIdsByFlags))
							array_push($reportIdsByFlags, $flag_not_ready_rep);
			}
			
			foreach ($reportIds as $key => $row)
				if(!in_array($row, $reportIdsByFlags))
					unset($reportIds[$key]);
		}
		
		$order_reverse = str_replace(array("ASC", "DESC"), array("<<<", ">>>"), $order);
		$order_reverse = str_replace(array("<<<", ">>>"), array("DESC", "ASC"), $order_reverse);
		
		$row_first = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order LIMIT 1");
		$row_prev = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 1");
		$row_prev_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 9,10");
		$row_prev_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 99,100");

		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_prev $group";
		$row_prev_c = $group ? count(db_fetch_rows($sql)) : intval(db_fetch_one($sql));

		$row_last = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order_reverse LIMIT 1");
		$row_next = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 1");
		$row_next_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 9,10");		
		$row_next_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 99,100");			
		
		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.id IN  ('". implode("','",$reportIds) ."') AND r.corpora = $corpus_id $where AND $where_next $group";
		$row_next_c = $group ? count(db_fetch_rows($sql)) : intval(db_fetch_one($sql));
		
		$this->set('row_prev_c', $row_prev_c);
		$this->set('row_number', $row_prev_c + 1);
		$this->set('row_first', $row_first);
		$this->set('row_prev', $row_prev);
		$this->set('row_prev_10', $row_prev_10);
		$this->set('row_prev_100', $row_prev_100);
		$this->set('row_last', $row_last);
		$this->set('row_next', $row_next);
		$this->set('row_next_10', $row_next_10);
		$this->set('row_next_100', $row_next_100);
		$this->set('row_next_c', $row_next_c);		
	}
	
	function set_flags(){
		/*****flags******/
		$sql = "SELECT corpora_flags.corpora_flag_id AS id, corpora_flags.name, corpora_flags.short, reports_flags.flag_id, flags.name AS fname " .
				"FROM corpora_flags " .
				"LEFT JOIN reports_flags " .
					"ON corpora_flags.corpora_id={$this->cid} " .
					"AND reports_flags.report_id={$this->id} " .
					"AND corpora_flags.corpora_flag_id=reports_flags.corpora_flag_id " .
				"LEFT JOIN flags " .
					"ON reports_flags.flag_id=flags.flag_id " .
				"WHERE corpora_flags.corpora_id={$this->cid}" .
				" ORDER BY sort";
		$corporaflags = db_fetch_rows($sql);
		$sql = "SELECT flag_id AS id, name FROM flags ";
		$flags = db_fetch_rows($sql);
		$this->set('corporaflags',$corporaflags);
		$this->set('flags',$flags);
	}
	
	function set_annotations(){
		$row = $this->row;
		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text, t.group_id, ans.description setname, ansub.description subsetname, ansub.annotation_subset_id, t.name typename, t.short_description typedesc, an.stage, t.css, an.source"  .
				" FROM reports_annotations an" .
				" LEFT JOIN annotation_types t ON (an.type=t.name)" .
				" LEFT JOIN annotation_subsets ansub ON (t.annotation_subset_id=ansub.annotation_subset_id)" .
				" LEFT JOIN annotation_sets ans on (t.group_id=ans.annotation_set_id)" .
				" WHERE report_id = {$row['id']} ";
		$sql = $sql . " ORDER BY `from` ASC, `level` DESC"; 
		$anns = db_fetch_rows($sql);
		try{
			$htmlStr = new HtmlStr2($row['content'], true); //akaczmarek: można dodać sprawdzenie czy format nie jest ustawiony na 'plain'
			$this->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
			$this->set('anns',$anns);
		}
		catch(Exception $ex){
			$this->set("error", $ex->getMessage());
		}		
	}
	
	/**
	 * Load report with extended data.
	 */
	function load_report_ext($report_id, $corpus){
		if ($corpus['ext']){
			$sql = "SELECT r.*, e.*, r.id, rs.status AS status_name, rt.name AS type_name, rf.format" .
					" FROM reports r" .
					" JOIN reports_formats rf ON (r.format_id = rf.id)" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" LEFT JOIN {$corpus['ext']} e ON (r.id=e.id) " .
					" WHERE r.id={$report_id}";
		}else{
			$sql = "SELECT r.*, rs.status AS status_name, rt.name AS type_name, rf.format" .
					" FROM reports r" .
					" JOIN reports_formats rf ON (r.format_id = rf.id)" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" WHERE r.id={$report_id}";
		}
		return db_fetch($sql);		
	}
	
	function get_subcorpus_name($subcorpus_id){
		global $db;
		$sql = "SELECT cs.name AS name FROM corpus_subcorpora cs WHERE cs.subcorpus_id=? ";
		return $db->fetch_one($sql, array($subcorpus_id));
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
	
}

?>


