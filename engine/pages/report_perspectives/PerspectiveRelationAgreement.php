<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveRelation_agreement extends CPerspective {
	
	function execute(){
        global $corpus;


        $corpus_id = $corpus['id'];
        $report_id = $this->document[DB_COLUMN_REPORTS__REPORT_ID];

        $this->page->includeJs('js/c_widget_annotation_type_tree.js');

        $annotator_a_id = intval($_COOKIE["agreement_relations_" . $corpus_id . "_annotator_id_a"]);
        $annotator_b_id = intval($_COOKIE["agreement_relations_" . $corpus_id . "_annotator_id_b"]);
        $relation_types_str = trim(strval($_COOKIE[$corpus_id . '_relation_lemma_types']));

        $relation_types_array = explode(",", $relation_types_str);
        $relation_types = null;
        if ( $relation_types_str ) {
            $relation_types = array();
            foreach ($relation_types_array as $id) {
                $id = intval($id);
                if ($id > 0) {
                    $relation_types[] = $id;
                }
            }
        }

        $annotation_types_str = trim(strval($_COOKIE[$corpus_id . '_relations_annotation_lemma_types']));
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

        //Annotation grouping
        $annotations = array();

        if ( $annotator_a_id > 0 && $annotator_b_id > 0 && $annotator_a_id != $annotator_b_id && $annotation_types !== null ){
            $annotations = DbAnnotation::getReportAnnotations($report_id, null, null, null, $annotation_types);
        }

        /** Posortuj anotacje po granicach */
        usort($annotations, function($a, $b){
            if ( $a[DB_COLUMN_REPORTS_ANNOTATIONS__FROM] < $b[DB_COLUMN_REPORTS_ANNOTATIONS__FROM] ){
                return -1;
            }
            else if ( $a[DB_COLUMN_REPORTS_ANNOTATIONS__FROM] > $b[DB_COLUMN_REPORTS_ANNOTATIONS__FROM] ){
                return 1;
            }
            else if ( $a[DB_COLUMN_REPORTS_ANNOTATIONS__TO] < $b[DB_COLUMN_REPORTS_ANNOTATIONS__TO] ){
                return -1;
            }
            else if ( $a[DB_COLUMN_REPORTS_ANNOTATIONS__TO] > $b[DB_COLUMN_REPORTS_ANNOTATIONS__TO] ){
                return 1;
            }
            else{
                return 0;
            }
        });

        /*  */
        $groups = DbAnnotation::groupAnnotationsByRangesOld($annotations, $annotator_a_id, $annotator_b_id);

        /** Insert annotation parts into the content */
        $content = $this->document[DB_COLUMN_REPORTS__CONTENT];
        $spans = array();
        foreach ( $groups as $group ){
            $from = $group['from'];
            $to = $group['to'];
            for ($i=$from; $i<=$to; $i++){
                $spans[$i] = 1;
            }
        }
        $html = new HtmlStr2($content);
        foreach ( array_keys($spans) as $index ){
            $html->insertTag($index, "<span class='token{$index}'>", $index+1, "</span>");
        }
        $this->page->set("content_inline", $html->getContent());
        //Annotation grouping


        if($relation_types != null && $annotation_types != null){
            $final_relations = DbRelationSet::getFinalRelations($report_id, $relation_types, $annotation_types);
            $relations = DbRelationSet::getRelationAgreement($report_id, $relation_types, $annotator_a_id, $annotator_b_id, $final_relations, $annotation_types);
            $this->page->set('relation_agreement', $relations);
        }
        $this->set_up_annotation_and_relation_trees($corpus_id, $relation_types_array, $report_id);

        if ( isset($_POST['submit']) ){
            $this->handlePost();
        }

        $users = DbRelationSet::getUserRelationCount($report_id, $annotation_types, $relation_types);
        $this->page->set("users", $users);

    }


    function handlePost(){
        global $user;
        global $db;
        $user_id = $user[DB_COLUMN_USERS__USER_ID];

        $prepared_relations = array();

        foreach ( $_POST as $key=>$val){
            /** Dodanie nowej anotacji */
            if ( preg_match('/range_([0-9]+)_([0-9]+)_([0-9]+)\/([0-9]+)_([0-9]+)_([0-9]+)(_[a-z]+)?\b/', $key, $match) ){
                if(!isset($prepared_relations[$key]))(
                    $prepared_relations[$key]['action'] = $val
                );
            }
            else if(preg_match('/range_([0-9]+)_([0-9]+)_([0-9]+)\/([0-9]+)_([0-9]+)_([0-9]+)_([0-9]+)(_[\S]+)?/', $key, $match)){
                $parent_range = "range_{$match[1]}_{$match[2]}_{$match[3]}/{$match[4]}_{$match[5]}_{$match[6]}";

                $action = $match[8];
                if($action == "_type_id_add_full"){
                    $action = "add_full";
                } else if($action == '_type_id_delete'){
                    $action = "del_final";
                } else{
                    $action = "nop";
                }

                $relation_type_id = $match[7];
                $source_id = intval($match[3]);
                $target_id = intval($match[6]);
                $attributes = array(
                    'relation_type_id'=>$relation_type_id,
                    'source_id'=>$source_id,
                    'target_id'=>$target_id,
                    'user_id'=>$user_id,
                    'date'=>date('Y-m-d'),
                    'stage'=>'final'
                );

                if(isset($prepared_relations[$parent_range])) {
                    if($prepared_relations[$parent_range]['action'] == $action){
                        $prepared_relations[$parent_range]['relations'][] = $attributes;
                    }
                }
            }
        }

        foreach($prepared_relations as $relation){
            if($relation['action'] == 'add_full'){
                if(isset($relation['relations'])){
                    foreach($relation['relations'] as $insert_relation){
                        DbRelationSet::insertFinalRelation($insert_relation);
                    }
                }
            } else if($relation['action'] == 'del_final'){
                if(isset($relation['relations'])) {
                    foreach ($relation['relations'] as $insert_relation) {
                        DbRelationSet::deleteRelation($insert_relation);
                    }
                }
            }
        }

        /* HACK: przeładowanie strony, aby nie było możliwe odświeżenie POST */
        $id = $_GET['id'];
        $corpus = $_GET['corpus'];
        header("Location: index.php?page=report&corpus=$corpus&subpage=relation_agreement&id=$id");
        ob_clean();

    }

    private function set_up_annotation_and_relation_trees($corpus_id, $relation_types, $report_id){
        $relations = DbRelationSet::getRelationTree($corpus_id, $report_id);
        $annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);

        $this->page->set('annotation_types',$annotations);
        $this->page->set('relation_types', $relations);
    }


		
}

