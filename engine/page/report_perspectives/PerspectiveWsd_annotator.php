<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveWsd_annotator extends CPerspective {

	function __construct(CPage $page, $document)
    {
        parent::__construct($page, $document);
        $this->page->includeJs("js/c_annotation_mode.js");
    }

    function execute(){

		$word = $_GET['wsd_word'];
		$word_annotation_type_id = $_GET['annotation_type_id'];
		$annotation_id = intval($_GET['aid']);

        $annotation_sets = DbAnnotationSet::getAnnotationSetsWithWSD();
        $selected_annotation_set = CookieManager::getAnnotatorWSDAnnotationSet();
        if($selected_annotation_set == null){
        	$selected_annotation_set = 2;
		}

        $annotation_mode = "final";
        if ( isset($_COOKIE['annotation_mode_wsd']) ){
            $annotation_mode = $_COOKIE['annotation_mode_wsd'];
        }
        else{
            setcookie("annotation_mode_wsd", "final");
		}

		$content = $this->load_document_content($this->document, $selected_annotation_set, $annotation_mode);

		$this->page->set('annotation_sets', $annotation_sets);
		$this->page->set('selected_annotation_set', $selected_annotation_set);

		$this->page->set("wsd_word", $word);
        $this->page->set("wsd_word_id", $word_annotation_type_id);
        $this->page->set("wsd_edit", $annotation_id);
		$this->page->set("content_inline", $content);

        if ( isset($_COOKIE['annotation_mode_wsd']) ){
            $annotation_mode = $_COOKIE['annotation_mode_wsd'];
            if($annotation_mode != "final")
                $annotation_mode = "agreement";
        }
        $this->page->set("annotation_mode", $annotation_mode);
	}

	function load_document_content($report, $annotationSetId, $anStage='agreement', $anUserId=null){
		$anUserId = $anUserId !== null && !is_array($anUserId) ? [$anUserId] : $anUserId;

		$htmlStr = ReportContent::getHtmlStr($report);
        $annotations = DbAnnotation::getReportAnnotations($report['id'], $anUserId,
			array($annotationSetId), null, null, array($anStage), false);
        $htmlStr = ReportContent::insertAnnotations($htmlStr, $annotations);
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($report['id']));
		return Reformat::xmlToHtml($htmlStr->getContent());
	}
}
