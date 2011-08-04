<?php

class PerspectiveEdit extends CPerspective {
	
	function execute()
	{
		$this->set_dropdown_lists();
	}

	function set_dropdown_lists()
	{
		global $mdb2;
		
		$sql = "SELECT * FROM reports_types ORDER BY name";
		$select_type = new HTML_Select('type', 1, false, array("id"=>"report_type"));
		$select_type->loadQuery($mdb2, $sql, 'name', 'id', $this->document['type']);

		$sql = "SELECT * FROM reports_statuses ORDER BY status";
		$select_status = new HTML_Select('status');
		$select_status->loadQuery($mdb2, $sql, 'status', 'id', $this->document['status']);

		$sql = "SELECT COUNT(*) FROM reports_annotations WHERE report_id = ?";
		$annotations_count = db_fetch_one($sql, $this->document[id]);
					 						
		$this->page->set('select_type', $select_type->toHtml());
		$this->page->set('select_status', $select_status->toHtml());
		$this->page->set('annotations_count', $annotations_count);
	}
}

?>
