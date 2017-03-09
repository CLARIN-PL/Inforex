<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveAnnotation_lemma extends CPerspective {

    var $page = null;

    function PerspectiveAnnotation_lemma(Page_report &$page){
        $this->page = $page;
        $this->page->includeJs("js/c_annotation_mode.js");
    }

	function execute(){
		global $corpus, $user;

		$corpus_id = $corpus['id'];
        $annotation_mode = $this->getAnnotationMode();
        $an_stages = array("final");
        $an_user_ids = null;

        /* Ustaw an_stage i an_user_id na podstawie annotation_mode */
        if ( $annotation_mode == "final" ){
            $an_stages = array("final");
        }
        else if ( $annotation_mode == "agreement" ){
            $an_stages = array("agreement");
            $an_user_ids = array($user['user_id']);
        }

        $report = $this->page->report;
        $htmlStr = ReportContent::getHtmlStr($report);
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($report['id']));
        $annotation_types = CookieManager::getAnnotationTypeTreeAnnotationTypes($corpus_id);

        $annotations = DbAnnotation::getReportAnnotations($report['id'], $an_user_ids, null, null, $annotation_types, $an_stages, false);
        $htmlStr = ReportContent::insertAnnotations($htmlStr, $annotations);
        ChromePhp::info($an_stages, $an_user_ids);

        $this->page->set('content', Reformat::xmlToHtml($htmlStr->getContent()));
        $this->page->set('annotation_types', DbAnnotation::getAnnotationStructureByCorpora($corpus_id));
        $this->page->set('annotation_mode', $annotation_mode);
	}


    /**
     * Return selected annotaion mode.
     * @return null|string
     */
	function getAnnotationMode(){
        $annotation_mode = null;

        if ( isset($_COOKIE['annotation_mode']) ){
            $annotation_mode = $_COOKIE['annotation_mode'];
        }

        if ( isset($_POST['annotation_mode']) ){
            $annotation_mode = $_POST['annotation_mode'];
        }

        /* Wymuś określony tryb w oparciu i prawa użytkownika */
        if ( hasCorpusRole(CORPUS_ROLE_ANNOTATE) && !hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT) ){
            $annotation_mode = "final";
        } else if ( !hasCorpusRole(CORPUS_ROLE_ANNOTATE) && hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT) ) {
            $annotation_mode = "agreement";
        } else{
            /* Użytkownik nie ma dostępu do żadnego trybu */
            // ToDo: zgłosić brak prawa dostępu
        }
        return $annotation_mode;
    }
}