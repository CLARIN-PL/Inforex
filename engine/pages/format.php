<?php

class Page_format extends CPage{
	
	function execute(){
		global $mdb2;
		
		$id = intval($_GET['id']);
		$p = intval($_GET['p']);
		
		if ($_POST['zapisz']){
			$status = intval($_POST['status']);
			$type = intval($_POST['type']);
			
			$sql = "UPDATE reports SET type = {$type}, status = {$status} WHERE id = {$id}";
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
		
		$links = ($row_prev?'<a href="index.php?page=report&id='.$row_prev.'"><< poprzedni</a>':'poprzedni');
		$links .= " | ";
		$links .= ($row_next?'<a href="html.php?id='.$id.'">html</a>':'następny');
		$links .= " | ";
		$links .= ($row_next?'<a href="index.php?page=report&id='.$row_next.'">następny >></a>':'następny');
		$links = "<div>{$links}</div>";
			 
		$this->set('links', $links);
		$this->set('row', $row);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('p', $p);
		$this->set('select_type', $select_type->toHtml());
		$this->set('select_status', $select_status->toHtml());
	}
}

?>


