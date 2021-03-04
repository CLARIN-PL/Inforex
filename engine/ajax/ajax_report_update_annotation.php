<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_update_annotation extends CPageCorpus {

    function __construct(){
        // ToDo: prawo edycji anotacji CORPUS_ROLE_ANNOTATE_AGREEMENT powinno dotyczyć wyłącznie anotacji o stage=agreement
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
    }
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		$annotation_id = intval($_POST['annotation_id']);
		$type_id = intval($_POST['type_id']);
		$lemma = strval($_POST['lemma']);
        $shared_attributes = $this->getRequestParameter('shared_attributes', array());
        $an = DbAnnotation::get($annotation_id);
        $error = null;

        $sharedAttributesValues = array();

		if($row = $this->getDb()->fetch("SELECT * FROM `reports_annotations_optimized` WHERE id = ".$annotation_id )){ 
			/** Update type */
			$this->getDb()->update("reports_annotations_optimized",
				array("type_id"=>$type_id),
				array("id"=>$annotation_id));

			/** Update lemma */
            DbReportAnnotationLemma::saveAnnotationLemma($annotation_id, $lemma);

			/** Update attributes */
            $sharedAttributesValues = $this->updateSharedAttributes($annotation_id, $type_id, $shared_attributes);

			if ( $type_id != $row['type_id'] ){
			    DbAnnotation::removeUnusedAnnotationSharedAttributes($annotation_id);
            }
		} else {
			throw new Exception("An error occurred while saving the annotation");
			return;			
		}

		$result = array();
		$result["text"] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT];
		$result["annotation_id"] = $annotation_id;
		$result["shared_attributes"] = $sharedAttributesValues;

		return $result;
	}

    /**
     * @param $row
     * @param $text
     * @param $from
     * @param $to
     * @param $type_id
     * @throws Exception
     */
	function validateText($row, $text, $from, $to, $type_id){
        $content = $row['content'];
        $content = normalize_content($content);
        if ( $row['format'] == 'plain' ){
            $content = htmlspecialchars($content);
        }

        $html = new HtmlStr2($content, true);
        $text_revalidate = $html->getText($from, $to);
        $html_revalidate = custom_html_entity_decode($text_revalidate);

        if ( preg_replace("/\n+|\r+|\s+/","",$text) != preg_replace("/\n+|\r+|\s+/","", $html_revalidate) ){
            $error = "Synchronizacja z bazą się nie powiodła &mdash; wystąpiła rozbieżność anotacji. <br/><br/>" .
                "Type id: <b>$type_id</b><br/>" .
                "Position: [<b>$from,$to</b>]<br/>" .
                "Sent phrase: <b>'$text'</b><br/>" .
                "Database phrase: <b>'$html_revalidate'</b>";
            throw new Exception($error);
        }
    }

    /**
     *
     */
	function updateSharedAttributes($annotationId, $typeId, $sharedAttributes){
	    $attributes = array();
        foreach ($sharedAttributes as $sharedAttributeId=>$value) {
            DbAnnotation::setSharedAttributeValue($annotationId, $sharedAttributeId, $value, $this->getUserId());
            $attr = CDbAnnotationSharedAttribute::get($sharedAttributeId);
            if ( $attr['type'] == DB_SHARED_ATTRIBUTE_TYPES_ENUM
                    && strlen(trim($value)) > 0
                    && !CDbAnnotationSharedAttribute::existsAttributeEnumValue($sharedAttributeId, $value)){
                CDbAnnotationSharedAttribute::addAttributeEnumValue($sharedAttributeId, $value);
            }
            $attributes[] = array("id"=>$sharedAttributeId, "value"=>$value);
        }
        return $attributes;
    }

}
