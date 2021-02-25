<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_token_split extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);

        $this->anyPerspectiveAccess[] = "tokenization";
    }

    function execute(){
        $tokenId = $this->getRequestParameterRequired("token_id");
        $token_len = $this->getRequestParameterRequired("token_length");
        $token = DbToken::get($tokenId);
        $reportId = $token["report_id"];

        $token1 = DBToken::updateToken($token["token_id"], $token["from"], $token["from"] + $token_len - 1);
        $token2_id = DBToken::saveToken($reportId, $token["from"] + $token_len, $token["to"], $token["eos"]);
        $token2 = DbToken::get($token2_id);

        return array("token1" =>  $token1 , "token2" => $token2);
	}
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