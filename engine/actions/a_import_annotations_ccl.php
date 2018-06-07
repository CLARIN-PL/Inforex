<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Action_import_annotations_ccl extends CAction {

    function execute(){
        global $user;

        $user_id = $user['user_id'];
        $stage = $_POST['annotation_stage'];
        $source = $_POST['annotation_source'];
        $ignore_duplicates = $_POST['ignore_duplicates'];
        $ignore_unknown_types = $_POST['ignore_unknown_types'];


        $document_id = $_GET['id'];
        $cclFileName = $_FILES["cclFile"]["tmp_name"];
        $annotation_set_id = $_POST['annotation_set'];
        $annotations = new Import_Annotations_CCL($cclFileName, $document_id, $user_id, $stage, $source, $annotation_set_id, $ignore_duplicates, $ignore_unknown_types);
        $annotations->read();
        $annotations->processAnnotationns();
        $import = $annotations->importAnnotations();
        if($import !== 'ok'){
            $_SESSION['importannotations']['error'] = $import['error'];

        } else{
            $_COOKIE['view_annotation_set'] = $annotation_set_id;
            $_COOKIE['view_annotation_stage'] = $stage;
        }
    }

}