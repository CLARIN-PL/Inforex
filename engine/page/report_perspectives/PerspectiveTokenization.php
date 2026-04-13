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
        $this->page->set('tokenization_options', $this->getTokenizationOptions($row));
	}

	function assignTexts($htmlStr, &$tokens){
        foreach ($tokens as &$token) {
            $token['text'] = html_entity_decode($htmlStr->getText($token['from'], $token['to']));
	    }

    }

    function getTokenizationOptions($report){
        return array(
	            array(
	                'group' => 'MorphoDita',
	                'items' => array(
	                    array('label' => 'MorphoDita Polish, NKJP', 'tagger' => 'morphodita', 'language' => 'pl', 'tagset' => 'nkjp', 'checked' => $report['lang'] == 'pol' || !$report['lang']),
	                    array('label' => 'MorphoDita Polish, SGJP', 'tagger' => 'morphodita', 'language' => 'pl', 'tagset' => 'sgjp', 'checked' => false),
	                ),
	            ),
	            array(
	                'group' => 'PTag',
	                'items' => array(
	                    array('label' => 'PTag Polish, NKJP', 'tagger' => 'ptag', 'language' => 'pl', 'tagset' => 'nkjp', 'checked' => false),
	                ),
	            ),
	            array(
	                'group' => 'Archeopteryx',
	                'items' => array(
	                    array('label' => 'Archeopteryx Polish, NKJP', 'tagger' => 'archeopteryx', 'language' => 'pl', 'tagset' => 'nkjp', 'checked' => false),
	                ),
	            ),
	            array(
	                'group' => 'LLM POS Tagger',
	                'items' => array(
	                    array('label' => 'LLM POS Tagger Polish, NKJP', 'tagger' => 'llm-pos-tagger', 'language' => 'pl', 'tagset' => 'nkjp', 'checked' => false),
	                ),
	            ),
	            array(
	                'group' => 'spaCy UD',
                'items' => array(
                    array('label' => 'spaCy Polish', 'tagger' => 'spacy', 'language' => 'pl', 'tagset' => 'ud', 'checked' => false),
                    array('label' => 'spaCy English', 'tagger' => 'spacy', 'language' => 'en', 'tagset' => 'ud', 'checked' => $report['lang'] == 'eng'),
                    array('label' => 'spaCy German', 'tagger' => 'spacy', 'language' => 'de', 'tagset' => 'ud', 'checked' => $report['lang'] == 'ger'),
                    array('label' => 'spaCy Russian', 'tagger' => 'spacy', 'language' => 'ru', 'tagset' => 'ud', 'checked' => $report['lang'] == 'rus'),
                    array('label' => 'spaCy Portuguese', 'tagger' => 'spacy', 'language' => 'pt', 'tagset' => 'ud', 'checked' => false),
                    array('label' => 'spaCy French', 'tagger' => 'spacy', 'language' => 'fr', 'tagset' => 'ud', 'checked' => false),
                    array('label' => 'spaCy Spanish', 'tagger' => 'spacy', 'language' => 'es', 'tagset' => 'ud', 'checked' => false),
                ),
            ),
        );
    }
}
