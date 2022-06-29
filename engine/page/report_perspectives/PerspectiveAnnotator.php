<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAnnotator extends CPerspective {

	function __construct(CPage $page, $document){
        parent::__construct($page, $document);
		$this->page->includeJs("libs/select2/js/select2.js");
		$this->page->includeCss("libs/select2/css/select2.min.css");

        $this->page->includeJs("js/c_widget_annotation_type_tree.js");
        $this->page->includeJs("js/c_widget_user_selection_a_b.js");
        $this->page->includeJs("js/c_annotation_mode.js");
        $this->page->includeJs("js/c_autoresize.js");
        $this->page->includeJs("js/c_widget_relation_sets.js");
        $this->page->includeJs("js/c_widget_annotation_details.js");
		$this->page->includeCss("css/c_widget_annotation_details.css");
        $this->page->includeJs("js/c_widget_annotation_panel.js");
        $this->page->includeJs("js/c_widget_annotation_relations.js");
        $this->page->includeJs("js/c_autoaccordionview.js");
        $this->page->includeJs("js/page_report_preview.js");
        $this->page->includeJs("libs/bootstrap-confirmation.min.js");
        $this->page->includeJs("js/page_report_annotation_tree_loader.js");
    }

    function execute(){
		global $user, $corpus;

		$anStage = "final";
		$an_source = null;
		$anUserIds = null;
		$annotation_mode = 'final';
        $report = $this->page->report;
        $corpusId = $corpus['id'];

		// Init global tables
		if (!is_array($this->annotationsClear)){
			$this->annotationsClear = array();
		}
		
		if ( isset($_COOKIE['annotation_mode']) ){
			$annotation_mode = $_COOKIE['annotation_mode'];
			if($annotation_mode != "final"){
			    $relation_mode = "agreement";
            } else{
			    $relation_mode = "final";
            }
		}

        $relationTypeIds = CookieManager::getRelationSets($corpusId);

        if ( isset($_POST['annotation_mode']) ){
			$annotation_mode = $_POST['annotation_mode'];
		}

        /*if ( isset($_COOKIE['stage_relations']) ){
            $relation_mode = $_COOKIE['stage_relations'];
        } else{
            $relation_mode = 'final';
        }*/
				
		/* Wymuś określony tryb w oparciu i prawa użytkownika */
		if ( hasCorpusRole(CORPUS_ROLE_ANNOTATE) && !hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT) ){
			$annotation_mode = "final";
		} else if ( !hasCorpusRole(CORPUS_ROLE_ANNOTATE) && hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT) ){
			$annotation_mode = "agreement";
		} else{
			/* Użytkownik nie ma dostępu do żadnego trybu */
			// ToDo: zgłosić brak prawa dostępu			
		}

		/* Ustaw an_stage i an_user_id na podstawie annotation_mode */					
		if ( $annotation_mode == "final" || $annotation_mode == "relation_agreement"){
			$anStage = "final";
		} else if ( $annotation_mode == "agreement"){
			$anStage = "agreement";
			$anUserIds = array($user['user_id']);
		}

		$anStages = array($anStage);

		$this->set_annotation_menu();
		$this->set_events();

        $htmlStr = ReportContent::getHtmlStr($report);
        $annotationTypes = CookieManager::getAnnotationTypeTreeAnnotationTypes($corpusId);
        $annotations = DbAnnotation::getReportAnnotations($report['id'], $anUserIds, null, null, $annotationTypes, $anStages, false);
        $relations = DbReportRelation::getReportRelations($this->page->cid, $this->page->id, $relationTypeIds, $annotationTypes, null,null, $annotation_mode);
        $htmlStr = ReportContent::insertAnnotationsWithRelations($htmlStr, $annotations, $relations);
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($report[DB_COLUMN_REPORTS__REPORT_ID]));

        $annotation_sets =  DbAnnotation::getAnnotationStructureByCorpora($corpusId);

        $this->loadAnnotationsAttributes($annotations);

        $html_content = Reformat::xmlToHtml($htmlStr->getContent());
        $this->page->set("content", $html_content);
        $this->page->set('annotation_types', $annotation_sets);
        $this->page->set('relation_sets', DbRelationSet::getRelationSetsAssignedToCorpus($corpusId, $anStage));
        $this->page->set("annotations", $annotations);
        $this->page->set("relations", $relations);
        $this->page->set("annotation_mode", $annotation_mode);

        /* Setup active accordion panel */
        $accordions = array("collapseConfiguration", "collapsePad", "collapseAnnotations", "collapseRelations");
        $activeAccordion = $_COOKIE['accordion_active'];
        if ( !in_array($activeAccordion, $accordions) ){
            $activeAccordion = $accordions[0];
        }
        $this->page->set("active_accordion", $activeAccordion);
	}

	function loadAnnotationsAttributes(&$annotations){
	    foreach ($annotations as &$an){
	        $attrs = DbAnnotation::getAnnotationSharedAttributes($an[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID]);
	        $vals = array();
	        foreach ( $attrs as $attr){
	            $vals[] = $attr['value'];
            }
	        $an['attributes'] = implode(", ", $vals);
        }
    }

	/**
	 * Set up twin panels.
	 */
	function set_panels(){
		$this->page->set('showRight', $_COOKIE['showRight']=="true"?true:false);
	}

	/**
	 *
	 */
	function set_annotation_menu(){
		global $db, $user;

		//Find out which annotation types are selected in view configuration
		$selected_annotation_types = CookieManager::getSelectedAnnotationTypeTreeAnnotationTypes($this->document['corpora']);
		$selected_types_string = implode(',',$selected_annotation_types);
		if(empty($selected_types_string)){
		    $selected_types_string = "NULL";
        }

		$sql = "SELECT t.*, s.name as `set`" .
				"	, ss.name AS subset" .
				"	, ss.annotation_subset_id AS subsetid" .
				"	, s.annotation_set_id AS groupid" .
				"	, t.shortlist AS common" .
				" FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
				" WHERE (c.corpus_id = {$this->document['corpora']} AND t.group_id IN ({$selected_types_string}))" .
				" ORDER BY `set`, subset, t.name";
		$annotation_types = $db->fetch_rows($sql);

        $sql = "SELECT * FROM annotation_types_shortlist ats WHERE ats.user_id = ?";
        $user_preferences = $db->fetch_rows($sql, array($user['user_id']));


        //Find out if user changed the visibility of any annotations
        foreach($user_preferences as $key=>$pref){
            $user_preferences[$pref['annotation_type_id']] = $pref;
            unset($user_preferences[$key]);
        }

        foreach($annotation_types as $key=>$a_type){
            $id = $a_type['annotation_type_id'];

            if(array_key_exists($id, $user_preferences)){
                if(($user_preferences[$id]['shortlist'] == 1 && $annotation_types[$key]['common'] == 0) || ($user_preferences[$id]['shortlist'] == 0 && $annotation_types[$key]['common'] == 1)){
                    $annotation_types[$key]['not_default'] = 1;
                } else {
                    $annotation_types[$key]['not_default'] = null;
                }

                if($user_preferences[$id]['shortlist'] == 1){
                    $annotation_types[$key]['common'] = 1;
                } else{
                    $annotation_types[$key]['common'] = 0;
                }
                continue;

            }
        }

		$annotation_grouped = array();
		$annotationsSubsets = array();
		foreach ($annotation_types as $an){
			$set = $an['group_id'];
			$set_name = $an['set'];
			$subset = $an['subset'] ? $an['subset'] : "none";
			if (!isset($annotation_grouped[$set])){
				$annotation_grouped[$set][$set_name] = array();
				$annotation_grouped[$set][$set_name]['groupid'] = $an['groupid'];
				$this->annotationsClear[] = $an['groupid'];
			}
			if (!isset($annotation_grouped[$set][$set_name][$subset])){
				$annotation_grouped[$set][$set_name][$subset] = array();
				$annotation_grouped[$set][$set_name][$subset]['subsetid'] = $an['subsetid'];
                //$annotation_grouped[$set][$set_name][$subset]['set_id'] = $an['group_id'];
				$annotation_grouped[$set][$set_name][$subset]['notcommon'] = !$an['common'];
				$annotationsSubsets[] = $an['subsetid'];
			}
			$annotation_grouped[$set][$set_name][$subset][$an['name']] = $an;
			$annotation_grouped[$set][$set_name][$subset]['notcommon'] |= !$an['common'];
		}
		if (!$_COOKIE['clearedLayer']){
			setcookie('clearedLayer', '{"id'.implode('":1,"id', $this->annotationsClear).'":1}');
			setcookie('clearedSublayer', '{"id'.implode('":1,"id', $annotationsSubsets).'":1}');
		}
        $this->page->set('annotation_types_tree', $annotation_grouped);
	}
	/**
	 *
	 */
	function set_events(){
		/*****obsluga zdarzeń********/
		//lista dostepnych grup zdarzen dla danego korpusu
		$sql = "SELECT DISTINCT event_groups.event_group_id, event_groups.name " .
				"FROM corpus_event_groups " .
				"JOIN event_groups " .
					"ON (corpus_event_groups.corpus_id={$this->page->cid} AND corpus_event_groups.event_group_id=event_groups.event_group_id) " .
				"JOIN event_types " .
					"ON (event_groups.event_group_id=event_types.event_group_id)";
		$event_groups = $this->page->getDb()->fetch_rows($sql);

		//lista zdarzen przypisanych do raportu
		$sql = "SELECT reports_events.report_event_id, " .
					  "event_groups.name AS groupname, " .
					  "event_types.name AS typename, " .
					  "event_types.event_type_id, " .
					  "count(reports_events_slots.report_event_slot_id) AS slots " .
					  "FROM reports_events " .
					  "JOIN reports " .
					  	"ON (reports_events.report_id={$this->page->id} " .
					  	"AND reports_events.report_event_id=reports.id) " .
				  	  "JOIN event_types " .
				  	  	"ON (reports_events.event_type_id=event_types.event_type_id) " .
			  	  	  "JOIN event_groups " .
			  	  	  	"ON (event_types.event_group_id=event_groups.event_group_id) " .
		  	  	  	  "LEFT JOIN reports_events_slots " .
		  	  	  	  	"ON (reports_events.report_event_id=reports_events_slots.report_event_id) " .
	  	  	  	  	  "GROUP BY (reports_events.report_event_id)";
		$events = $this->page->getDb()->fetch_rows($sql);
		$this->page->set('event_groups',$event_groups);
		$this->page->set('events',$events);

	}

	/**
	 * ToDo: Informacja o tym, jakie anotacje powinny być wyświetlone powinna być przekazana jako parametry funkcji.
	 * ToDo: Sql-e wymagają refaktoringu i przerobienia na bezpiecznie wywołanie.
	 * @param $stage
	 * @param $source
	 * @param $user_id
	 * @param $force_annotation_set_id Ignore information from cookies and display given set of annotations.
	 */
	function set_annotations($stage=null, $source=null, $user_id=null, $force_annotation_set_id=null, $char_from=null, $char_to=null)
	{
		$subpage = $this->page->subpage;
		$id = $this->page->id;
		$cid = $this->page->cid;
		$row = $this->page->row;
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text, t.group_id, " .
				"	ans.name setname, ansub.name subsetname, " .
				"	ansub.annotation_subset_id, " .
				"	t.name typename, " .
				"	t.short_description typedesc, " .
				"	an.stage, t.css, an.source"  .
				" FROM reports_annotations an" .
				" LEFT JOIN annotation_types t ON (an.type=t.name)" .
				" LEFT JOIN annotation_subsets ansub ON (t.annotation_subset_id=ansub.annotation_subset_id)" .
				" LEFT JOIN annotation_sets ans on (t.group_id=ans.annotation_set_id)" .
				" WHERE report_id = {$row['id']} " .
				" AND ans.annotation_set_id IN" .
					"(SELECT annotation_set_id " .
					"FROM annotation_sets_corpora  " .
					"WHERE corpus_id=$cid)";

		if ( $stage != null ){
			$sql .= " AND an.stage = '$stage'";
		}

		if ( $source != null ){
			$sql .= " AND an.source = '$source'";
		}

		if ( $user_id != null){
			$sql .= " AND an.user_id = $user_id";
		}

		$sql2 = $sql;
		$sql3 = $sql;

		if ( $force_annotation_set_id ){
			$sql .= " AND group_id = " . intval($force_annotation_set_id);
			$sql2 = $sql;
		}
		else{
			// Ustaw filtrowanie anotaci na podstawie cookies
			if (!$_COOKIE['clearedLayer'] && count($this->annotationsClear)>0){
				$sql = $sql . ' AND group_id ' .
						'NOT IN (' . implode(", ", $this->annotationsClear) . ') ' ;
				$sql2 = $sql;
			}
			elseif ($_COOKIE['clearedLayer'] && $_COOKIE['clearedLayer']!="{}"){
				$set = preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedLayer']);
				$set = trim($set, ", ");
				$set = preg_replace("/,+/", ",", $set);
				if ( trim($set) != "" )
				$sql = $sql . " AND group_id NOT IN ( $set) " ;
				$sql2 = $sql;
			}
			if ($_COOKIE['clearedSublayer'] && $_COOKIE['clearedSublayer']!="{}"){
				$set = preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedSublayer']);
				$set = trim($set, ", ");
				$set = preg_replace("/,+/", ",", $set);
				if ( trim($set) != "" ){
					$sql = $sql . " AND (ansub.annotation_subset_id NOT IN ($set) " .
									"OR ansub.annotation_subset_id IS NULL) ";
					$sql2 = $sql;
				}
			}

			if ($_COOKIE['rightSublayer'] && $_COOKIE['rightSublayer']!="{}"){
				$set = preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['rightSublayer']);
				$set = trim($set, ", ");
				$set = preg_replace("/,+/", ",", $set);
				if ( trim($set) != " ") {
					$sql = $sql . " AND ansub.annotation_subset_id NOT IN ($set) " ;
					$sql2 = $sql2 . " AND (ansub.annotation_subset_id IN ($set) " .
									"OR ansub.annotation_subset_id IS NULL) ";
				}

			}
			else {
				$sql2 = $sql2 . " AND ansub.annotation_subset_id=0 ";
			}
		}

		$sql = $sql . " ORDER BY t.level ASC, t.name, `from` ASC, `len` DESC";
		$sql2 = $sql2 . " ORDER BY `from` ASC, `len` DESC";
		$sql3 = $sql3 . " ORDER BY `from` ASC";

		$anns = $this->page->getDb()->fetch_rows($sql);
		$anns2 = $this->page->getDb()->fetch_rows($sql2);
		$anns3 = $this->page->getDb()->fetch_rows($sql3);

		$annotation_set_map = array();
		foreach ($anns3 as $as){
			$setName = $as['setname'];
			$subsetName = $as['subsetname']==NULL ? "!uncategorized" : $as['subsetname'];
			$anntype = $as['typename'];
			if ($annotation_set_map[$setName][$subsetName][$anntype]==NULL){
				$annotation_set_map[$setName][$subsetName]['subsetid'] = $as['annotation_subset_id'];
				$annotation_set_map[$setName][$subsetName][$anntype] = array();
				$annotation_set_map[$setName][$subsetName][$anntype]['description']=$as['typedesc'];
				$annotation_set_map[$setName]['groupid']=$as['group_id'];
			}
			array_push($annotation_set_map[$setName][$subsetName][$anntype], $as);
		}

		$exceptions = array();

		$content = $row['content'];
		// Escape html special characters for plain format
		if ( $row['format'] == 'plain'){
			$content = htmlspecialchars($content);
		}
		$content2 = $content;

		try{
			$htmlStr =  new HtmlStr2($content, true);
			$htmlStr2 = clone $htmlStr;

			$sql_relations = "SELECT an.*, at.group_id, r.source_id, r.target_id, t.name" .
								" FROM relations r" .
								" JOIN reports_annotations an ON (r.source_id=an.id)" .
								" JOIN relation_types t ON (r.relation_type_id=t.id)" .
								" JOIN annotation_types at ON (an.type=at.name)" .
								" WHERE an.report_id = ?" .
								($_COOKIE['active_annotation_types'] && $_COOKIE['active_annotation_types']!="{}"
									? " AND (t.relation_set_id IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['active_annotation_types']) . ") OR t.name='Continous') "
									: "") .
								" ORDER BY an.to ASC";
			$relations = $this->page->getDb()->fetch_rows($sql_relations, array($id));

			$show_relation["leftContent"] = array();
			$show_relation["rightContent"] = array();
			foreach ($anns as $token){
				$show_relation["leftContent"][$token['id']] = array();
			}
			foreach ($anns2 as $token){
				$show_relation["rightContent"][$token['id']] = array();
			}

			foreach ($relations as $r){
				if(array_key_exists($r['source_id'],$show_relation["leftContent"]) && array_key_exists($r['target_id'],$show_relation["leftContent"]))
					$show_relation["leftContent"][$r['source_id']][] = "<sup class='rel' title='".$r['name']."' sourcegroupid='".$r['source_id']."' target='".$r['target_id']."'/></sup>";
				if(array_key_exists($r['source_id'],$show_relation["rightContent"]) && array_key_exists($r['target_id'],$show_relation["rightContent"]))
					$show_relation["rightContent"][$r['source_id']][] = "<sup class='rel' title='".$r['name']."' sourcegroupid='".$r['source_id']."' target='".$r['target_id']."'/></sup>";
			}


			$htmlStr = ReportContent::insertAnnotations($htmlStr, $anns);
			$htmlStr2 = ReportContent::insertAnnotations($htmlStr2, $anns2);

			/** Dodanie zaznaczenia określonego zakresu znaków **/
			if ( intval($char_from) && intval($char_to) && $char_from < $char_to ){
				for ( $i = $char_from; $i<=$char_to; $i++){
					$htmlStr->insertTag($i, "<u>", $i+1, "</u>");
				}
			}

			/** Wstawienie tokenów */
			$content = $htmlStr->getContent();
			$content2 = $htmlStr2->getContent();
			$tokens = DbToken::getTokenByReportId($id);

			if ( count($tokens) > 11000 ){
				$exceptions[] = "<b>Tokenization was not displayed</b> — too many tokens (" .count($tokens). ").";
			}
			else{
				$htmlStr = ReportContent::insertTokens($htmlStr, $tokens);
				/** Jeżeli nie wystąpiły problemy ze wstawieniem tokenizacji,
				 * to podmień treść dokumentu do wyświetlenia. */
				if ( count(ReportContent::$exceptions) == 0){
					$content = $htmlStr->getContent();
					$content2 = $htmlStr2->getContent();
				}
				else{
					$exceptions[] = "<b>Tokenization was not displayed</b> — unknown tokenization error (retokenization might be required).";
					$exceptions = array_merge($exceptions, ReportContent::$exceptions);
				}
			}
		}
		catch (Exception $ex){
			$exceptions[] = $ex->getMessage();
		}

		if ( count($exceptions) > 0 ){
			$this->page->set("exceptions", $exceptions);
		}

		$this->page->set('sets', $annotation_set_map);
		$this->page->set('content_inline', Reformat::xmlToHtml($content));
		$this->page->set('content_inline2', Reformat::xmlToHtml($content2));
		$this->page->set('anns',$anns);
	}
}

?>
