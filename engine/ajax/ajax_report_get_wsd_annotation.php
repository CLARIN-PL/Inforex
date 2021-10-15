<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_get_wsd_annotation extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyPerspectiveAccess[] = "wsd_annotator";
    }

    function execute(){
        global $user;
        $annotation_id = intval($_POST['annotation_id']);
        $user_id = intval($user['user_id']);

        if ($annotation_id <= 0) {
            throw new Exception("No identifier of annotation found");
        }
        $attr = DbAnnotationType::getAnnotationTypeByAnnotationId($annotation_id);
        $rows_values = CDbAnnotationTypesAttributesEnum::getAnnotationTypeAttributesEnumRowsByAttributeId($attr);

        $values = array();
        foreach ($rows_values as $v)
            $values[] = array("value" => $v['value'], "description" => $v['description']);
        $attr['values'] = $values;
        $attr['value'] = CDbReportAnnotationAttributes::getAnnotationTypeAttributesEnumRowsByAttributeId($annotation_id, $attr['id'], $user_id);

        return $attr;

    }
	
}
