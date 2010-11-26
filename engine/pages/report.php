<?php

class Page_report extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2, $auth, $corpus, $user;
		
		$cid = $corpus['id'];
		
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$id 	= intval($_GET['id']);
		$p 		= intval($_GET['p']);
		$edit 	= intval($_GET['edit']);
		$subpage = array_key_exists('subpage', $_GET) ? $_GET['subpage'] : $_COOKIE["{$cid}_".'subpage'];
		$view = array_key_exists('view', $_GET) ? $_GET['view'] : $_COOKIE["{$cid}_".'view'];
		$where = trim(stripslashes($_COOKIE["{$cid}_".'sql_where']));
		$join = stripslashes($_COOKIE["{$cid}_".'sql_join']);
		$group = stripcslashes($_COOKIE["{$cid}_".'sql_group']);
		
		// Walidacja parametrów
		// ******************************************************************************
		// List dostępnych podstron dla danego korpusu
		$subpages = array('preview'=>'','html','raw','edit','edit_raw','annotator', 'takipi', 'tei');
		
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
		
		if ($corpus['ext']){
			$sql = "SELECT r.*, e.*, r.id, rs.status AS status_name, rt.name AS type_name" .
					" FROM reports r" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" LEFT JOIN reports_ext_{$corpus['id']} e ON (r.id=e.id) " .
					" WHERE r.id={$id}";
		}else{
			$sql = "SELECT r.*, rs.status AS status_name, rt.name AS type_name" .
					" FROM reports r" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" WHERE r.id={$id}";
		}
		$row = db_fetch($sql);
		
		$year = date("Y", strtotime($row['date']));
		$month = date("n", strtotime($row['date']));

				
		// Lista adnoatcji
		$annotations = db_fetch_rows("SELECT a.*, u.screename" .
				" FROM reports_annotations a" .
				" JOIN annotation_types t ON (a.type=t.name)" .
				" LEFT JOIN users u USING (user_id)" .
				" WHERE a.report_id=$id");
		
		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len" .
				" FROM reports_annotations an" .
				" LEFT JOIN annotation_types t ON (an.type=t.name)" .
				" WHERE report_id = {$row['id']}" .
				" ORDER BY `from` ASC, `level` DESC";
		$anns = db_fetch_rows($sql);
		$row['content'] = normalize_content($row['content']);

		try{
			$htmlStr = new HtmlStr(html_entity_decode($row['content'], ENT_COMPAT, "UTF-8"));
			foreach ($anns as $ann){
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");
			}
		}catch (Exception $ex){
			custom_exception_handler($ex);
		}
		
		// Kontrola dostępu do podstron
		if (!hasRole("admin") && !isCorpusOwner() ){
			if ( $subpage == "annotator" && !hasCorpusRole("annotate") ){
				$subpage = "";
				$this->set("page_permission_denied", "Brak dostępu do edytora anotacji");
			}else if ($subpage == "edit" && !hasCorpusRole("edit_documents") ){
				$subpage = "";
				$this->set("page_permission_denied", "Brak dostępu do edytora treści dokumentu");			
			}
		}

		$this->set_up_navigation_links($id, $corpus['id'], $where, $join, $group);
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
		$this->set('content_inline', $htmlStr->getContent());
		$this->set('subpages', $subpages);

		// Load and execute the perspective 
		$subpage = $subpage ? $subpage : "preview";
		$perspective_class_name = "Perspective".ucfirst($subpage);
		$perspective = new $perspective_class_name($this, $row);
		$perspective->execute();			
	}

	function set_up_navigation_links($id, $corpus_id, $where, $join, $group)
	{
		$row_first = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where $group ORDER BY r.id ASC LIMIT 1");
		$row_prev = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 1");
		$row_prev_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 9,10");
		$row_prev_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 99,100");

		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = $corpus_id $where AND r.id<{$id} $group ORDER BY r.id DESC";
		$row_prev_c = $group ? count(db_fetch_rows($sql)) : intval(db_fetch_one($sql));

		$row_last = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where $group ORDER BY r.id DESC LIMIT 1");		
		$row_next = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 1");
		$row_next_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 9,10");		
		$row_next_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 99,100");
		
		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = $corpus_id $where AND r.id>{$id} $group";
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
}

?>


