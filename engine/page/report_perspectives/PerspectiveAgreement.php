<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveAgreement extends CPerspective {

    private $rr = null; // resource meter for debugging

    function __construct(CPage $page, $document)
    {
        parent::__construct($page, $document);
        $this->page->includeJs("js/c_widget_annotation_type_tree.js");
        $this->page->includeJs("js/c_widget_user_selection_a_b.js");
    }
	
	function execute(){
        global $corpus;

		$corpus_id = $corpus['id'];
		$report_id = $this->document[DB_COLUMN_REPORTS__REPORT_ID];
		
		$annotator_a_id = intval($_COOKIE['agreement_annotations_'.$corpus_id.'_annotator_id_a']);
		$annotator_b_id = intval($_COOKIE['agreement_annotations_'.$corpus_id.'_annotator_id_b']);
		
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

		$available_annotation_types = DbAnnotation::getAnnotationTypesByIds($annotation_types);
		/*  */
		$groups = DbAnnotation::groupAnnotationsByRanges($annotations, $annotator_a_id, $annotator_b_id, $available_annotation_types);

        /** Insert annotation parts into the content */
        $spans = array();
        foreach ( $groups as $group ){
            $from = $group['from'];
            $to = $group['to'];
            for ($i=$from; $i<=$to; $i++){
                $spans[$i]['annotations'] = $group['all_annotations'];
                $spans[$i]['text'] = $group['text'];
            }
        }

        $html = ReportContent::getHtmlStr($this->page->report);
        $errors = array();

        foreach ($spans as $index => $information){
            try {
                $html->insertTag($index, "<span class='token{$index}'>", $index+1, "</span>");
            } catch (exception $e) {
                $exception = $e->getMessage();
                $exception .= "; Text: '" . $information['text']."'";
                foreach($information['annotations'] as $ann){
                    $exception .= "; " . $ann['type'] . " (id=" . $ann['type_id'].")";
                }
                $errors[] = $exception;
            }
        }
        
		/** Output variables to the template */
		$this->page->set("users", $users);
		$this->page->set("errors", $errors);
		$this->page->set("annotations", $annotations);
		$this->page->set("groups", $groups);
		$this->page->set("content_inline", $html->getContent());
		$this->page->set("available_annotation_types", $available_annotation_types);
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
	 * Obsługa żądania POST.
	 */
	function handlePost(){
		global $user;

        // start resources debugging
        $this->rr = new ReportResources();

		$user_id = $user[DB_COLUMN_USERS__USER_ID];
		$report_id = $this->document[DB_COLUMN_REPORTS__REPORT_ID];
		$html = new HtmlStr2($this->document[DB_COLUMN_REPORTS__CONTENT]);
        $this->rr->sendReportResourceDiffToLog("1-st step HtmlStr2 loads document");

        $prepared_annotations = array();
		foreach ( $_POST as $key=>$val){
			/** Dodanie nowej anotacji */
			if ( preg_match('/([0-9]+):([0-9]+)\b/', $key, $match) ){
                if(!isset($prepared_annotations[$key]))(
                $prepared_annotations[$key]['action'] = $val
                );

			} else if(preg_match('/([0-9]+):([0-9]+)_([0-9]+)(_[\S]+)\/(.+)/', $key, $match)){
                $from = $match[1];
                $to = $match[2];
                $parent_range = $from.":".$to;
                $action = $match[5];
                $text = $html->getText($from, $to);
                $annotation_type_id = $match[3];

                $attributes = array(
                    'report_id'=>$report_id,
                    'type_id'=>$annotation_type_id,
                    'from'=>$from,
                    'to'=>$to,
                    'text'=>$text,
                    'user_id'=>$user_id,
                    'stage'=>'final',
                    'source'=>'user'
                );

                if(isset($prepared_annotations[$parent_range])) {
                    if($prepared_annotations[$parent_range]['action'] == $action){
                        if($action == "delete"){
                            $attributes['annotation_id'] = $match[3];
                        }

                        $prepared_annotations[$parent_range]['annotations'][] = $attributes;
                    }
                }
            }
		}
        $this->rr->sendReportResourceDiffToLog("2-nd step prepare annotation");

		foreach($prepared_annotations as $prepared_annotation){
		    if(!isset($prepared_annotation['annotations'])){
		        continue;
            } else{
		        foreach($prepared_annotation['annotations'] as $annotation){
                    if($prepared_annotation['action'] == "add_full"){
                        if($annotation['stage'] == 'final') {
                            $this->insertAnnotationNoduplicates($annotation);
                        } else {
                            $this->page->getDb()->replace(DB_TABLE_REPORTS_ANNOTATIONS, $annotation);
                        }
                    } else{
                       DbAnnotation::deleteReportAnnotation($report_id, $annotation['annotation_id']);
                    }
                }
            }
        }
        $this->rr->sendReportResourceDiffToLog("3-rd step store to database");

		/* HACK: przeładowanie strony, aby nie było możliwe odświeżenie POST */
		$id = $_GET['id'];
		$corpus = $_GET['corpus'];
		header("Location: index.php?page=report&corpus=$corpus&subpage=agreement&id=$id");
		ob_clean();

        $this->rr->sendReportResourceDiffToLog("leaving PerspectiveAgreement->handlePost() method");
		
	}

    private function insertAnnotationNoduplicates($annotation) {
        // INSERT annotation row to DB, only if row with same attrs
        // does not exists, without primary key
        if( is_array($annotation) 
            and isset($annotation['report_id'])
            and isset($annotation['type_id'])
            and isset($annotation['from'])
            and isset($annotation['to'])
            and isset($annotation['text'])
            and isset($annotation['user_id'])
            and isset($annotation['stage'])
            and isset($annotation['source'])
        ){
            
            $sql = "INSERT INTO `".DB_TABLE_REPORTS_ANNOTATIONS."` ".
            "(`report_id`,`type_id`,`from`,`to`,`text`,`user_id`,`stage`, ".
            "`source`) SELECT ?,?,?,?,?,?,?,? FROM dual WHERE NOT EXISTS ".
            "( SELECT `id` FROM `".DB_TABLE_REPORTS_ANNOTATIONS."` ".
            "WHERE `report_id`=? AND `type_id`=? AND `from`=? AND `to`=? ".
            "AND `text`=? AND `user_id`=? AND `stage`=? AND `source`=? )";
            $params = array($annotation['report_id'],$annotation['type_id'],
                $annotation['from'], $annotation['to'], $annotation['text'],
                $annotation['user_id'], $annotation['stage'], 
                $annotation['source'],
                $annotation['report_id'],$annotation['type_id'],
                $annotation['from'], $annotation['to'], $annotation['text'],
                $annotation['user_id'], $annotation['stage'],                                   $annotation['source']);
            $this->page->getDb()->execute($sql, $params);
        }

    } // insertAnnotationNoduplicates()

}
