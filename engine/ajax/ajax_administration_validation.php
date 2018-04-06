<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_administration_validation extends CPage {

    var $isSecure = false;

    function execute(){
        global $db;

        $type = $_POST['type'];
        $mode = $_POST['mode'];

        if($type == 'relation_edit'){
            if($mode == 'create'){
                $name = $_POST['create_relation_name'];
                $sql_select = "SELECT * FROM relation_types WHERE name = '" . $name . "'";
            } else{
                $name = $_POST['edit_relation_name'];
                $relation_type_id = $_POST['id'];
                $sql_select = "SELECT * FROM relation_types WHERE (name = '" . $name . "' AND id != " . $relation_type_id . ")";
            }

            $results = $db->fetch($sql_select);
        }
        else if($type == 'relation_set_edit'){
            if($mode == 'create'){
                $name = $_POST['create_relation_set_name'];
                $sql_select = "SELECT * FROM relation_sets WHERE name = '" . $name . "'";
            } else{
                $name = $_POST['edit_relation_set_name'];
                $relation_set_id = $_POST['id'];
                $sql_select = "SELECT * FROM relation_sets WHERE (name = '" . $name . "' AND relation_set_id != " . $relation_set_id . ")";
            }

            $results = $db->fetch($sql_select);
        }
        else if($type == 'event_group'){
            if($mode == 'create'){
                $name = $_POST['create_event_name'];
                $sql_select = "SELECT * FROM event_groups WHERE name = '" . $name . "'";
            } else{
                $name = $_POST['edit_event_name'];
                $event_group_id = $_POST['id'];
                $sql_select = "SELECT * FROM event_groups WHERE (name = '" . $name . "' AND event_group_id != " . $event_group_id . ")";
            }

            $results = $db->fetch($sql_select);
        }
        else if($type == 'event_type'){
            $event_group = $_POST['event_group'];
            if($mode == 'create'){
                $name = $_POST['create_event_type_name'];
                $sql_select = "SELECT * FROM event_types WHERE (name = '" . $name . "' AND event_group_id = " . $event_group .")";
            } else{
                $name = $_POST['edit_event_name'];
                $id = $_POST['id'];
                $sql_select = "SELECT * FROM event_types WHERE (name = '" . $name . "' AND event_group_id = " . $event_group ." AND event_type_id != " . $id .")";
            }

            $results = $db->fetch($sql_select);
        }
        else if($type == 'event_type_slot'){
            $event_type = $_POST['event_type'];
            if($mode == 'create'){
                $name = $_POST['create_event_type_slot_name'];
                $sql_select = "SELECT * FROM event_type_slots WHERE (name = '" . $name . "' AND event_type_id = " . $event_type .")";
            } else{
                $name = $_POST['edit_event_type_slot_name'];
                $id = $_POST['id'];
                $sql_select = "SELECT * FROM event_type_slots WHERE (name = '" . $name . "' AND event_type_id = " . $event_type ." AND event_type_slot_id != " . $id .")";
            }

            $results = $db->fetch($sql_select);
        }
        else if($type == 'sens_edit'){
            if($mode == 'create'){
                $name = $_POST['create_lemma_word'];
                $sql_select = "SELECT * FROM annotation_types WHERE name = 'WSD_" . $name . "'";
            } else{
                $name = $_POST['edit_lemma_word'];
                $lemma_id = $_POST['id'];
                $sql_select = "SELECT * FROM annotation_types_attributes WHERE (annotation_type = 'wsd_" . $name . "' AND id != " . $lemma_id . ")";
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

        else if($type == 'new_sense'){
            $annotation_type_attribute_id = $_POST['id'];
            $value = $_POST['name'] . $_POST['create_sens_name'];
            $sql_select = "SELECT * FROM annotation_types_attributes_enum WHERE (annotation_type_attribute_id = '" . $annotation_type_attribute_id . "' AND value = '" . $value ."')";
            $results = $db->fetch($sql_select);
        }

        else if($type == 'shared_attribute'){
            $name = $_POST['create_shared_attribute_name'];
            if($mode == 'create'){
                $sql_select = "SELECT * FROM shared_attributes WHERE name = '" . $name . "'";
            } else{
                //edit?
            }

            $results = $db->fetch($sql_select);
        }

        else if($type == 'shared_attribute_enum'){
            $value = $_POST['create_shared_attribute_enum_value'];
            $shared_attribute_id = $_POST['id'];
            if($mode == 'create'){
                $sql_select = "SELECT * FROM shared_attributes_enum WHERE (value = '" . $value . "' AND shared_attribute_id = ".$shared_attribute_id.")";
            } else{
                //edit?
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