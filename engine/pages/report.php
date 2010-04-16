<?php

class HtmlStr{
	function __construct($content){
		$this->content = $content;
		$this->n = 0; // Numer pozycji w tekście
		$this->m = 0; // Numer znaku z pominięciem tagów html
	}
	function insert($pos, $text, $begin=true){
		if ($this->m>$pos){
			$this->n = 0;
			$this->m = 0;
		}		
		$hold_count = false;
		while ($this->m<$pos && $this->n<mb_strlen($this->content)){
			if (mb_substr($this->content, $this->n, 1)=="<") $hold_count = true;
			else if (mb_substr($this->content, $this->n, 1)==">") $hold_count = false;
			else if (!$hold_count) $this->m++;
			$this->n++;
		}
		if (mb_substr($this->content, $this->n, 1)=="<"){
			do{
				$this->n++;
			}while (mb_substr($this->content, $this->n, 1)!=">");
			$this->n++;
		}
		$this->content = mb_substr($this->content, 0, $this->n) . $text . mb_substr($this->content, $this->n);	
	}
	function getContent(){
		return $this->content;
	}
}


function str_html_insert($content, $pos, $text){
	$hold_count = false;
	$n = 0;
	$m = 0;
	//echo $pos;
	while ($m<$pos && $n<mb_strlen($content)){
		if (mb_substr($content, $n, 1)=="<") $hold_count = true;
		else if (mb_substr($content, $n, 1)==">") $hold_count = false;
		else if (!$hold_count) $m++;
		$n++;
	}
	return mb_substr($content, 0, $n) . $text . mb_substr($content, $n);	
}

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
		
		if (defined(IS_RELEASE)){
			$where  = ' WHERE YEAR(r.date)=2004 AND r.status=2 ';
		}
		
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
		setcookie('subpage', $subpage);
		setcookie('view', $view);
		
		$sql = "SELECT r.*, rs.status AS status_name, rt.name AS type_name" .
				" FROM reports r" .
				" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
				" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
				" WHERE r.id={$id}";
		if (PEAR::isError( $r = $mdb2->query($sql) )) die("<pre>{$r->getUserInfo()}</pre>");
		$row = $r->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$year = date("Y", strtotime($row['date']));
		$month = date("n", strtotime($row['date']));

		$sql = "SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where $group ORDER BY r.id ASC LIMIT 1";
		if (PEAR::isError( $r = $mdb2->query($sql) )) die("<pre>{$r->getUserInfo()}</pre>");
		$row_first = $r->fetchOne();
		
		$sql = "SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 1";
		if (PEAR::isError( $r = $mdb2->query($sql) )) die("<pre>{$r->getUserInfo()}</pre>");
		$row_prev = $r->fetchOne();

		$sql = "SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 9,10";
		if (PEAR::isError( $r = $mdb2->query($sql) )) die("<pre>{$r->getUserInfo()}</pre>");
		$row_prev_10 = $r->fetchOne();

		$sql = "SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id<{$id} $group ORDER BY r.id DESC LIMIT 99,100";
		$row_prev_100 = $mdb2->query($sql)->fetchOne();

		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id<{$id} $group ORDER BY r.id DESC";
		$row_prev_c = $group ? count($mdb2->query($sql)->fetchAll()) : intval($mdb2->query($sql)->fetchOne());

		$sql = "SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where $group ORDER BY r.id DESC LIMIT 1";
		$row_last = $mdb2->query($sql)->fetchOne();
		
		$sql = "SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 1";
		$row_next = $mdb2->query($sql)->fetchOne();
		
		$sql = "SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 9,10";
		$row_next_10 = $mdb2->query($sql)->fetchOne();
		
		$sql = "SELECT r.id FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id>{$id} $group ORDER BY r.id ASC LIMIT 99,100";
		$row_next_100 = $mdb2->query($sql)->fetchOne();
		
		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = {$corpus['id']} $where AND r.id>{$id} $group";
		$row_next_c = $group ? count($mdb2->query($sql)->fetchAll()) : intval($mdb2->query($sql)->fetchOne());
				
		$sql = "SELECT * FROM reports_types ORDER BY name";
		$select_type = new HTML_Select('type', 1, false, array("id"=>"report_type"));
		$select_type->loadQuery($mdb2, $sql, 'name', 'id', $row['type']);

		$sql = "SELECT * FROM reports_statuses ORDER BY status";
		$select_status = new HTML_Select('status');
		$select_status->loadQuery($mdb2, $sql, 'status', 'id', $row['status']);
					 						
		$group_id = intval($corpus['id']);
		$sql = "SELECT * FROM annotation_types WHERE group_id = $group_id ORDER BY name";
		$select_annotation_types = new HTML_Select('annotation_type', 1, false, array("id"=>"annotation_type", "disabled"=>"true"));
		$select_annotation_types->loadQuery($mdb2, $sql, 'name', 'name', "");					 						
					 			
		$annotation_types = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
					 						
		// Lista adnoatcji
		$sql = "SELECT a.*, u.screename FROM reports_annotations a LEFT JOIN users u USING (user_id) WHERE a.report_id=$id";
		$annotations = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC); 

		$sql = "SELECT r.title, r.id " .
				" FROM reports r" .
				" ".$join .
				" WHERE r.corpora={$corpus['id']} ".$where .
				" LIMIT 10";
		$reports = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);					 						
					 								
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
		$table_annotations = $mdb2->tableBrowserFactory("reports_annotations", "id");
		$table_annotations->addFilter('report_id', 'report_id', '=', $row['id']);
		$anns = $table_annotations->getRows()->fetchAll(MDB2_FETCHMODE_ASSOC);
		$row['content'] = normalize_content($row['content']);

		$htmlStr = new HtmlStr(html_entity_decode($row['content'], ENT_COMPAT, "UTF-8"));
		foreach ($anns as $ann){
			//echo $ann['from'].",".$ann['to']."<br>";
			$htmlStr->insert($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']));
			$htmlStr->insert($ann['to']+1, "</an>", false);
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
		$this->set('reports', $reports);
		$this->set('content_html', htmlspecialchars($content));
		$this->set('content_inline', $htmlStr->getContent());
	}
	
}

?>


