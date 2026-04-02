<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveEdit extends CPerspective {

    const FULL_EDIT_MAX_CONTENT_LENGTH = 150000;
    const FULL_EDIT_MAX_ANNOTATIONS = 2000;
    const CODEMIRROR_MAX_CONTENT_LENGTH = 200000;
	
	function execute()
	{
		$this->set_dropdown_lists();
	}

	function set_dropdown_lists()
	{
		$edit_type = array_key_exists('edit_type', $_COOKIE) ? $_COOKIE['edit_type'] : "full";
        $useCodeMirror = array_key_exists('edit_use_codemirror', $_COOKIE) ? $_COOKIE['edit_use_codemirror'] === "1" : false;

		$select_type = DbReport::getReportTypes();
		$select_status = DbReport::getReportStatuses();
		$select_format = DbReport::getAllFormatsByName();
		
		$sql = "SELECT COUNT(*) FROM reports_annotations WHERE report_id = ?";
		$annotations_count = $this->page->getDb()->fetch_one($sql, $this->document['id']);
        $content = $this->document['content'];
        $contentLength = strlen($content);
        $isFullEditTooHeavy = $contentLength > self::FULL_EDIT_MAX_CONTENT_LENGTH
            || intval($annotations_count) > self::FULL_EDIT_MAX_ANNOTATIONS;
        $disableCodeMirror = !$useCodeMirror || $contentLength > self::CODEMIRROR_MAX_CONTENT_LENGTH;
        $fullEditDisabledReason = null;

        if ($isFullEditTooHeavy && $edit_type !== 'no_annotation'){
            $edit_type = 'no_annotation';
            $fullEditDisabledReason = "Full edit was disabled automatically for this document because it is too large.";
        }

		try{
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

		$this->page->set('select_type', $select_type);
		$this->page->set('selected_type', $this->document['type']);

		$this->page->set('select_status', $select_status);
        $this->page->set('selected_status', $this->document['status']);

		$this->page->set('select_format', $select_format);
        $this->page->set('selected_format', $this->document['format_id']);
		$this->page->set('annotations_count', $annotations_count);
		$this->page->set('content_edit', $content);
        $this->page->set('full_edit_disabled', $isFullEditTooHeavy);
        $this->page->set('full_edit_disabled_reason', $fullEditDisabledReason);
        $this->page->set('disable_codemirror', $disableCodeMirror);
        $this->page->set('use_codemirror', $useCodeMirror ? 1 : 0);
        $this->page->set('content_edit_length', $contentLength);
	}
}

?>
