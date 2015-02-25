<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAnnotatorWSD extends CPerspective {
	
	function execute()
	{
		global $corpus;
		$corpus_id = $corpus['id'];
		
		$word = $_GET['wsd_word'];
		$rid  = intval($_GET['id']);
		$annotation_id = intval($_GET['aid']);
		
		$report_ids = $this->load_filter_reports($corpus_id);
		
		$content = $this->load_document_content();
		
		$this->page->set("wsd_word", $word);
		$this->page->set("wsd_edit", $annotation_id);
		$this->page->set("content_inline", $content);
		$this->page->set("words", $this->load_wsd_words($report_ids));

		$sql_annotation = "SELECT * FROM reports_annotations WHERE id = ?";
		$ann = db_fetch($sql_annotation, array($annotation_id));
		$annotation_from = $ann['from'];

		list($next_word_not_report_id, $next_word_not_annotation_id) = $this->load_next_not_set($word, $report_ids, $rid, $annotation_from); 		
		$this->page->set("next_word_not_report_id", $next_word_not_report_id);
		$this->page->set("next_word_not_annotation_id", $next_word_not_annotation_id);

		list($prev_word_not_report_id, $prev_word_not_annotation_id) = $this->load_prev_not_set($word, $report_ids, $rid, $annotation_from); 		
		$this->page->set("prev_word_not_report_id", $prev_word_not_report_id);
		$this->page->set("prev_word_not_annotation_id", $prev_word_not_annotation_id);
		
		list($next_word_report_id, $next_word_annotation_id) = $this->load_next_word($word, $report_ids, $rid, $annotation_from); 		
		$this->page->set("next_word_report_id", $next_word_report_id);
		$this->page->set("next_word_annotation_id", $next_word_annotation_id);
		
		list($prev_word_report_id, $prev_word_annotation_id) = $this->load_prev_word($word, $report_ids, $rid, $annotation_from); 		
		$this->page->set("prev_word_report_id", $prev_word_report_id);
		$this->page->set("prev_word_annotation_id", $prev_word_annotation_id);
	}

	/**
	 * Odczytuje z bazy listę słów dla WSD. Zwraca tablicę identyfikator=>opis_słowa
	 */
	function load_wsd_words($reportIds){
		$sql = "SELECT * FROM annotation_types WHERE group_id = 2 ORDER BY name";
		
		$sql_first_ann = "SELECT an.report_id, an.id" .
				" FROM reports_annotations an " .
				" WHERE an.type = ? " .
				" AND an.report_id IN ('". implode("','",$reportIds) ."') " .				
				" ORDER BY an.report_id ASC, an.from ASC";
				
		$rows = db_fetch_rows($sql);
		$words = array();
		foreach ($rows as $r){
			$r['word'] = substr($r['name'], 4);
			
			// Znajdź pierwsze wystąpienie anotacji
			$row = db_fetch($sql_first_ann, array($r['name']));
			list($first_report_id, $first_annotation_id) = is_array($row) ? array_values($row) : array(null, null);
			$r['report_id'] = $first_report_id;
			$r['annotation_id'] =  $first_annotation_id;
						
			$words[$r['name']] = $r;						
		}
		
		return $words;
	}
	
	/**
	 * 
	 */
	function load_document_content(){
		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, group_id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types t ON (an.type=t.name)" .
				" WHERE report_id = {$this->document['id']}" .
				" AND t.group_id = 2" .
				" ORDER BY `from` ASC, `level` DESC";
		$anns = db_fetch_rows($sql);

		try{
			$htmlStr = new HtmlStr($this->document['content']);
			foreach ($anns as $ann){
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", $ann['id'], $ann['type'], $ann['group_id']), $ann['to']+1, "</an>");
				//$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");
			}
		}catch (Exception $ex){
			fb($ex);//InforexWeb::custom_exception_handler($ex);
		}
		
		return Reformat::xmlToHtml($htmlStr->getContent());
	}

	/**
	 * Znajduje następne wystąpienie danego słowa w dokumencie.
	 */
	function load_next_word($word_wsd, $reportIds, $report_id, $annotation_from){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" WHERE at.group_id = 2" .
				"  AND r.id IN ('". implode("','",$reportIds) ."') " .
				"  AND ata.name = 'sense'" .
				"  AND ( ( r.id > ? ) OR ( r.id = ? AND an.from > ?) )" .
				"  AND an.type = ?" .
				" ORDER BY r.id, an.from ASC";
		$row = db_fetch($sql, array($report_id, $report_id, $annotation_from, $word_wsd));
		return is_array($row) ? array_values($row) : array(null, null);
	}
	
	function load_prev_word($word_wsd, $reportIds, $report_id, $annotation_from){		
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" WHERE at.group_id = 2" .
				"  AND r.id IN ('". implode("','",$reportIds) ."') " .
				"  AND ata.name = 'sense'" .
				"  AND ( ( r.id < ? ) OR ( r.id = ? AND an.from < ?) )" .
				"  AND an.type = ?" .
				" ORDER BY r.id DESC, an.from DESC";
		$row = db_fetch($sql, array($report_id, $report_id, $annotation_from, $word_wsd));
		return is_array($row) ? array_values($row) : array(null, null);
	}	
	
	function load_next_not_set($word_wsd, $reportIds, $report_id, $annotation_from){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = 2" .
				"  AND r.id IN ('". implode("','",$reportIds) ."') " .
				"  AND ata.name = 'sense'" .
				"  AND raa.value IS NULL" .
				"  AND ( ( r.id > ? ) OR ( r.id = ? AND an.from > ?) )" .
				"  AND an.type = ?" .
				" ORDER BY r.id, an.from ASC";
		$row = db_fetch($sql, array($report_id, $report_id, $annotation_from, $word_wsd));
		return is_array($row) ? array_values($row) : array(null, null);
	}

	function load_prev_not_set($word_wsd, $reportIds, $report_id, $annotation_from){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = 2" .
				"  AND r.id IN ('". implode("','",$reportIds) ."') " .
				"  AND ata.name = 'sense'" .
				"  AND raa.value IS NULL" .
				"  AND ( ( r.id < ? ) OR ( r.id = ? AND an.from < ?) )" .
				"  AND an.type = ?" .
				" ORDER BY r.id DESC, an.from DESC";
		$row = db_fetch($sql, array($report_id, $report_id, $annotation_from, $word_wsd));
		return is_array($row) ? array_values($row) : array(null, null);
	}
	
	/**
	 * Odczytuje identyfikator pierwszej jednostki do edycji.
	 * W pierwszej kolejności wybierane jest nieopisane słowo podanego typu.
	 * Jeżeli typ słowa nie jest określony, to pobierane jest pierwsze nieopisane słowo.
	 */
	function load_wsd_edit($report_id, $wsd_word, $annotation_id){
		$sql = "SELECT an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = 2" .
				"  AND ata.name = 'sense'" .
				"  AND r.id = ?" .
				( $wsd_word ? " AND an.type = '" . mysql_real_escape_string($wsd_word) . "'" : "" ).
				" ORDER BY an.from ASC";
		return db_fetch_one($sql, array($report_id));		
	}
	
	/**
	 * Pobiera identyfikatory dokumentów odpowiadające ustawieniom filtrów na stronie z dokumentami.
	 */
	function load_filter_reports($corpus_id){
		global $mdb2, $db;
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
			$rows_flags = $db->fetch_rows($sql);
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
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);

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
			$rows_flags_not_ready = $db->fetch_rows($sql);
  			
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
				$rows_flags = $db->fetch_rows($sql);
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
