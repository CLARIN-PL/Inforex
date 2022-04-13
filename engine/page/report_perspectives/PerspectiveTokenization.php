<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveTokenization extends CPerspective {

    public function __construct(CPage $page, $document){
        parent::__construct($page, $document);

        $this->page->includeJs("libs/bootstrap-confirmation.min.js");
    }

    function execute(){
		$row = $this->page->row;

		$tokens = DbToken::getTokenByReportId($row[DB_COLUMN_REPORTS__REPORT_ID], null,true);

		$htmlStr = ReportContent::getHtmlStr($row);
		$htmlStr = ReportContent::insertTokens($htmlStr, $tokens);

        $this->assignTexts($htmlStr, $tokens);

		$this->page->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('report', $row);
		$this->page->set('tokens', $tokens);
	}

	function assignTexts($htmlStr, &$tokens){
        foreach ($tokens as &$token) {
            $token['text'] = html_entity_decode($htmlStr->getText($token['from'], $token['to']));
	    }

    }
}