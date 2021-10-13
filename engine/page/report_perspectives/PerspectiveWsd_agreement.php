<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveWsd_agreement extends CPerspective {

    function __construct(CPage $page, $document)
    {
        parent::__construct($page, $document);
        $this->page->includeJs("js/c_widget_annotation_type_tree.js");
        $this->page->includeJs("js/c_widget_user_selection_a_b.js");
    }
	
	function execute(){
        global $corpus;

		$corpus_id = $corpus['id'];
		$report_id = $this->document[DB_COLUMN_REPORTS__REPORT_ID];
		
		$annotator_a_id = intval($_COOKIE['agreement_annotations_'.$corpus_id.'_annotator_id_a']);
		$annotator_b_id = intval($_COOKIE['agreement_annotations_'.$corpus_id.'_annotator_id_b']);


		//$this->setup_annotation_type_tree($corpus_id);
		
		$annotation_types_str = trim(strval($_COOKIE[$corpus_id . '_annotation_lemma_types']));
		$annotation_types = null;

		if ( $annotation_types_str ) {
		    $annotation_types = array();
            foreach (explode(",", $annotation_types_str) as $id) {
                $id = intval($id);
                if ($id > 0) {
                    $annotation_types[] = $id;
                }
            }
        }

		$users = DbAnnotation::getUserAnnotationAttributesCount(null, null, array($report_id), null, $annotation_types, null, "agreement");
		$annotations = array();
		
		if ( $annotator_a_id > 0 && $annotator_b_id > 0 && $annotator_a_id != $annotator_b_id && $annotation_types !== null ){
			$annotations = DbAnnotation::getReportAnnotations($report_id, null, null, null, $annotation_types);
		}

		$available_annotation_types = DbAnnotation::getAnnotationTypesByIds($annotation_types);

		$wsd_annotations = DbAnnotation::getWSDAgreementAnnotationsWithFinal($report_id, 70, $annotator_a_id, $annotator_b_id);

        $html = ReportContent::getHtmlStr($this->page->report);

        $errors = array();

		/** Output variables to the template */
		$this->page->set("users", $users);
		$this->page->set("errors", $errors);
		$this->page->set("annotations", $annotations);
		$this->page->set("wsd_annotations", $wsd_annotations);
		$this->page->set("content_inline", $html->getContent());
		$this->page->set("available_annotation_types", $available_annotation_types);
		$this->page->set("annotator_a_id", $annotator_a_id);
		$this->page->set("annotator_b_id", $annotator_b_id);
	}

	/**
	 * Ustaw strukturę dostępnych typów anotacji.
	 * @param unknown $corpus_id
	 */
	private function setup_annotation_type_tree($corpus_id){
		$annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);
		$this->page->set('annotation_types',$annotations);
	}
}
