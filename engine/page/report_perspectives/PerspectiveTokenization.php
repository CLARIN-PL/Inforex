<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveTokenization extends CPerspective {
	
	function execute(){
		$row = $this->page->row;

		$htmlStr = ReportContent::getHtmlStr($row);
		$htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($row[DB_COLUMN_REPORTS__REPORT_ID]));

		$this->page->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('report', $row);
	}

}