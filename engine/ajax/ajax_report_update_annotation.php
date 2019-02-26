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
		global $mdb2, $db;
		$annotation_id = intval($_POST['annotation_id']);
		$type_id = intval($_POST['type_id']);
		$from = intval($_POST['from']);
		$to = intval($_POST['to']);
		$text = stripslashes(strval($_POST['text']));
		$lemma = strval($_POST['lemma']);
		$report_id = intval($_POST['report_id']);
		$error = null;
        $shared_attributes = $_POST['shared_attributes'];

        $sharedAttributesValues = array();

        $row = $db->fetch("SELECT r.content, f.format" .
            " FROM reports r" .
            " JOIN reports_formats f ON (r.format_id=f.id)" .
            " WHERE r.id=?", array($report_id));

        $this->validateText($row, $text, $from, $to, $type_id);
		
		$table_annotations = $mdb2->tableBrowserFactory('reports_annotations', 'id');		

		if ($row = $table_annotations->getRow($annotation_id)){
			/** Update type */
			$db->update("reports_annotations_optimized",
				array("from"=>$from,"to"=>$to,"text"=>$text,"type_id"=>$type_id),
				array("id"=>$annotation_id));

			/** Update lemma */
            DbReportAnnotationLemma::saveAnnotationLemma($annotation_id, $lemma);

			/** Update attributes */
            $sharedAttributesValues = $this->updateSharedAttributes($annotation_id, $type_id, $shared_attributes);

			if ( $type_id != $row['type_id'] ){
			    DbAnnotation::removeUnusedAnnotationSharedAttributes($annotation_id);
            }
		}else{
			throw new Exception("An error occurred while saving the annotation");
			return;			
		}


		$result = array();
		$result["from"] = $from;
		$result["to"] = $to;
		$result["text"] = $text;
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