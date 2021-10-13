<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_update_wsd_annotation extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyPerspectiveAccess[] = "annotator_wsd";
    }
	
	function execute(){
		global $user, $db;

		if (!intval($user['user_id'])){
			throw new Exception("User not exists");
		}

		$annotation_id = intval($_POST['annotation_id']);
		$value = strval($_POST['value']);

		$attribute_id = CDbAnnotationTypesAttributes::getAnnotationTypeAttributeIdForSensByAnnotationId($annotation_id);

        CDbReportAnnotationAttributes::updateAttributeValue($annotation_id, $attribute_id,$user['user_id'], $value);

	}
}
