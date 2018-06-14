<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_relations_groups_management extends CPageAdministration {

    private $mode;
    private $action;
    private $direction;
    private $relation_type_id;

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = ROLE_SYSTEM_EDITOR_SCHEMA_RELATIONS;
    }

    function execute()
    {
        global $db;

        $this->mode = $_POST['mode'];
        $this->action = $_POST['action'];
        $this->direction = $_POST['direction'];
        $this->relation_type_id = $_POST['relation_type_id'];

        switch($this->mode){
            case "annotation_type":
                $annotation_type_id = $_POST['annotation_type_id'];
                $annotation_subset_id = $_POST['annotation_subset_id'];
                $annotation_set_id = $_POST['annotation_set_id'];

                if($this->action == "create"){
                    $this->insertAnnotationType($annotation_type_id, $annotation_subset_id, $annotation_set_id);
                } else{
                    $this->deleteAnnotationType($annotation_set_id, $annotation_subset_id, $annotation_type_id);
                }

                break;
            case "annotation_subset":
                $annotation_set_id = $_POST['annotation_set_id'];
                $annotation_subset_id = $_POST['annotation_subset_id'];

                if($this->action == "create"){
                    $this->insertAnnotationSubset($annotation_set_id, $annotation_subset_id);
                } else{
                    $this->deleteAnnotationSubset($annotation_set_id, $annotation_subset_id);
                }

                break;
            case "annotation_set":
                $annotation_set_id = $_POST['annotation_set_id'];
                if($this->action == "create"){
                    $this->insertAnnotationSet($annotation_set_id);
                } else{
                    $this->deleteAnnotationSet($annotation_set_id);
                }


                break;
        }
    }

    private function insertAnnotationSet($annotation_set_id){
        global $db;

        $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
        $db->execute($sql, array($this->relation_type_id, $this->direction, $annotation_set_id, null, null));
    }

    private function deleteAnnotationSet($annotation_set_id){
        global $db;

        $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_set_id = ? AND part = ?)";
        $db->execute($sql, array($this->relation_type_id, $annotation_set_id, $this->direction));

        $annotation_subsets = $this->getAnnotationSubsetsOfSet($annotation_set_id);
        $annotation_subsets_list = $this->convertIdToCSV($annotation_subsets, 'annotation_subset_id');

        $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_subset_id IN (".$annotation_subsets_list.") AND part = ?)";
        $db->execute($sql, array($this->relation_type_id, $this->direction));

        $annotation_types_list = $this->getAnnotationTypesForSubsetList($annotation_subsets_list);
        $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_type_id IN (".$annotation_types_list.") AND part = ?)";
        $db->execute($sql, array($this->relation_type_id, $this->direction));

    }

    private function getAnnotationTypesForSubsetList($annotation_subset_list){
        global $db;

        $sql = "SELECT * FROM annotation_types WHERE annotation_subset_id IN (".$annotation_subset_list.")";
        $annotation_types = $db->fetch_rows($sql);
        $annotation_types_list = $this->convertIdToCSV($annotation_types, 'annotation_type_id');

        return $annotation_types_list;
    }


    private function insertAnnotationSubset($annotation_set_id, $annotation_subset_id){
        global $db;

        $possible_annotation_subsets = $this->getAnnotationSubsetsOfSet($annotation_set_id);
        $subsets_list = $this->convertIdToCSV($possible_annotation_subsets, 'annotation_subset_id');
        $number_annotation_subsets_inserted = $this->getInsertedAnnotationSubsets($subsets_list);
        $number_of_possible_subsets = count($possible_annotation_subsets);


        $this->deleteAnnotationTypesOfSubset($annotation_subset_id);

        if(($number_annotation_subsets_inserted + 1) >= $number_of_possible_subsets){
            $this->convertToSet($annotation_set_id, $subsets_list);
        } else {
            $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
            $db->execute($sql, array($this->relation_type_id, $this->direction, null, $annotation_subset_id, null));
        }

    }

    private function deleteAnnotationSubset($annotation_set_id, $annotation_subset_id){
        global $db;

        //check if annotation set is inserted
        if($this->isAnnotationSetInserted($annotation_set_id)){
            //delete the set and insert every subset except the one that needs to be deleted
            $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_set_id = ? AND part = ?)";
            $db->execute($sql, array($this->relation_type_id, $annotation_set_id, $this->direction));

            $possible_annotation_subsets = $this->getAnnotationSubsetsOfSet($annotation_set_id);
            foreach($possible_annotation_subsets as $annotation_subset){
                if($annotation_subset['annotation_subset_id'] != $annotation_subset_id){
                    $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
                    $db->execute($sql, array($this->relation_type_id, $this->direction, null, $annotation_subset['annotation_subset_id'], null));
                }
            }
        } else{
            //delete all inserted annotation types and the subset
            $annotation_types = $this->getAnnotationTypesOfSubset($annotation_subset_id);
            $annotation_types_list = $this->convertIdToCSV($annotation_types, 'annotation_type_id');

            //Deleting annotation types
            $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_type_id IN (".$annotation_types_list.") AND part = ?)";
            $db->execute($sql, array($this->relation_type_id, $this->direction));

            //Deleting annotation subset
            $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_subset_id  = ? AND part = ?)";
            $db->execute($sql, array($this->relation_type_id, $annotation_subset_id, $this->direction));
        }


    }

    private function deleteAnnotationTypesOfSubset($annotation_subset_id){
        global $db;

        $annotation_types = $this->getAnnotationTypesOfSubset($annotation_subset_id);

        foreach($annotation_types as $annotation_type){
            $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_type_id = ? AND part = ?)";
            $db->execute($sql, array($this->relation_type_id, $annotation_type['annotation_type_id'], $this->direction));
        }

    }

    private function isAnnotationSetInserted($annotation_set_id){
        global $db;

        $sql = "SELECT * FROM relations_groups WHERE (relation_type_id = ? AND part = ? AND annotation_set_id = ?)";
        $annotation_sets = $db->fetch_rows($sql, array($this->relation_type_id, $this->direction, $annotation_set_id));
        if(count($annotation_sets) > 0){
            return true;
        } else{
            return false;
        }
    }



    private function insertAnnotationType($annotation_type_id, $annotation_subset_id, $annotation_set_id){
        global $db;

        $possible_annotation_types = $this->getAnnotationTypesOfSubset($annotation_subset_id);
        $types_list = $this->convertIdToCSV($possible_annotation_types, 'annotation_type_id');
        $number_annotation_types_inserted = $this->getInsertedAnnotationTypes($types_list);
        $number_of_possible_types = count($possible_annotation_types);

        if($number_annotation_types_inserted + 1 >= $number_of_possible_types){
            $this->convertToSubset($annotation_set_id, $annotation_subset_id, $types_list);
        } else{
            $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
            $db->execute($sql, array($this->relation_type_id, $this->direction, null, null, $annotation_type_id));
        }
    }

    private function deleteAnnotationType($annotation_set_id, $annotation_subset_id, $annotation_type_id){
        global $db;

        $possible_annotation_types = $this->getAnnotationTypesOfSubset($annotation_subset_id);
        $types_list = $this->convertIdToCSV($possible_annotation_types, 'annotation_type_id');
        $annotation_types_inserted = $this->getNumberOfAnnotationTypes($annotation_set_id, $annotation_subset_id, $types_list);

        if($annotation_types_inserted == "set"){
            //Kasujemy set
            $this->deleteAnnotationSetForType($annotation_set_id, $annotation_subset_id, $annotation_type_id);

        } else if($annotation_types_inserted == "subset"){
            $this->deleteAnnotationSubsetForType($annotation_set_id, $annotation_subset_id, $annotation_type_id);
        } else{
            $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_type_id = ? AND part = ?)";
            $db->execute($sql, array($this->relation_type_id, $annotation_type_id, $this->direction));

        }
    }

    private function deleteAnnotationSubsetForType($annotation_set_id, $annotation_subset_id, $annotation_type_id){
        global $db;

        $annotation_types = $this->getAnnotationTypesOfSubset($annotation_subset_id);

        //Delete annotation subset
        $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_subset_id = ? AND part = ?)";
        $db->execute($sql, array($this->relation_type_id, $annotation_subset_id, $this->direction));

        foreach($annotation_types as $annotation_type){
            if($annotation_type['annotation_type_id'] == $annotation_type_id){
                continue;
            }

            $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
            $db->execute($sql, array($this->relation_type_id, $this->direction, null, null, $annotation_type['annotation_type_id']));
        }

    }

    private function deleteAnnotationSetForType($annotation_set_id, $annotation_subset_id, $annotation_type_id){
        global $db;

        $annotation_subsets = $this->getAnnotationSubsetsOfSet($annotation_set_id);
        $annotation_types = $this->getAnnotationTypesOfSubset($annotation_subset_id);

        $subsets_list = $this->convertIdToCSV($annotation_subsets, 'annotation_subset_id');

        //Delete annotation set
        $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND annotation_set_id = ? AND part = ?)";
        $db->execute($sql, array($this->relation_type_id, $annotation_set_id, $this->direction));

        //Insert annotation subsets (except the one attached to the annotation types)
        foreach($annotation_subsets as $annotation_subset){
            if($annotation_subset['annotation_subset_id'] == $annotation_subset_id){
                continue;
            }

            $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
            $db->execute($sql, array($this->relation_type_id, $this->direction, null, $annotation_subset['annotation_subset_id'], null));
        }

        //Insert annotation types (except the one attached to the annotation types)
        foreach($annotation_types as $annotation_type){
            if($annotation_type['annotation_type_id'] == $annotation_type_id){
                continue;
            }

            $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
            $db->execute($sql, array($this->relation_type_id, $this->direction, null, null, $annotation_type['annotation_type_id']));
        }
    }

    private function getNumberOfAnnotationTypes($annotation_set_id, $annotation_subset_id, $types){
        global $db;

        $sql = "SELECT * FROM relations_groups WHERE (relation_type_id = ".$this->relation_type_id." AND part = '".$this->direction."' AND annotation_set_id = ".$annotation_set_id.")";
        $annotation_set_relations = $db->fetch_rows($sql);

        if(count($annotation_set_relations) == 0){
            //Check if subsets exist
            $sql = "SELECT * FROM relations_groups WHERE (relation_type_id = ".$this->relation_type_id." AND part = '".$this->direction."' AND annotation_subset_id = ".$annotation_subset_id.")";
            $annotation_subset_relations = $db->fetch_rows($sql);

            if(count($annotation_subset_relations) == 0){
                $sql = "SELECT * FROM relations_groups WHERE (relation_type_id = ".$this->relation_type_id." AND part = '".$this->direction."' AND annotation_type_id IN (".$types."))";
                $annotation_types = $db->fetch_rows($sql);
                $result = count($annotation_types);

            } else{
                $result = "subset";
            }

        } else{
            $result = "set"; global $db;

        $possible_annotation_subsets = $this->getAnnotationSubsetsOfSet($annotation_set_id);
        $subsets_list = $this->convertIdToCSV($possible_annotation_subsets, 'annotation_subset_id');
        $number_annotation_subsets_inserted = $this->getInsertedAnnotationSubsets($subsets_list);
        $number_of_possible_subsets = count($possible_annotation_subsets);
        }

        return $result;
    }

    private function convertToSubset($annotation_set_id, $annotation_subset_id, $types_list){
        global $db;

        //Delete all annotation types
        $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND (annotation_type_id IN (".$types_list.") AND part = ?))";
        $db->execute($sql, array($this->relation_type_id, $this->direction));

        //Insert annotation subset instead
        $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
        $db->execute($sql, array($this->relation_type_id, $this->direction, null, $annotation_subset_id, null));

        //Check if annotation subsets need to be converted into set now
        $possible_annotation_subsets = $this->getAnnotationSubsetsOfSet($annotation_set_id);
        $subsets_list = $this->convertIdToCSV($possible_annotation_subsets, 'annotation_subset_id');
        $number_annotation_subsets_inserted = $this->getInsertedAnnotationSubsets($subsets_list);
        $number_of_possible_subsets = count($possible_annotation_subsets);

        if($number_annotation_subsets_inserted >= $number_of_possible_subsets){
            $this->convertToSet($annotation_set_id, $subsets_list);
        }
    }

    private function convertToSet($annotation_set_id, $subsets_list){
        global $db;
        //Delete all annotation subsets
        $sql = "DELETE FROM relations_groups WHERE (relation_type_id = ? AND (annotation_subset_id IN (".$subsets_list.") AND part = ?))";
        $db->execute($sql, array($this->relation_type_id, $this->direction));

        //Insert annotation set instead
        $sql = "INSERT INTO relations_groups VALUES(?, ?, ?, ?, ?)";
        $db->execute($sql, array($this->relation_type_id, $this->direction, $annotation_set_id, null, null));
    }


    private function getInsertedAnnotationTypes($types){
        global $db;

        $sql = "SELECT * FROM relations_groups WHERE (relation_type_id = ".$this->relation_type_id." AND part = '".$this->direction."' AND (annotation_type_id IN (".$types.")))";
        $annotation_types = $db->fetch_rows($sql);
        return count($annotation_types);
    }

    private function getInsertedAnnotationSubsets($types){
        global $db;

        $sql = "SELECT * FROM relations_groups WHERE (relation_type_id = ".$this->relation_type_id." AND part = '".$this->direction."' AND (annotation_subset_id IN (".$types.")))";
        $annotation_subsets = $db->fetch_rows($sql);
        return count($annotation_subsets);
    }

    private function getAnnotationTypesOfSubset($annotation_subset_id){
        global $db;

        $sql = "SELECT annotation_type_id FROM annotation_types WHERE annotation_subset_id = ?";
        $annotation_types = $db->fetch_rows($sql, array($annotation_subset_id));

        return $annotation_types;
    }

    private function getAnnotationSubsetsOfSet($annotation_set_id){
        global $db;

        $sql = "SELECT annotation_subset_id FROM annotation_subsets WHERE annotation_set_id = ?";
        $annotation_subsets = $db->fetch_rows($sql, array($annotation_set_id));

        return $annotation_subsets;
    }

    private function convertIdToCSV($array, $id){
        $types_list = array();

        foreach($array as $type){
            $types_list[] = $type[$id];
        }

        $types_num = array_map('intval', $types_list);
        $result = implode(',', $types_num);

        return $result;
    }
}
