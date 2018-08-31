<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveImportAnnotations extends CPerspective {

    function execute(){
        global $corpus, $user;

        $report = $this->page->report;
        $corpus_id = $corpus['id'];
        $annotation_sets = DbAnnotationSet::getAnnotationSetsAssignedToCorpus($corpus_id);

        $this->handleErrors();

        $selected_annotation_set = $_COOKIE['view_annotation_set'];
        $selected_stage = $_COOKIE['view_annotation_stage'];

        if($selected_annotation_set != null && $selected_stage == null){
            $selected_stage = 'new';
        }

        ChromePhp::log($selected_annotation_set, $selected_stage);
        $stages = array(
            'New' => 'new',
            'Final' => "final",
            'Agreement' => "agreement"
        );


        $htmlStr = ReportContent::getHtmlStr($report);
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($report['id']));
        if($selected_annotation_set == "-" || $selected_annotation_set == null){
            $annotations = array();
        } else{
            $annotations = DbAnnotation::getReportAnnotations($report['id'], $user['id'], array($selected_annotation_set), null, null, array($selected_stage), false);
        }

        $logged_user = DbUser::get($user['user_id']);
        $corpus_owner = DbUser::get(DbCorpus::getOwnerId($corpus['id']));

        $users = array_filter(DbCorporaUsers::getCorpusUsers($corpus_id), function($user) use($logged_user, $corpus_owner) {
           return $user['user_id'] !== $logged_user['user_id'] and $user['user_id'] !== $corpus_owner['user_id'];
        });

        $users[] = $corpus_owner;
        usort($users, function($a, $b){
            return strcmp($a['screename'], $b['screename']);
        });

        $htmlStr = ReportContent::insertAnnotations($htmlStr, $annotations);

        $this->page->set('annotation_sets', $annotation_sets);
        $this->page->set("content", Reformat::xmlToHtml($htmlStr->getContent()));
        $this->page->set("selected_set", $selected_annotation_set);
        $this->page->set("selected_stage", $selected_stage);
        $this->page->set("stages", $stages);

        $this->page->set("logged_user", $logged_user);
        $this->page->set("users", $users);
    }

    private function handleErrors(){
        if(isset($_SESSION['importannotations']['error'])){
            $error = "<br> Unknown annotation type: <strong>".$_SESSION['importannotations']['error']."</strong> - Aborted.";
            $this->page->set("error", $error);
            unset($_SESSION['importannotations']['error']);
        }
    }




}

?>
