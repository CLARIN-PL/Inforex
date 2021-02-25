<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_content extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);

        $this->anyPerspectiveAccess[] = "tokenization";
    }

    function execute(){

        $reportId = $this->getRequestParameterRequired("report_id");

        $tokens = DbToken::getTokenByReportId($reportId, null,true);
        $report = DbReport::get($reportId);

        $htmlStr = ReportContent::getHtmlStr($report);
        $htmlStr = ReportContent::insertTokens($htmlStr, $tokens);

        $this->assignTexts($htmlStr, $tokens);

        $content_inline = Reformat::xmlToHtml($htmlStr->getContent());

        return array("content_inline" => $this->format_annotations($content_inline));
	}

    function assignTexts($htmlStr, &$tokens){
        foreach ($tokens as &$token) {
            $token['text'] = html_entity_decode($htmlStr->getText($token['from'], $token['to']));
        }

    }

    function format_annotations($string){
        $string = stripslashes($string);
        $string = preg_replace("/<br>|<\/br>/","<div></div>",$string);
        $string = str_replace('<an:$k>', '<span class=\'$k\'>[?]', $string);
        //$string = preg_replace('/<an#(\d+):([a-z_]+)>/', "<small title='an#$1:$2'>[#$1]</small><span id='an$1' class='$2' title='an#$1:$2'>", $string);
        $string = preg_replace('/<an#(\d+):([^:]+):(\d+):(\d+):\'(.*?)\'>/u', "<span id='an$1' class='ann annotation_set_$3 $2' groupid='$3' subgroupid='$4' lemma='$5' title='an#$1:$2'>", $string);
        $string = preg_replace('/<an#(\d+):([^:]+):(\d+):(\d+)>/u', "<span id='an$1' class='ann annotation_set_$3 $2' groupid='$3' subgroupid='$4' title='an#$1:$2'>", $string);
        $string = preg_replace('/<an#(\d+):([^:]+):(\d+)>/u', "<span id='an$1' class='ann annotation_set_$3 $2' groupid='$3' title='an#$1:$2'>", $string);
        $string = preg_replace('/<an#(\d+):([a-z0-9_]+)>/', "<span id='an$1' class='$2' title='an#$1:$2'>", $string);
        $string = preg_replace('/<an#(\d+):([a-z0-9_]+) eos>/', "<span id='an$1' class='$2 eos' title='an#$1:$2'>", $string);
        $string = str_replace("</an>", "</span>", $string);
        return $string;
    }
}


