<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveAgreement extends CPerspective {
	
	function execute(){

        $this->page->includeJs("js/c_autoresize.js");

		global $corpus;
		
		$corpus_id = $corpus['id'];
		$report_id = $this->document[DB_COLUMN_REPORTS__REPORT_ID];
		
		$annotator_a_id = intval($_COOKIE[$corpus_id.'_annotator_a_id']);
		$annotator_b_id = intval($_COOKIE[$corpus_id.'_annotator_b_id']);
		
		$this->setup_annotation_type_tree($corpus_id);
		
		$annotation_types_str = trim(strval($_COOKIE[$corpus_id . '_annotation_lemma_types']));
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
		$users = DbAnnotation::getUserAnnotationCount(null, null, array($report_id), null, $annotation_types, null, "agreement");
		ChromePhp::log($users);

		if ( isset($_POST['submit']) ){
			$this->handlePost();
		}
		
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
		$groups = $this->groupAnnotationsByRanges($annotations, $annotator_a_id, $annotator_b_id);
		
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
		
		/** Output variables to the template */
		$this->page->set("users", $users);
		$this->page->set("annotations", $annotations);
		$this->page->set("groups", $groups);
		$this->page->set("content_inline", $html->getContent());
		$this->page->set("available_annotation_types", DbAnnotation::getAnnotationTypesByIds($annotation_types));
		$this->page->set("annotator_a_id", $annotator_a_id);
		$this->page->set("annotator_b_id", $annotator_b_id);
	}

	/**
	 * Ustaw strukturę dostępnych typów anotacji.
	 * @param unknown $corpus_id
	 */
	private function setup_annotation_type_tree($corpus_id){
		$annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);
		$this->page->set('annotation_types',$annotations);
	}
	
	/**
	 * Obsługa rządania POST.
	 */
	function handlePost(){
		global $user;
		$user_id = $user[DB_COLUMN_USERS__USER_ID];
		$report_id = $this->document[DB_COLUMN_REPORTS__REPORT_ID];
		$html = new HtmlStr2($this->document[DB_COLUMN_REPORTS__CONTENT]);
				
		foreach ( $_POST as $key=>$val){
			/** Dodanie nowej anotacji */
			if ( preg_match('/range_([0-9]+)_([0-9]+)(_[a-z]+)?/', $key, $match) ){
				$from = intval($match[1]);
				$to = intval($match[2]);
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
					$text = $html->getText($from, $to);
					$attributes = array(
						'report_id'=>$report_id,
						'type_id'=>$type_id,
						'from'=>$from,
						'to'=>$to,
						'text'=>$text,
						'user_id'=>$user_id,
						'source'=>'user',
						'stage'=>'final'
					);
					db_replace(DB_TABLE_REPORTS_ANNOTATIONS, $attributes);						
				}
			}
			/** Operacje na istniejącej anotacji */
			else if ( preg_match('/annotation_id_([0-9]+)/', $key, $match) ){
				$annotation_id = intval($match[1]);
				if ( $val == "delete" ){
					/* Usunięcie anotacji */
					fb($annotation_id);
					DbAnnotation::deleteReportAnnotation($report_id, $annotation_id);
				}
				else if ( $val == "change_select" ){
					/* Zmiana typu anotacji na wartość z pola ${key}_select */
					$type_id = intval($_POST[$key . "_select"]);
					db_update(DB_TABLE_REPORTS_ANNOTATIONS, array("type_id"=>$type_id), array(DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID=>$annotation_id));
				}
				else if ( preg_match('/change_([0-9]+)/', $val, $match_val) ){
					$type_id = intval($match_val[1]);
					db_update(DB_TABLE_REPORTS_ANNOTATIONS, array("type_id"=>$type_id), array(DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID=>$annotation_id));
				}
			}
		}
		
		/* HACK: przeładowanie strony, aby nie było możliwe odświeżenie POST */
		$id = $_GET['id'];
		$corpus = $_GET['corpus'];
		header("Location: index.php?page=report&corpus=$corpus&subpage=agreement&id=$id");
		ob_clean();
		
	}
	
	/**
	 * Grupowanie anotacji po zakresie
	 * @param unknown $annotations
	 */
	function groupAnnotationsByRanges($annotations, $user_id1, $user_id2){
		$groups = array();
		$last_range = "";
		foreach ($annotations as $an){
			if ( $an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id1 
					|| $an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id2
					|| $an[DB_COLUMN_REPORTS_ANNOTATIONS__STAGE] == "final"){
				$range = sprintf("%d:%d", $an[DB_COLUMN_REPORTS_ANNOTATIONS__FROM], $an[DB_COLUMN_REPORTS_ANNOTATIONS__TO]);
				if ( $range != $last_range ){
					$group = array();
					$group[DB_COLUMN_REPORTS_ANNOTATIONS__FROM] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__FROM];
					$group[DB_COLUMN_REPORTS_ANNOTATIONS__TO] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__TO];
					$group[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT];
					$group["user1"] = null;
					$group["user2"] = null;
					$group["final"] = null;
					$groups[] = $group;
				}
				$last_range = $range;
				$type = array();
				$type[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID];
				$type[DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID];
				$type["type"] = $an['type'];
				
				if ( $an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id1 ){
					$groups[count($groups)-1]["user1"] = $type;
				}
				else if ($an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id2){
					$groups[count($groups)-1]["user2"] = $type;
				}
				else if ($an[DB_COLUMN_REPORTS_ANNOTATIONS__STAGE] == "final"){
					$groups[count($groups)-1]["final"] = $type;
				}
			}
		}
		return $groups;
	}
		
}