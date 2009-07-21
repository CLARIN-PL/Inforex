<?php

class Page_report_html extends CPage{
	
	function execute(){
		global $mdb2;
		
		$id 	= intval($_GET['id']);
		$p 		= intval($_GET['p']);
		$edit 	= intval($_GET['edit']);
		
		if ($_POST['zapisz']){
			$status = intval($_POST['status']);
			$type = intval($_POST['type']);
			
			$sql = "UPDATE reports SET type = {$type}, status = {$status} WHERE id = {$id}";
			$mdb2->query($sql);
		}
		
		if ($_POST['formatowanie']){
			$content = $_POST['content'];
			$sql = "UPDATE reports SET content = '{$content}', formated=1 WHERE id = {$id}";
			$mdb2->query($sql);			
		}
		
		
		$result = $mdb2->query("SELECT * FROM reports WHERE id={$id}");
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$year = date("Y", strtotime($row['date']));
		$month = date("n", strtotime($row['date']));
		
		//$result = $mdb2->query("SELECT id FROM reports WHERE id<{$id} AND content!='' AND html_downloaded!='0000-00-00 00:00:00' AND skip=0 ORDER BY id DESC LIMIT 1");
		$result = $mdb2->query("SELECT id FROM reports WHERE id<{$id} ORDER BY id DESC LIMIT 1");
		$row_prev = $result->fetchOne();
		
		//$result = $mdb2->query("SELECT id FROM reports WHERE id>{$id} AND content!='' AND html_downloaded!='0000-00-00 00:00:00' AND skip=0 ORDER BY id ASC LIMIT 1");
		$result = $mdb2->query("SELECT id FROM reports WHERE id>{$id} ORDER BY id ASC LIMIT 1");
		$row_next = $result->fetchOne();
		
		$sql = "SELECT * FROM reports_types ORDER BY name";
		$select_type = new HTML_Select('type');
		$select_type->loadQuery($mdb2, $sql, 'name', 'id', $row['type']);

		$sql = "SELECT * FROM reports_statuses ORDER BY status";
		$select_status = new HTML_Select('status');
		$select_status->loadQuery($mdb2, $sql, 'status', 'id', $row['status']);
					 						
		$this->set('row_prev', $row_prev);
		$this->set('row_next', $row_next);
		$this->set('row', $row);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('p', $p);
		$this->set('select_type', $select_type->toHtml());
		$this->set('select_status', $select_status->toHtml());
		$this->set('edit', $edit);
		$this->set('content', htmlentities($row['content'],ENT_COMPAT,'utf-8'));
		
		if ( ($row['status'] == 2 && $row['formated'] == 0) || $edit==1 ){
			$content = $row['content'];
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
						$content_chunks[] = "<p>".implode("\n<br/>\n", $content_chunk_br)."</p>\n";
						$content_chunk_br = array();
					}
				}
			}
			// Ostatni element
			if (count($content_chunk_br)>0){
				$content_chunks[] = "<p>".implode("<br/>\n", $content_chunk_br)."</p>\n";
				$content_chunk_br = array();
			}
			
			$content_formated = trim(implode("\n", $content_chunks));
			$this->set('content_formated', $content_formated);			
		}

		require_once(PATH_ENGINE."/marginalia-php/config.php");
		require_once(PATH_ENGINE."/marginalia-php/embed.php");
		$this->set('marginalia_js', listMarginaliaJavascript());
	}
}

?>


