<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_corpus_custom_annotation_sets extends CPage {

    function checkPermission(){
        return true;
    }

    function execute(){
        global $db;

        $type = $_POST['type'];
        $mode = $_POST['mode'];
         if($type == 'annotation_subset'){
            $annotation_set = $_POST['annotation_set'];
            if($mode == 'create'){
                $name = $_POST['create_annotation_subset_name'];
                $sql_select = "SELECT * FROM annotation_subsets WHERE (name = '" . $name . "' AND annotation_set_id = '" . $annotation_set ."')";
            } else{
                $name = $_POST['edit_annotation_subset_name'];
                $annotation_subset_id = $_POST['id'];
                $sql_select = "SELECT * FROM annotation_subsets WHERE (name = '" . $name . "' AND annotation_set_id = '" . $annotation_set ."' AND annotation_subset_id != " . $annotation_subset_id . ")";

            }

            $results = $db->fetch($sql_select);
        }
        else if($type == 'annotation_type'){
            $annotation_subset = $_POST['annotation_subset'];
            if($mode == 'create'){
                $name = $_POST['create_annotation_type_name'];
                $sql_select = "SELECT * FROM annotation_types WHERE (name = '" . $name . "' AND annotation_subset_id = " . $annotation_subset .")";

            } else{
                $name = $_POST['edit_annotation_type_name'];
                $id = $_POST['id'];
                $sql_select = "SELECT * FROM annotation_types WHERE (name = '" . $name . "' AND annotation_subset_id = " . $annotation_subset ." AND annotation_type_id != " . $id .")";

            }

            $results = $db->fetch($sql_select);
        }

        if($results != null){
            echo "false";
        } else{
            echo "true";
        }
        die();
    }
}
?>
