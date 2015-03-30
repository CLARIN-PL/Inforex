<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveEdit extends CPerspective {
	
	function execute()
	{
		$this->set_dropdown_lists();
	}

	function set_dropdown_lists()
	{
		global $mdb2;
		
		$edit_type = array_key_exists('edit_type', $_COOKIE) ? $_COOKIE['edit_type'] : "full";
		
		$sql = "SELECT * FROM reports_types ORDER BY name";
		$select_type = new HTML_Select('type', 1, false, array("id"=>"report_type"));
		$select_type->loadQuery($mdb2, $sql, 'name', 'id', $this->document['type']);

		$sql = "SELECT * FROM reports_statuses ORDER BY status";
		$select_status = new HTML_Select('status');
		$select_status->loadQuery($mdb2, $sql, 'status', 'id', $this->document['status']);

		$select_format = new HTML_Select('format');
		$select_format->loadArray(DbReport::getAllFormatsByName(), array($this->document['format_id']));
		
		
		
		$sql = "SELECT COUNT(*) FROM reports_annotations WHERE report_id = ?";
		$annotations_count = db_fetch_one($sql, $this->document[id]);

		try{
			$content = $this->document['content'];

			if($edit_type != 'no_annotation'){
				$htmlStr = new HtmlStr2($content, true);
				$sql = "SELECT * FROM reports_annotations WHERE report_id = ?";
				$ans = db_fetch_rows($sql, array($this->document['id']));
				foreach ($ans as $a){
					try{
						$htmlStr->insertTag(intval($a['from']), sprintf("<anb id=\"%d\" type=\"%s\"/>", $a['id'], $a['type']), $a['to']+1, sprintf("<ane id=\"%d\"/>", $a['id']), TRUE);
					}
					catch(Exception $ex){
						$this->page->set("ex", $ex);
					}
				}
				$content = $htmlStr->getContent();
			}				
		}
		catch(Exception $ex){
			$this->page->set("ex", $ex);
		}
		
		$this->page->set('active_edit_type', $edit_type);								 						
		$this->page->set('active_edit_type', $edit_type);								 						
		$this->page->set('select_type', $select_type->toHtml());
		$this->page->set('select_status', $select_status->toHtml());
		$this->page->set('select_format', $select_format->toHtml());
		$this->page->set('annotations_count', $annotations_count);
		$this->page->set('content_edit', $content);
	}
}

?>
