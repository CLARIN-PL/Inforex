<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_get_relation_type_groups extends CPage {

    function checkPermission(){
        if (hasRole('admin') || hasRole('editor_schema_relations'))
            return true;
        else
            return "Brak prawa do edycji.";
    }

    function execute(){

        global $db;

        $mode = $_POST['mode'];
        $relation_type_id = $_POST['relation_type_id'];

        if($mode == 'annotation_set'){

            $sql = "SELECT * FROM annotation_sets";
            $annotation_sets = $db->fetch_rows($sql);
            $relation_groups = $this->getRelationGroups($relation_type_id);

            foreach($annotation_sets as $key => $annotation_set){
                foreach($relation_groups as $relation_group){
                    if($relation_group['annotation_set_id'] == $annotation_set['annotation_set_id']){
                        $annotation_sets[$key]['checked'] = 1;
                        if($relation_group['part'] == 'source'){
                            $annotation_sets[$key]['source'] = 1;
                        } else{
                            $annotation_sets[$key]['target'] = 1;
                        }
                    }
                }
            }

            return $annotation_sets;

        }
        else if($mode == "annotation_subsets"){
            $annotation_set_id = $_POST['annotation_set_id'];
            $sql = "SELECT * FROM annotation_subsets WHERE annotation_set_id = ?";
            $annotation_subsets = $db->fetch_rows($sql, array($annotation_set_id));
            $relations = $this->getRelationGroups($relation_type_id);

            foreach($relations as $relation){
                foreach($annotation_subsets as $key => $annotation_subset){
                    if(($annotation_subset['annotation_subset_id'] == $relation['annotation_subset_id']) || ($annotation_set_id == $relation['annotation_set_id'] && !$relation['annotation_subset_id'])){

                        if(!$relation['annotation_type_id']){
                            $annotation_subsets[$key]['checked'] = 1;
                            if($relation['part'] == 'source'){
                                $annotation_subsets[$key]['source'] = 1;
                            } else{
                                $annotation_subsets[$key]['target'] = 1;
                            }
                        } else{
                            if($this->annotationTypeBelongsToSubset($relation['annotation_type_id'], $annotation_subset['annotation_subset_id'])){
                                $annotation_subsets[$key]['checked'] = 1;
                                if($relation['part'] == 'source'){
                                    $annotation_subsets[$key]['source'] = 1;
                                } else{
                                    $annotation_subsets[$key]['target'] = 1;
                                }
                            }
                        }
                    }
                }
            }

            return $annotation_subsets;

        }
        else if($mode == "annotation_types"){
            $annotation_set_id = $_POST['annotation_set_id'];
            $annotation_subset_id = $_POST['annotation_subset_id'];


            $sql = "SELECT * FROM annotation_types WHERE annotation_subset_id = ?";
            $annotation_types = $db->fetch_rows($sql, array($annotation_subset_id));
            $relations = $this->getRelationGroups($relation_type_id);

            foreach($relations as $relation){
                foreach($annotation_types as $key => $annotation_type){
                    if(($annotation_type['annotation_type_id'] == $relation['annotation_type_id']) || ($annotation_set_id == $relation['annotation_set_id'] && (!$relation['annotation_type_id'] && !$relation['annotation_subset_id']) ) || ($annotation_subset_id == $relation['annotation_subset_id'] && (!$relation['annotation_type_id']))){


                        if($relation['part'] == 'source'){
                            $annotation_types[$key]['source'] = 1;
                        } else {
                            $annotation_types[$key]['target'] = 1;
                        }
                    }
                }
            }

            return $annotation_types;
        }
        else{
            //Generating the table "Relation groups" on the page "relation_edit"
            $sql = "SELECT rg.* , ast.name AS  'set_name', ass.name AS  'subset_name', at.name AS  'type_name'
                    FROM  `relations_groups` rg
                    LEFT JOIN annotation_sets ast ON ast.annotation_set_id = rg.annotation_set_id
                    LEFT JOIN annotation_subsets ass ON ass.annotation_subset_id = rg.annotation_subset_id
                    LEFT JOIN annotation_types at ON at.annotation_type_id = rg.annotation_type_id
                    WHERE rg.relation_type_id = ?";
            $result = $db->fetch_rows($sql, array($relation_type_id));
            return $result;
        }
    }

    function annotationTypeBelongsToSubset($annotation_type_id, $annotation_subset_id){
        global $db;

        $sql = "SELECT * FROM annotation_types WHERE (annotation_type_id = ? AND annotation_subset_id = ?)";
        $result = $db->fetch_rows($sql, array($annotation_type_id, $annotation_subset_id));

        $number = count($result);

        if($number > 0){
            return true;
        } else{
            return false;
        }
    }

    //Returns the relation groups for a given relation type id. Finds the annotation set or subset depending on the level of the relation group.
    function getRelationGroups($relation_type_id){
        global $db;

        $sql = "SELECT * FROM relations_groups rg WHERE relation_type_id = ?";
        $relations = $db->fetch_rows($sql, array($relation_type_id));

        foreach($relations as $key => $relation){
            if($relation['annotation_set_id'] == NULL){
                $annotation_subset_id = $relation['annotation_subset_id'];
                if($annotation_subset_id == NULL){
                    $annotation_subset_id = $this->annotationTypeToSubset($relation['annotation_type_id']);
                    $relations[$key]['annotation_subset_id'] = $annotation_subset_id;
                    $relations[$key]['level'] = 'type';
                } else{
                    $relations[$key]['level'] = 'subset';
                }
                $relations[$key]['annotation_set_id'] = $this->annotationSubsetToSet($annotation_subset_id);
            } else{
                $relations[$key]['level'] = 'set';
            }
        }
        return $relations;
    }

    //Find the annotation subset that a given annotation type belongs to.
    function annotationTypeToSubset($annotation_type_id){
        global $db;

        $sql = "SELECT annotation_subset_id FROM annotation_types WHERE annotation_type_id = ?";
        $annotation_subset = $db->fetch_one($sql, array($annotation_type_id));
        return $annotation_subset;
    }

    //Find the annotation set that a given annotation subset belongs to.
    function annotationSubsetToSet($annotation_subset_id){
        global $db;

        $sql = "SELECT annotation_set_id FROM annotation_subsets  WHERE annotation_subset_id = ?";
        $annotation_set = $db->fetch_one($sql, array($annotation_subset_id));
        return $annotation_set;
    }

}
?>
