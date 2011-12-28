<?php

class Page_report extends CPage{
	
	//var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $mdb2, $auth, $corpus, $user;
						
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
		$where = trim(stripslashes($_COOKIE["{$cid}_".'sql_where']));
		$where_prev = trim(stripslashes($_COOKIE["{$cid}_".'sql_where_prev']));
		$where_next = trim(stripslashes($_COOKIE["{$cid}_".'sql_where_next']));
		$join = stripslashes($_COOKIE["{$cid}_".'sql_join']);
		$group = stripcslashes($_COOKIE["{$cid}_".'sql_group']);
		$order = stripcslashes($_COOKIE["{$cid}_".'sql_order']);
		
		// Domyślne wartości dla wymaganych
		$order = strlen($order)==0 ? "r.id ASC" : $order; 
		
		// Walidacja parametrów
		// ******************************************************************************
		// List dostępnych podstron dla danego korpusu
		$subpages = DBReportPerspective::get_corpus_perspectives($cid, $user);
		
		$find = false;
		foreach ($subpages as $s)
			$find = $find || $s->id == $subpage;
		$subpage = $find ? $subpage : 'preview';

		if (!$id)
			header("Location: index.php?page=browse");
		
		// Zapisz parametry w sesjii
		// ******************************************************************************		
		setcookie("{$cid}_".'subpage', $subpage);
		setcookie('view', $view);
						
		$row = $this->load_report_ext($id, $corpus);
		
		/* Sprawdzenie, czy id raportu jest poprawny */
		if ( !isset($row['id'])){
			$this->set("invalid_report_id", true);
			return;
		}
		
		$access = hasAccessToReport($user, $row, $corpus);
		if ( $access !== true){
			$this->set("page_permission_denied", $access);
			return;
		}	
		
		/* Kontrola dostępu do podstron */
		if (!hasRole("admin") && !isCorpusOwner() ){
			if ( $subpage == "annotator" && !hasCorpusRole("annotate") ){
				$subpage = "";
				$this->set("page_permission_denied", "Brak dostępu do edytora anotacji");
			}
			else if ($subpage == "edit" && !hasCorpusRole("edit_documents") ){
				$subpage = "";
				$this->set("page_permission_denied", "Brak dostępu do edytora treści dokumentu");			
			}
			//return;
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
		
		if (!in_array($subpage,array('annotator_anaphora','annotator','autoextension','tokenization')) ){
			$this->set_annotations();
		}
		$this->set_flags();
		
		$this->set_up_navigation_links($id, $corpus['id'], $where, $join, $group, $order, $where_prev, $where_next);
		$this->set('row', $row);
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
		$subpage = $subpage ? $subpage : "preview";
		$perspective_class_name = "Perspective".ucfirst($subpage);
		$perspective = new $perspective_class_name($this, $row);
		$perspective->execute();					
	}

	/**
	 * 
	 */
	function set_up_navigation_links($id, $corpus_id, $where, $join, $group, $order, $where_next, $where_prev)
	{
		$order_reverse = str_replace(array("ASC", "DESC"), array("<<<", ">>>"), $order);
		$order_reverse = str_replace(array("<<<", ">>>"), array("DESC", "ASC"), $order_reverse);
		
		$row_first = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order LIMIT 1");
		$row_prev = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 1");
		$row_prev_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 9,10");
		$row_prev_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 99,100");

		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group";
		$row_prev_c = $group ? count(db_fetch_rows($sql)) : intval(db_fetch_one($sql));

		$row_last = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order_reverse LIMIT 1");
		$row_next = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 1");
		$row_next_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 9,10");		
		$row_next_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 99,100");			
		
		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group";
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
		$htmlStr = new HtmlStr($row['content'], true);
		$this->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->set('anns',$anns);
	}
	
	/**
	 * Load report with extended data.
	 */
	function load_report_ext($report_id, $corpus){
		if ($corpus['ext']){
			$sql = "SELECT r.*, e.*, r.id, rs.status AS status_name, rt.name AS type_name" .
					" FROM reports r" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" LEFT JOIN reports_ext_{$corpus['id']} e ON (r.id=e.id) " .
					" WHERE r.id={$report_id}";
		}else{
			$sql = "SELECT r.*, rs.status AS status_name, rt.name AS type_name" .
					" FROM reports r" .
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
	
	
}

?>


