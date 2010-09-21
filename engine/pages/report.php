<?php

class Page_report extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2, $auth, $corpus;
		
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
		$pages = array('preview','html','raw','edit','edit_raw','annotator', 'takipi', 'tei');
		if (defined(IS_RELEASE) || !$auth->getAuth())
			$pages = array('preview', 'html', 'raw', 'takipi', 'tei');
		if (!in_array($subpage, $pages))
			$subpage = 'preview';

		if (!$id)
			header("Location: index.php?page=browse");
		
		// Zapisz parametry w sesjii
		// ******************************************************************************		
		setcookie("{$cid}_".'subpage', $subpage);
		setcookie('view', $view);
		
		//$report = new CReport($id);
		//$corpus = new CCorpus($report->corpora);
		//print_r($corpus);
		
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

		$row_first = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where $group ORDER BY r.id ASC LIMIT 1");
		$row_prev = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 1");
		$row_prev_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 9,10");
		$row_prev_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 99,100");

		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id<{$id} $group ORDER BY r.id DESC";
		$row_prev_c = $group ? count(db_fetch_rows($sql)) : intval(db_fetch_one($sql));

		$row_last = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where $group ORDER BY r.id DESC LIMIT 1");		
		$row_next = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 1");
		$row_next_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 9,10");		
		$row_next_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 99,100");
		
		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id>{$id} $group";
		$row_next_c = $group ? count(db_fetch_rows($sql)) : intval(db_fetch_one($sql));
				
		$sql = "SELECT * FROM reports_types ORDER BY name";
		$select_type = new HTML_Select('type', 1, false, array("id"=>"report_type"));
		$select_type->loadQuery($mdb2, $sql, 'name', 'id', $row['type']);

		$sql = "SELECT * FROM reports_statuses ORDER BY status";
		$select_status = new HTML_Select('status');
		$select_status->loadQuery($mdb2, $sql, 'status', 'id', $row['status']);
					 						
		$group_id = intval($corpus['id']);
		$sql = "SELECT * FROM annotation_types t JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id) WHERE c.corpus_id = {$corpus['id']} ORDER BY t.name";
		$select_annotation_types = new HTML_Select('annotation_type', 1, false, array("id"=>"annotation_type", "disabled"=>"true"));
		$select_annotation_types->loadQuery($mdb2, $sql, 'name', 'name', "");					 						
					 			
		$annotation_types = db_fetch_rows($sql);
					 						
		// Lista adnoatcji
		$sql = "SELECT a.*, u.screename FROM reports_annotations a LEFT JOIN users u USING (user_id) WHERE a.report_id=$id";
		$annotations = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC); 

		if ($subpage == "tei"){			
			try{
				$this->set('tei_header', TeiFormater::report_to_header($row));
				$this->set('tei_text', TeiFormater::report_to_text($row));
			}
			catch(Exception $ex){
				$this->set('structure_corrupted', 1);
			}
		}
		
		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len" .
				" FROM reports_annotations" .
				" WHERE report_id = {$row['id']}" .
				" ORDER BY `from` ASC";
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
		$this->set('row', $row);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('p', $p);
		$this->set('select_type', $select_type->toHtml());
		$this->set('select_status', $select_status->toHtml());
		$this->set('select_annotation_types', $select_annotation_types->toHtml());
		$this->set('status', $row['status']);
		$this->set('edit', $edit);
		$this->set('view', $view);
		$this->set('subpage', $subpage);
		$this->set('subpage_file', "inc_report_{$subpage}.tpl");
		$this->set('content_formated', reformat_content($row['content']));
		$this->set('annotations', $annotations);
		$this->set('annotation_types', $annotation_types);
		$this->set('content_html', htmlspecialchars($content));
		$this->set('content_inline', $htmlStr->getContent());
	}
	
}

?>


