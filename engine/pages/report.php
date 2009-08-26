<?php

function preg_annotation_callback($match){
	global $mdb2;
	$report_id 	= intval($_GET['id']);
	
	$sql = "INSERT INTO reports_annotations (`report_id`,`type`,`text`) VALUES("
			."'" . mysql_escape_string($report_id) . "', "
			."'" . mysql_escape_string($match[1]) . "', "
			."'" . mysql_escape_string($match[2]) . "');";
	$mdb2->query($sql);
	$an_id = mysql_insert_id();	
	$return = "<an#$an_id:{$match[1]}>{$match[2]}</an>";
	return $return;
}

class Page_report extends CPage{
	
	function execute(){
		global $mdb2;
		
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$id 	= intval($_GET['id']);
		$p 		= intval($_GET['p']);
		$edit 	= intval($_GET['edit']);
		$subpage = array_key_exists('subpage', $_GET) ? $_GET['subpage'] : HTTP_Session2::get('subpage');
		$view = array_key_exists('view', $_GET) ? $_GET['view'] : HTTP_Session2::get('view');
		$where = HTTP_Session2::get('sql_where');
		
		// Walidacja parametrów
		// ******************************************************************************		
		if (!in_array($subpage, array('preview','html','raw','edit','edit_raw')))
			$subpage = 'preview';
		
		// Zapisz parametry w sesjii
		// ******************************************************************************		
		HTTP_Session2::set('subpage', $subpage);
		HTTP_Session2::set('view', $view);
		
		if ($_POST['formatowanie']){
			// Uaktualnij formatowanie raportu
			$content = $_POST['content'];			
			$content = preg_replace_callback('/<an:([a-z]+)>([^<]+)<\/an>/', "preg_annotation_callback", $content);
			$sql = "UPDATE reports SET content = '{$content}', formated=1 WHERE id = {$id}";
			$mdb2->query($sql);
			
			// Uaktualnij status i typ raportu
			$status = intval($_POST['status']);
			$type = intval($_POST['type']);			
			$sql = "UPDATE reports SET type = {$type}, status = {$status} WHERE id = {$id}";
			$mdb2->query($sql);
						
		}
		
		
		$result = $mdb2->query("SELECT r.*, rs.status AS status_name, rt.name AS type_name" .
				" FROM reports r" .
				" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
				" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
				" WHERE r.id={$id}");
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$year = date("Y", strtotime($row['date']));
		$month = date("n", strtotime($row['date']));
		
		$sql = "SELECT r.id" .
				" FROM reports r" .
				$where .
				($where=="" ? " WHERE " : " AND ") ."r.id<{$id}" .
				" ORDER BY r.id DESC LIMIT 1";
		$row_prev = $mdb2->query($sql)->fetchOne();

		$sql = "SELECT COUNT(*)" .
				" FROM reports r" .
				$where .
				($where=="" ? " WHERE " : " AND ") ."r.id<{$id}";
		$row_prev_c = $mdb2->query($sql)->fetchOne();
		
		$sql = "SELECT r.id" .
				" FROM reports r" .
				$where .
				($where=="" ? " WHERE " : " AND ") ."r.id>{$id}" .
				" ORDER BY r.id ASC LIMIT 1";
		$row_next = $mdb2->query($sql)->fetchOne();
		
		$sql = "SELECT COUNT(*)" .
				" FROM reports r" .
				$where .
				($where=="" ? " WHERE " : " AND ") ."r.id>{$id}";
		$row_next_c = $mdb2->query($sql)->fetchOne();
		
		
		$sql = "SELECT * FROM reports_types ORDER BY name";
		$select_type = new HTML_Select('type');
		$select_type->loadQuery($mdb2, $sql, 'name', 'id', $row['type']);

		$sql = "SELECT * FROM reports_statuses ORDER BY status";
		$select_status = new HTML_Select('status');
		$select_status->loadQuery($mdb2, $sql, 'status', 'id', $row['status']);
					 						
		$this->set('row_prev', $row_prev);
		$this->set('row_prev_c', $row_prev_c);
		$this->set('row_next', $row_next);
		$this->set('row_next_c', $row_next_c);
		$this->set('row', $row);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('p', $p);
		$this->set('select_type', $select_type->toHtml());
		$this->set('select_status', $select_status->toHtml());
		$this->set('edit', $edit);
		$this->set('view', $view);
		$this->set('subpage', $subpage);
		$this->set('subpage_file', "inc_report_{$subpage}.tpl");
		
		if ( $subpage == 'edit' || $subpage == 'edit_raw'){
			$this->set('content_formated', $this->reformat_content($row['content']));			
		}

		//require_once(PATH_ENGINE."/marginalia-php/config.php");
		//require_once(PATH_ENGINE."/marginalia-php/embed.php");
		//$this->set('marginalia_js', listMarginaliaJavascript());
	}
	
	function reformat_content($content){
		//$content = html_entity_decode($content);
		$content = str_replace("<br>", "<br/>", $content);
		$content_br = explode("<br/>", $content);
		
		$content_chunks = array();
		$content_chunk_br = array();
		foreach ($content_br as $br){
			$br = trim($br);
			if ($br){
				$content_chunk_br[] = $br;
			}else{
				if (count($content_chunk_br)>0){
					$lines = implode("\n<br/>\n", $content_chunk_br);
					if (substr($lines, 0, 3) != "<p>")
						$lines = "<p>$lines</p>\n";
					$content_chunks[] = $lines;
					$content_chunk_br = array();
				}
			}
		}
		// Ostatni element
		if (count($content_chunk_br)>0){
			$lines = implode("\n<br/>\n", $content_chunk_br);
			if (substr($lines, 0, 3) != "<p>")
				$lines = "<p>$lines</p>";
			$content_chunks[] = $lines;
			$content_chunk_br = array();
		}
		
		$content_formated = trim(implode("\n", $content_chunks));
		return $content_formated;
	}
}

?>


