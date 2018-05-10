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

		$select_type = DbReport::getReportTypes();
		$select_status = DbReport::getReportStatuses();
		$select_format = DbReport::getAllFormatsByName();
		
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

		$this->page->set('select_type', $select_type);
		$this->page->set('selected_type', $this->document['type']);

		$this->page->set('select_status', $select_status);
        $this->page->set('selected_status', $this->document['status']);

		$this->page->set('select_format', $select_format);
        $this->page->set('selected_format', $this->document['format_id']);
		$this->page->set('annotations_count', $annotations_count);
		$this->page->set('content_edit', $content);
	}
}

?>
