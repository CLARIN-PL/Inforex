<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAnnotator_wsd extends CPerspective {

	function __construct(CPage $page, $document)
    {
        parent::__construct($page, $document);
        $this->page->includeJs("js/c_annotation_mode.js");
    }

    function execute(){
		global $corpus, $user;

		$corpus_id = $corpus['id'];
        $user_id = intval($user['user_id']);

		$word = $_GET['wsd_word'];
		$word_annotation_type_id = $_GET['annotation_type_id'];
		$rid  = intval($_GET['id']);
		$annotation_id = intval($_GET['aid']);
        $annotation_sets = DbAnnotationSet::getAnnotationSetsWithWSD();
        $selected_annotation_set = CookieManager::getAnnotatorWSDAnnotationSet();
        if($selected_annotation_set == null){
        	$selected_annotation_set = 2;
		}

        $annotation_mode = "final";
        if ( isset($_COOKIE['annotation_mode_wsd']) ){
            $annotation_mode = $_COOKIE['annotation_mode_wsd'];
        }
        else{
            setcookie("annotation_mode_wsd", "final");
		}

		$report_ids = $this->load_filter_reports($corpus_id);

        $annotationOwnerId = $annotation_mode == "final" ? null : $user_id;
		$content = $this->load_document_content($this->document, $selected_annotation_set, $annotation_mode, $annotationOwnerId);

		$this->page->set('annotation_sets', $annotation_sets);
		$this->page->set('selected_annotation_set', $selected_annotation_set);

		$this->page->set("wsd_word", $word);
        $this->page->set("wsd_word_id", $word_annotation_type_id);
        $this->page->set("wsd_edit", $annotation_id);
		$this->page->set("content_inline", $content);
		$this->page->set("words", $this->load_wsd_words($report_ids, $selected_annotation_set, $annotation_mode, $user_id));

		$sql_annotation = "SELECT * FROM reports_annotations WHERE id = ?";
		$ann = $this->page->getDb()->fetch($sql_annotation, array($annotation_id));
		$annotation_from = $ann['from'];

		list($next_word_not_report_id, $next_word_not_annotation_id) = $this->load_next_not_set(
			$word_annotation_type_id, $report_ids, $rid, $annotation_from, $selected_annotation_set, $annotation_mode, $user_id);
		$this->page->set("next_word_not_report_id", $next_word_not_report_id);
		$this->page->set("next_word_not_annotation_id", $next_word_not_annotation_id);

		list($prev_word_not_report_id, $prev_word_not_annotation_id) = $this->load_prev_not_set(
			$word_annotation_type_id, $report_ids, $rid, $annotation_from, $selected_annotation_set, $annotation_mode, $user_id);
		$this->page->set("prev_word_not_report_id", $prev_word_not_report_id);
		$this->page->set("prev_word_not_annotation_id", $prev_word_not_annotation_id);

		list($next_word_report_id, $next_word_annotation_id) = $this->load_next_word(
			$word_annotation_type_id, $report_ids, $rid, $annotation_from, $selected_annotation_set, $annotation_mode, $user_id);
		$this->page->set("next_word_report_id", $next_word_report_id);
		$this->page->set("next_word_annotation_id", $next_word_annotation_id);

		list($prev_word_report_id, $prev_word_annotation_id) = $this->load_prev_word(
			$word_annotation_type_id, $report_ids, $rid, $annotation_from, $selected_annotation_set, $annotation_mode, $user_id);
		$this->page->set("prev_word_report_id", $prev_word_report_id);
		$this->page->set("prev_word_annotation_id", $prev_word_annotation_id);


        if ( isset($_COOKIE['annotation_mode_wsd']) ){
            $annotation_mode = $_COOKIE['annotation_mode_wsd'];
            if($annotation_mode != "final")
                $annotation_mode = "agreement";
        }
        $this->page->set("annotation_mode", $annotation_mode);
	}

	/**
	 * Odczytuje z bazy listę słów dla WSD. Zwraca tablicę identyfikator=>opis_słowa
	 */
	function load_wsd_words($reportIds, $annotation_set_id, $stage, $user_id){
		$sql = "SELECT at. * , inner_query.report_id report_id, inner_query.id annotation_id
				FROM annotation_types at
				JOIN annotation_types_attributes ata ON ata.annotation_type_id = at.annotation_type_id
				LEFT JOIN (
					SELECT GROUP_CONCAT( an.report_id ) report_id, GROUP_CONCAT( an.id ) id, an.type_id
					FROM reports_annotations an
					WHERE an.report_id IN ('". implode("','",$reportIds) ."') " .
					" AND an.stage = ?".
					($stage !== "agreement" ? "" :  " AND an.user_id = ?") .
				" GROUP BY an.type_id
					ORDER BY an.report_id ASC , an.from ASC
				)inner_query ON inner_query.type_id = at.annotation_type_id
				WHERE at.group_id = ?
				AND ata.name =  'sense'
				ORDER BY at.name";

		$sql_param = array($stage);
        if ( $stage === "agreement"){
            $sql_param[] = $user_id;
        }
        $sql_param[] = $annotation_set_id;

        $rows =  $this->page->getDb()->fetch_rows($sql, $sql_param);

		$words = array();
		foreach ($rows as $r){
			$r['word'] = substr($r['name'], 4);

			if (!is_null($r['report_id'])){
				$r['report_id'] = explode(',', $r['report_id']);
                $r['report_id'] = $r['report_id'][0];
				$r['annotation_id'] = explode(',', $r['annotation_id']);
				$r['annotation_id']= $r['annotation_id'][0];
			}

			$words[$r['name']] = $r;						
		}
		return $words;
	}
	
	function load_document_content($report, $annotationSetId, $anStage='agreement', $anUserId=null){
		$anUserId = $anUserId !== null && !is_array($anUserId) ? [$anUserId] : $anUserId;
        $htmlStr = ReportContent::getHtmlStr($report);
        $annotations = DbAnnotation::getReportAnnotations($report['id'], $anUserId,
			array($annotationSetId), null, null, array($anStage), false);
        $htmlStr = ReportContent::insertAnnotations($htmlStr, $annotations);
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($report['id']));
		return Reformat::xmlToHtml($htmlStr->getContent());
	}

	/**
	 * Znajduje następne wystąpienie danego słowa w dokumencie.
	 */
	function load_next_word($word_wsd, $reportIds, $report_id, $annotation_from, $annotation_set_id, $stage, $user_id){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type_id=at.annotation_type_id)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type_id = an.type_id)" .
				" WHERE at.group_id = ?" .
				"  AND r.id IN ('". implode("','",$reportIds) ."') " .
				"  AND ata.name = 'sense'" .
				"  AND ( ( r.id > ? ) OR ( r.id = ? AND an.from > ?) )" .
				"  AND an.type_id = ?" .
				" AND an.stage = ?".
				($stage !== "agreement" ? "" :  " AND an.user_id = ?") .
				" ORDER BY r.id, an.from ASC";

        if ($stage == "agreement"){
            $row =  $this->page->getDb()->fetch($sql, array($annotation_set_id, $report_id, $report_id, $annotation_from, $word_wsd, $stage, $user_id));
        } else{
            $row = $this->page->getDb()->fetch($sql, array($annotation_set_id, $report_id, $report_id, $annotation_from, $word_wsd, $stage));
        }
		return is_array($row) ? array_values($row) : array(null, null);
	}
	
	function load_prev_word($word_wsd, $reportIds, $report_id, $annotation_from, $annotation_set_id, $stage, $user_id){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type_id=at.annotation_type_id)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type_id = an.type_id)" .
				" WHERE at.group_id = ?" .
				"  AND r.id IN ('". implode("','",$reportIds) ."') " .
				"  AND ata.name = 'sense'" .
				"  AND ( ( r.id < ? ) OR ( r.id = ? AND an.from < ?) )" .
				"  AND an.type_id = ?" .
				" AND an.stage = ?".
				($stage !== "agreement" ? "" :  " AND an.user_id = ?") .
				" ORDER BY r.id DESC, an.from DESC";

        if ($stage == "agreement"){
            $row =  $this->page->getDb()->fetch($sql, array($annotation_set_id, $report_id, $report_id, $annotation_from, $word_wsd, $stage, $user_id));
        } else{
            $row = $this->page->getDb()->fetch($sql, array($annotation_set_id, $report_id, $report_id, $annotation_from, $word_wsd, $stage));
        }
		return is_array($row) ? array_values($row) : array(null, null);
	}	
	
	function load_next_not_set($word_wsd, $reportIds, $report_id, $annotation_from, $annotation_set_id, $stage, $user_id){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type_id=at.annotation_type_id)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type_id = an.type_id)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = ?" .
				"  AND r.id IN ('". implode("','",$reportIds) ."') " .
				"  AND ata.name = 'sense'" .
				"  AND raa.value IS NULL" .
				"  AND ( ( r.id > ? ) OR ( r.id = ? AND an.from > ?) )" .
				"  AND an.type_id = ?" .
				" AND an.stage = ?".
				($stage !== "agreement" ? "" :  " AND an.user_id = ?") .
				" ORDER BY r.id, an.from ASC";

        if ($stage == "agreement"){
            $row =  $this->page->getDb()->fetch($sql, array($annotation_set_id,$report_id, $report_id, $annotation_from, $word_wsd, $stage, $user_id));
        } else {
            $row = $this->page->getDb()->fetch($sql, array($annotation_set_id,$report_id, $report_id, $annotation_from, $word_wsd, $stage));
        }
		return is_array($row) ? array_values($row) : array(null, null);
	}

	function load_prev_not_set($word_wsd, $reportIds, $report_id, $annotation_from, $annotation_set_id, $stage, $user_id){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type_id=at.annotation_type_id)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type_id = an.type_id)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = ?" .
				"  AND r.id IN ('". implode("','",$reportIds) ."') " .
				"  AND ata.name = 'sense'" .
				"  AND raa.value IS NULL" .
				"  AND ( ( r.id < ? ) OR ( r.id = ? AND an.from < ?) )" .
				"  AND an.type_id = ?" .
				" AND an.stage = ?".
				($stage !== "agreement" ? "" :  " AND an.user_id = ?") .
				" ORDER BY r.id DESC, an.from DESC";

        if ($stage == "agreement"){
            $row =  $this->page->getDb()->fetch($sql, array($annotation_set_id, $report_id, $report_id, $annotation_from, $word_wsd, $stage, $user_id));
        } else{
            $row = $this->page->getDb()->fetch($sql, array($annotation_set_id, $report_id, $report_id, $annotation_from, $word_wsd, $stage));
        }
		return is_array($row) ? array_values($row) : array(null, null);
	}
	
	/**
	 * Odczytuje identyfikator pierwszej jednostki do edycji.
	 * W pierwszej kolejności wybierane jest nieopisane słowo podanego typu.
	 * Jeżeli typ słowa nie jest określony, to pobierane jest pierwsze nieopisane słowo.
	 */
	function load_wsd_edit($report_id, $wsd_word, $annotation_id, $annotation_set_id){ // todo - check if user specific
		$sql = "SELECT an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = ?" .
				"  AND ata.name = 'sense'" .
				"  AND r.id = ?" .
				( $wsd_word ? " AND an.type_id = '" . mysql_real_escape_string($wsd_word) . "'" : "" ).
				" ORDER BY an.from ASC";
		return $this->page->getDb()->fetch_one($sql, array($annotation_set_id, $report_id));
	}
	
	/**
	 * Pobiera identyfikatory dokumentów odpowiadające ustawieniom filtrów na stronie z dokumentami.
	 */
	function load_filter_reports($corpus_id){
		// Wczytaj wszystkie flagi dla korpusu
		$flags_names = DbCorpus::getCorpusFlags($corpus_id);		
		// wczytaj parametry filtrowania raportów										
		$join = $_COOKIE["{$corpus_id}_".'sql_join'];
		$where_sql = $_COOKIE["{$corpus_id}_".'sql_where'];
		$group_sql = $_COOKIE["{$corpus_id}_".'sql_group'];
		$order = array_key_exists("{$corpus_id}_".'sql_order', $_COOKIE) ? $_COOKIE["{$corpus_id}_".'sql_order'] : "r.id";

		/// Flagi
		$flag_array = array();
		$flags_not_ready_map = array();
		foreach($flags_names as $key => $flag_name){
			$flag_name_str = str_replace(' ', '_', $flag_name['short']);
			$flag_name_str = 'flag_' . $flag_name_str;
			$flag_array[$key]['flag_name'] = $flag_name['short'];
			$flag_array[$key]['no_space_flag_name'] = $flag_name_str;
			$flag_array[$key]['value'] = $_COOKIE["{$corpus_id}_".$flag_name_str];
			$flags_not_ready_map[$flag_name['short']] = array(); 			 
		}
		
		foreach($flag_array as $key => $value){
			$flag_array[$key]['data'] = array_filter(explode(",", $flag_array[$key]['value']), "intval"); 
		}
		
		$flags_count = array(); // Ilość aktywnych flag 
		$flag_not_ready = array(); // Dla przypadku filtrowania po fladze niegotowy
		foreach($flag_array as $key => $value){
			if (count($flag_array[$key]['data'])){
				$flags_count[] = $key;
				if (in_array('-1', $flag_array[$key]['data'])){
					$flag_not_ready[] = $flag_array[$key];
				}							
			}	
								
		}
		
		$where_flags = array();
		if(count($flags_count)){ 
			$sql = "SELECT f.flag_id as id FROM flags f WHERE f.flag_id>0 ";  	
			$rows_flags = $this->page->getDb()->fetch_rows($sql);
			foreach($rows_flags as $key => $row_flag){
				$rows_flags[$key] = $row_flag['id'];
			}				
			foreach($flags_count as $value){
				$where_data = array();
				if(in_array('-1', $flag_array[$value]['data'])){
					if(count($flag_array[$value]['data']) > 1){
						foreach($flag_array[$value]['data'] as $data)
							if($data != '-1')
								$where_data[] = $data;
						$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . $this->where_or("f.flag_id", $where_data) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
					}
					else{
						$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . $this->where_or("f.flag_id", array('-1')) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
					}
				}	 
				else{
					$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . $this->where_or("f.flag_id", $flag_array[$value]['data']) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
				}
			}
			$group['report_id'] = "r.id";
		}
		
		$sql = 	"SELECT " .
				" r.id " .
				" FROM reports r" .
				" LEFT JOIN reports_types rt ON ( r.type = rt.id )" .
				" LEFT JOIN reports_statuses rs ON ( r.status = rs.id )" .
				" LEFT JOIN corpus_subcorpora cs ON (r.subcorpus_id=cs.subcorpus_id)" .
				" LEFT JOIN users u USING (user_id)" .
				$join .
				" WHERE r.corpora = {$corpus_id} ".
				$where_sql .
				$group_sql .
				" ORDER BY $order" ;
		$rows = $this->page->getDb()->fetch_rows($sql);

		$reportIds = array();
		foreach ($rows as $row){
			array_push($reportIds, $row['id']);
		}
		
		// Jeżeli są zaznaczone flagi to obcina listę wynikow
		$reports_ids_flag_not_ready = array();
		if(count($flags_count)){  
			$sql = "SELECT r.id AS id, cf.short as name ".
					"FROM reports r " .
  					"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  					"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
    				"WHERE r.id IN  ('". implode("','",$reportIds) ."') ";
			$rows_flags_not_ready = $this->page->getDb()->fetch_rows($sql);
  			
			foreach ($rows_flags_not_ready as $row_flags_not_ready){
				$flags_not_ready_map[$row_flags_not_ready['name']][] = $row_flags_not_ready['id'];
			}
			foreach($flag_not_ready as $flag_not){
				$reports_ids_flag_not_ready[$flag_not['flag_name']] = array();
				foreach($reportIds as $repId){
					if(!in_array($repId,$flags_not_ready_map[$flag_not['flag_name']]))
						if(!in_array($repId,$reports_ids_flag_not_ready[$flag_not['flag_name']]))
							$reports_ids_flag_not_ready[$flag_not['flag_name']][] = $repId;
				}
			}
			foreach($flags_count as $flags_where){
				if(isset($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']])){
					foreach($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']] as $key => $flag_not_ready_rep){
						if(!in_array($flag_not_ready_rep,$reportIds))
							unset($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']][$key]);
					}
				}
				$sql = "SELECT r.id AS id  ".
	  					"FROM reports r " .
  						"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  						"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  						"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
	  					"WHERE r.id IN  ('". implode("','",$reportIds) ."') " .
	  					$where_flags[$flag_array[$flags_where]['no_space_flag_name']] .
  						" GROUP BY r.id " .
  						" ORDER BY r.id ASC " ;
				$rows_flags = $this->page->getDb()->fetch_rows($sql);
				$reportIds = array();
				foreach ($rows_flags as $row){
					array_push($reportIds, $row['id']);				
				}
				if(isset($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']])){
					foreach($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']] as $flag_not_ready_rep){
						if(!in_array($flag_not_ready_rep,$reportIds))
							array_push($reportIds, $flag_not_ready_rep);
					}
				}
			}			
		}

		return $reportIds;
	}
	
	function where_or($column, $values){
		$ors = array();
		foreach ($values as $value)
			$ors[] = "$column = '$value'";
		if (count($ors)>0)	
			return "(" . implode(" OR ", $ors) . ")";
		else
			return "";
	}
}

?>
