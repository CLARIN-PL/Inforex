<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveCleanup extends CPerspective {

    const FULL_EDIT_MAX_CONTENT_LENGTH = 150000;
    const CODEMIRROR_MAX_CONTENT_LENGTH = 200000;

    function execute()
	{
		$this->set_dropdown_lists();
	}

	function set_dropdown_lists()
	{
		$edit_type = array_key_exists('edit_type', $_COOKIE) ? $_COOKIE['edit_type'] : "full";
        $useCodeMirror = array_key_exists('edit_use_codemirror', $_COOKIE) ? $_COOKIE['edit_use_codemirror'] === "1" : false;
		
		$sql = "SELECT * FROM reports_types ORDER BY name";
		$select_type = new HTML_Select('type', 1, false, array("id"=>"report_type"));
		$select_type->loadArray($this->page->getDb()->fetch_assoc_array($sql, 'name', 'id'), $this->document['type']);

		$sql = "SELECT * FROM reports_statuses ORDER BY status";
		$select_status = new HTML_Select('status');
		$select_status->loadArray($this->page->getDb()->fetch_assoc_array($sql, 'status', 'id'), $this->document['status']);

		$select_format = new HTML_Select('format');
		$select_format->loadArray(DbReport::getAllFormatsByName(), array($this->document['format_id']));

		$sql = "SELECT COUNT(*) FROM reports_annotations WHERE report_id = ?";
		$annotations_count = $this->page->getDb()->fetch_one($sql, $this->document['id']);
        $content = $this->document['content'];
        $contentLength = strlen($content);
        $hasAnnotations = intval($annotations_count) > 0;
        $fullEditDisabledReason = null;
        $disableCodeMirror = !$useCodeMirror || $contentLength > self::CODEMIRROR_MAX_CONTENT_LENGTH;

		try{
            if ($hasAnnotations && $edit_type !== 'no_annotation') {
                $edit_type = 'no_annotation';
                $fullEditDisabledReason = "Full cleanup mode was disabled automatically because this document already has annotations.";
            } elseif ($contentLength > self::FULL_EDIT_MAX_CONTENT_LENGTH && $edit_type !== 'no_annotation') {
                $edit_type = 'no_annotation';
                $fullEditDisabledReason = "Full cleanup mode was disabled automatically because this document is too large.";
            }

			if($edit_type != 'no_annotation'){
				$htmlStr = new HtmlStr2($content, true);
				$sql = "SELECT * FROM reports_annotations WHERE report_id = ?";
				$ans = $this->page->getDb()->fetch_rows($sql, array($this->document['id']));
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
        $this->page->set('disable_codemirror', $disableCodeMirror);
        $this->page->set('use_codemirror', $useCodeMirror ? 1 : 0);
        $this->page->set('content_edit_length', $contentLength);
        $this->page->set('full_edit_disabled_reason', $fullEditDisabledReason);
	}
}

?>
