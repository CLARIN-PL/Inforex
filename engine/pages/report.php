<?php

class Page_report extends CPage{
	
	var $isSecure = false;
	
	function checkPermission(){
		global $corpus;
		return true;
	}
	
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
		
		// Ustal warunki wyboru następnego/poprzedniego
		$fields = explode(" ", $order);
		$column = str_replace("r.", "", $fields[0]);
		$where_next = "r.$column < '{$row[$column]}'";
		$where_prev = "r.$column > '{$row[$column]}'";
		
		$year = date("Y", strtotime($row['date']));
		$month = date("n", strtotime($row['date']));
				
		// Lista adnoatcji
		$annotations = db_fetch_rows("SELECT a.*, u.screename" .
				" FROM reports_annotations a" .
				" JOIN annotation_types t ON (a.type=t.name)" .
				" LEFT JOIN users u USING (user_id)" .
				" WHERE a.report_id=$id");
	
		$allCount = db_fetch_one("SELECT count(id) cnt FROM reports_annotations WHERE report_id = {$row['id']}");
		setcookie('allcount',$allCount);

		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text, t.group_id, ans.description setname, ansub.description subsetname, t.name typename" .
				" FROM reports_annotations an" .
				" LEFT JOIN annotation_types t ON (an.type=t.name)" .
				" LEFT JOIN annotation_subsets ansub ON (t.annotation_subset_id=ansub.annotation_subset_id)" .
				" LEFT JOIN annotation_sets ans on (t.group_id=ans.annotation_set_id)" .
				" WHERE report_id = {$row['id']}" .
				" ORDER BY `from` ASC, `level` DESC"; 
		
		if ($_COOKIE['clearedLayer'] && $_COOKIE['clearedLayer']!="{}"){
			$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text, t.group_id, ans.description setname, ansub.description subsetname, t.name typename" .
					" FROM reports_annotations an" .
					" LEFT JOIN annotation_types t ON (an.type=t.name)" .
					" LEFT JOIN annotation_subsets ansub ON (t.annotation_subset_id=ansub.annotation_subset_id)" .
					" LEFT JOIN annotation_sets ans on (t.group_id=ans.annotation_set_id)" .
					" WHERE report_id = {$row['id']}" .
					" AND group_id NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedLayer']) . ")" . 
					" ORDER BY `from` ASC, `level` DESC";
		} 
		$anns = db_fetch_rows($sql);
		
		
		$annotation_set_map = array();
		foreach ($anns as $as){
			$setName = $as['setname'];
			$subsetName = $as['subsetname']==NULL ? "!uncategorized" : $as['subsetname'];
			$anntype = $as['typename'];
			if ($annotation_set_map[$setName][$subsetName][$anntype]==NULL){
				$annotation_set_map[$setName][$subsetName][$anntype] = array();
				$annotation_set_map[$setName]['groupid']=$as['group_id'];
			}
			array_push($annotation_set_map[$setName][$subsetName][$anntype], $as);
		}
		
		$exceptions = array();
		$htmlStr = new HtmlStr($row['content'], true);
		foreach ($anns as $ann){
			try{
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", $ann['id'], $ann['type'], $ann['group_id']), $ann['to']+1, "</an>");
				
			}catch (Exception $ex){
				$exceptions[] = sprintf("Annotation could not be displayed due to invalid border [%d,%d,%s]", $ann['from'], $ann['to'], $ann['text']);
				if ($ann['from'] == $ann['to'])
					$htmlStr->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
				else{				
					$htmlStr->insertTag($ann['from'], "<b class='invalid_border_start' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					for ($i=$ann['from']+1; $i<$ann['to']; $i++)				
						$htmlStr->insertTag($i, "<b class='invalid_border_middle' title='$i'>", $i+1, "</b>");
					$htmlStr->insertTag($ann['to'], "<b class='invalid_border_end' title='{$ann['to']}'>", $ann['to']+1, "</b>");
				}				
			}
		}
		if ( count($exceptions) > 0 )
			$this->set("exceptions", $exceptions);
		
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
		$this->set('sets', $annotation_set_map);
		$this->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->set('content_edit', $htmlStr->getContent());
		$this->set('subpages', $subpages);

		// Load and execute the perspective 
		$subpage = $subpage ? $subpage : "preview";
		$perspective_class_name = "Perspective".ucfirst($subpage);
		$perspective = new $perspective_class_name($this, $row);
		$perspective->execute();			
	}

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
}

?>


