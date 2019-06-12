<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_edit_add extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyPerspectiveAccess[] = 'annotation_lemma';
    }

    function execute(){
		global $user, $db;

		if (!intval($user['user_id'])){
			throw new Exception("Missing user identifier");
		}

		$name_str = $this->getRequestParameter('name_str');
		$desc_str = $this->getRequestParameter('desc_str');
		$description = $this->getRequestParameter('description');
        $setVisibility = $this->getRequestParameter('setAccess_str');
		$element_type = $this->getRequestParameter('element_type');
		$parent_id = intval($this->getRequestParameter('parent_id'));
        $corpus = $this->getRequestParameter('corpus');
        $custom_annotation = $this->getRequestParameter('customAnnotation');

		$user_id = $user['user_id'];

        $sql = "";
        $params = array();

		if ($element_type=="annotation_set"){
            $access = ($setVisibility == 'public' ? 1 : 0);
			$sql = 'INSERT INTO annotation_sets (name, description, public, user_id) VALUES (?, ?, ?, ?);';
			$params = array($desc_str, $description, $access, $user_id);
		}
		else if ($element_type=="annotation_subset"){
			$sql = 'INSERT INTO annotation_subsets (name, description, annotation_set_id) VALUES (?, ?, ?)';
			$params = array($desc_str, $description, $parent_id);
		}
		else if ($element_type=="annotation_type"){
			$group_id = $_POST['set_id'];
			$level = 0;
			$short_description = $_POST['short'];
            $shortlist = ($_POST['visibility'] == 'Hidden' ? 1 : 0);
			$css = $_POST['css'];
			$sql = 'INSERT INTO annotation_types (name,  description,annotation_subset_id, group_id, level, short_description, css, shortlist) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
			$params = array($name_str, $desc_str, $parent_id, $group_id, $level, $short_description, $css, $shortlist);
		}
				
		$db->execute($sql, $params);
		$last_id = $db->last_id();

		// Assign annotation set to corpora if called from corpus settings -> custom annotation sets.
        if( $element_type=="annotation_set" && $custom_annotation != null ){
            $sql = "INSERT INTO annotation_sets_corpora(annotation_set_id, corpus_id) VALUES (?, ?);";
            $params = array($last_id, $corpus);
            $db->execute($sql, $params);
        }

		return array("last_id"=>$last_id, "user" => $user['screename'], 'user_id' => $user_id);
	}
	
}