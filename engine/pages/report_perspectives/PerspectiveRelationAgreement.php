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

        $annotator_a_id = intval($_COOKIE[$corpus_id.'relation_agreement_annotator_a_id']);
        $annotator_b_id = intval($_COOKIE[$corpus_id.'relation_agreement_annotator_b_id']);

        ChromePhp::log("annotators");
        ChromePhp::log($annotator_a_id . ", " . $annotator_b_id);

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

        ChromePhp::log("Annotations");
        ChromePhp::log($annotations);

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
        $groups = DbAnnotation::groupAnnotationsByRanges($annotations, $annotator_a_id, $annotator_b_id);

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

        if($annotation_types != null && $relation_types != null){
            $users = DbRelationSet::getUserRelationCount($report_id, $annotation_types, $relation_types);
        } else{
            $users = null;
        }
        $this->page->set("users", $users);

    }

    function handlePost(){
        global $user;
        global $db;
        $user_id = $user[DB_COLUMN_USERS__USER_ID];

        foreach ( $_POST as $key=>$val){

            /** Dodanie nowej anotacji */
            if ( preg_match('/range_([0-9]+)_([0-9]+)_([0-9]+)\/([0-9]+)_([0-9]+)_([0-9]+)(_[a-z]+)?/', $key, $match) ){
                $source_id = intval($match[3]);
                $target_id = intval($match[6]);
                $type_id = null;

                if ( preg_match('/add_([0-9]+)/', $val, $match_val) ){
                    /* Dodanie anotacji jako określony typ */
                    $type_id = intval($match_val[1]);
                }
                else if ($val == "add_short"){
                    /* Dodanie anotacji określonego typu, typ anotacji podany jest w osobej zmiennej */
                    $type_id_val = $key . "_type_id_short";
                    if ( isset($_POST[$type_id_val]) && intval($_POST[$type_id_val]) > 0 ){
                        $type_id = intval($_POST[$type_id_val]);
                    }
                }
                else if ($val == "add_full"){
                    /* Dodanie anotacji określonego typu, typ anotacji podany jest w osobej zmiennej */
                    $type_id_val = $key . "_type_id_full";
                    if ( isset($_POST[$type_id_val]) && intval($_POST[$type_id_val]) > 0 ){
                        $type_id = intval($_POST[$type_id_val]);
                    }
                }

                if ( $type_id !== null ){
                    $attributes = array(
                        'relation_type_id'=>$type_id,
                        'source_id'=>$source_id,
                        'target_id'=>$target_id,
                        'user_id'=>$user_id,
                        'date'=>date('Y-m-d'),
                        'stage'=>'final'
                    );
                    $db->replace('relations', $attributes);
                }
            }
            /** Operacje na istniejącej anotacji */
            else if ( preg_match('/relation_id_([0-9]+)/', $key, $match) ){
                $relation_id = intval($match[1]);
                if ( $val == "delete" ){
                    /* Usunięcie anotacji */
                    DbRelationSet::deleteRelation($relation_id);
                }
                else if ( $val == "change_select" ){
                    /* Zmiana typu anotacji na wartość z pola ${key}_select */
                    $type_id = intval($_POST[$key . "_select"]);
                    DbRelationSet::updateRelation($relation_id, $type_id);
                }
                else if ( preg_match('/change_([0-9]+)/', $val, $match_val) ){
                    $type_id = intval($match_val[1]);
                    DbRelationSet::updateRelation($relation_id, $type_id);
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

        ChromePhp::log("Relations");
        ChromePhp::log($relation_types);

        $available_annotations = DbRelationSet::getAnnotationsOfRelations($relation_types, $report_id);
        ChromePhp::log($available_annotations);


        $relations = DbRelationSet::getRelationTree($corpus_id, $report_id);

        $annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);

        $available_annotations_list = array();
        foreach($available_annotations as $available_annotation){
            $available_annotations_list[] = $available_annotation['annotation_set_id'];
        }

        ChromePhp::log($available_annotations_list);
        foreach($annotations as $an_set_id => $annotation){
            if(!in_array($an_set_id, $available_annotations_list)){
                unset($annotations[$an_set_id]);
            }
        }

        ChromePhp::log($annotations);

        $this->page->set('annotation_types',$annotations);
        $this->page->set('relation_types', $relations);
    }


		
}

