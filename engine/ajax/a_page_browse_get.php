<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_page_browse_get extends CPage {
	
	var $isSecure = false;
	function execute(){
		global $mdb2, $corpus, $db;

		$sortName		= $_POST['sortname']; 
		$sortOrder		= $_POST['sortorder'];
		$pageElements	= $_POST['rp'];
		$page			= $_POST['page'];	
		$cid			= $_POST['corpus'];
		$prevReport		= intval($_POST['r']);

		if($sortName == "found_base_form") $sortName = "id";


		$limitStart = ($page - 1) * $pageElements;
		$limitCount = $pageElements;

		// Wczytaj wszystkie flagi dla korpusu
		$flags_names = DbCorpus::getCorpusFlags($cid);
	
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$reset = array_key_exists('reset', $_GET) ? intval($_GET['reset']) : false;
		// wgawel: Stronicowanie teraz po stronie JS
        //$p = 0;
		//$prevReport = intval($_GET['r']);	
		$status	= array_key_exists('status', $_GET) ? $_GET['status'] : ($reset ? "" : $_COOKIE["{$cid}_".'status']);
		$type 	= array_key_exists('type', $_GET) ? $_GET['type'] : ($reset ? "" : $_COOKIE["{$cid}_".'type']);
		$year 	= array_key_exists('year', $_GET) ? $_GET['year'] : ($reset ? "" : $_COOKIE["{$cid}_".'year']);
		$month 	= array_key_exists('month', $_GET) ? $_GET['month'] : ($reset ? "" : $_COOKIE["{$cid}_".'month']);
		$search	= array_key_exists('search', $_GET) ? $_GET['search'] : ($reset ? "" : $_COOKIE["{$cid}_".'search']);
		$search_field= array_key_exists('search_field', $_GET) ? $_GET['search_field'] : ($reset ? "" : explode("|", $_COOKIE["{$cid}_".'search_field']));
		$annotation	= array_key_exists('annotation', $_GET) ? $_GET['annotation'] : ($reset ? "" : $_COOKIE["{$cid}_".'annotation']);
		
		$annotation_value = array_key_exists('annotation_value', $_GET) ? $_GET['annotation_value'] : ($reset ? "" : $_COOKIE["{$cid}_".'annotation_value']);
		$annotation_type = $annotation_value ? array_key_exists('annotation_type', $_GET) ? $_GET['annotation_type'] : ($reset ? "" : $_COOKIE["{$cid}_".'annotation_type']) : "";
		
		$subcorpus	= array_key_exists('subcorpus', $_GET) ? $_GET['subcorpus'] : ($reset ? "" : $_COOKIE["{$cid}_".'subcorpus']);
		$flag_array = array();
		$flags_not_ready_map = array();
		foreach($flags_names as $key => $flag_name){
			$flag_name_str = str_replace(' ', '_', $flag_name['short']);
			$flag_name_str = 'flag_' . $flag_name_str;
			$flag_array[$key]['flag_name'] = $flag_name['short'];
			$flag_array[$key]['no_space_flag_name'] = $flag_name_str;
			$flag_array[$key]['value'] = array_key_exists($flag_name_str, $_GET) ? $_GET[$flag_name_str] : ($reset ? "" : $_COOKIE["{$cid}_".$flag_name_str]);
			$flags_not_ready_map[$flag_name['short']] = array(); 			 
		}
		$filter_order = array_key_exists('filter_order', $_GET) ? $_GET['filter_order'] : ($reset ? "" : $_COOKIE["{$cid}_".'filter_order']);
		$base	= array_key_exists('base', $_GET) ? $_GET['base'] : ($reset ? "" : $_COOKIE["{$cid}_".'base']);
		$results_limit = (int) (array_key_exists('results_limit', $_GET) ? $_GET['results_limit'] : ($reset ? 0 : (isset($_COOKIE["{$cid}_".'results_limit']) ? $_COOKIE["{$cid}_".'results_limit'] : 5)));
		$random_order	= array_key_exists('random_order', $_GET) ? $_GET['random_order'] : (($reset || array_key_exists('base', $_GET) || array_key_exists('results_limit', $_GET) || array_key_exists('search', $_GET)) ? "" : (isset($_COOKIE["{$cid}_".'random_order']) ? $_COOKIE["{$cid}_".'random_order'] : ""));
		$base_show_found_sentences = array_key_exists('base_show_found_sentences', $_GET) ? $_GET['base_show_found_sentences'] : (($reset || array_key_exists('base', $_GET)) ? "" : (isset($_COOKIE["{$cid}_".'base_show_found_sentences']) ? $_COOKIE["{$cid}_".'base_show_found_sentences'] : ""));
				
		$search = stripslashes($search);
		$base = stripcslashes($base);
				
		$statuses = array_filter(explode(",", $status), "intval");
		$types = array_filter(explode(",", $type), "intval");
		$years = array_filter(explode(",", $year), "intval");
		$months = array_filter(explode(",", $month), "intval");
		$subcorpuses = array_filter(explode(",", $subcorpus), "intval");
		foreach($flag_array as $key => $value){
			$flag_array[$key]['data'] = array_filter(explode(",", $flag_array[$key]['value']), "intval"); 
		}
		$search = strval($search);
		$annotations = array_diff(explode(",", $annotation), array(""));
		$search_field = is_array($search_field) ? $search_field : array('title');
		$filter_order = explode(",", $filter_order);		
		$filter_order = is_array($filter_order) ? $filter_order : array();

		if (defined(IS_RELEASE) && $cid==2){
			$years = array(2004);
			$statuses = array(2);
			$months = array();
		}

		$limit = $limitCount;
		$from = $limitStart;
	
		/*** 
		 * Przygotuj warunki where dla zapytania SQL
		 ******************************************************************************/
				
		$where = array();
		$join = "";
		$select = "";
		$columns = array("lp"=>"No.", 
							"subcorpus_id"=>"Subcorpus",
							"id"=>"Id", 
							"title"=>"Title", 
							"status_name"=>"Status"); // lista kolumna do wyświetlenia na stronie
		
		/// Fraza
		if (strval($search)){
			$where_fraza = array();
			if (in_array('title', $search_field))
				$where_fraza[] = "r.title LIKE '%$search%'";
			if (in_array('content', $search_field))
				$where_fraza[] = "r.content LIKE '%$search%'";
			if (count($where_fraza))
				$where['text'] = ' (' . implode(" OR ", $where_fraza) . ') ';
		}
		
		if ( $base ){
                        $select .= " GROUP_CONCAT(CONCAT(tokens.from,'-',tokens.to) separator ',') AS base_tokens_pos, ";
			$join = " JOIN tokens AS tokens ON (r.id=tokens.report_id) JOIN tokens_tags as tt USING(token_id) ";
                        $join .= " LEFT JOIN bases AS b ON b.id=tt.base_id ";
			$where['base'] = " ( b.text = '". mysql_real_escape_string($base) ."' COLLATE utf8_bin AND tt.disamb = 1) "; 
			$group['report_id'] = "r.id";
		}

		if (count($years)>0)	$where['year'] = where_or("YEAR(r.date)", $years);			
		if (count($months)>0)	$where['month'] = where_or("MONTH(r.date)", $months);
		if (count($types)>0)	$where['type'] = where_or("r.type", $types);
		if (count($statuses)>0)	$where['status'] = where_or("r.status", $statuses);
		if (count($subcorpuses)>0)	$where['subcorpus'] = where_or("r.subcorpus_id", $subcorpuses);
				
		/// Anotacje
		if (in_array("no_annotation", $annotations)){
			if( count($annotations) > 1 ){
				$where['annotation'] = "( a.id IS NULL OR " . where_or("a.type", array_diff($annotations, array("no_annotation"))) ." ) ";
				$group['report_id'] = "r.id";				
			}
			else{
				$where['annotation'] = "a.id IS NULL";
			}	
			$join .= " LEFT JOIN reports_annotations a ON (r.id = a.report_id)";		
		}elseif (is_array($annotations) && count($annotations)>0 || $annotation_type != "" && $annotation_value != ""){
			$join .= " INNER JOIN reports_annotations an ON ( an.report_id = r.id )";
			$group['report_id'] = "r.id";
			
			if(is_array($annotations) && count($annotations)>0){
                                $where['annotation'] = where_or("an.type", $annotations);			
                                $join .= " LEFT JOIN annotation_types at ON an.type_id=at.annotation_type_id ";
                        }
		
			if($annotation_type != "" && $annotation_value != ""){
				$where['annotation_value'] = 'an.type = "'.mysql_real_escape_string($annotation_type).'" AND an.text = "'.mysql_real_escape_string($annotation_value).'" ';
			}
		}
		
		
		
		/// Flagi
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
						$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . where_or("f.flag_id", $where_data) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
					}
					else{
						$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . where_or("f.flag_id", array('-1')) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
					}
				}	 
				else{
					$where_flags[$flag_array[$value]['no_space_flag_name']] = ' AND ' . where_or("f.flag_id", $flag_array[$value]['data']) . ' AND cf.short=\''. $flag_array[$value]['flag_name'] .'\' ';
				}
			}
			$group['report_id'] = "r.id";
		}
		
		$columns["tokenization"] = "Tokenization";
		
		/// Wczytaj dodatkowe kolumny zależne od korpusu
		if ( $cid == 3 ){
			$join .= " LEFT JOIN reports_ext_3 ext ON (r.id = ext.id)";
			$select .= "ext.*, YEAR(r.date) as year, ";
			$columns["deceased_age"] = "Wiek";
			$columns["deceased_gender"] = "Płeć";
			$columns["deceased_maritial"] = "Status cywilny";
			$columns["source"] = "Sposób zapisu";
			$columns["year"] = "Rok";
			$columns["suicide_place"] = "Miejsce samobójstwa";
			
			$order = "r.title ASC";
		}else{
			$columns["bootstrapping"] = "PN to verify";
			
			$select .= " (SELECT COUNT(*) FROM reports_annotations WHERE report_id = r.id AND stage='new' AND source='bootstrapping') AS bootstrapping, ";
		}
		
		/* Format SQL statement elements */
		$group_sql = (count($group) == 0 ? "" : " GROUP BY " . implode(", ", array_values($group)) );
		$where_sql = ((count($where)>0) ? "AND " . implode(" AND ", array_values($where) ) : "");
		
		if($sortName == "id") $sortName = "r.id";
		/// Kolejność
		if ($random_order) {
		    $order = "RAND()";
		} 
		else{
			if(strpos($sortName, "flag") === 0){
				$join .= " LEFT JOIN (SELECT flag_id, report_id FROM reports_flags LEFT JOIN flags f USING(flag_id) WHERE corpora_flag_id = ".substr($sortName, 4).") AS fgo ON(fgo.report_id=r.id) ";
				$order = "fgo.flag_id ".$sortOrder;
			}else{
				$order = $sortName.' '.$sortOrder;
			}
		}

		if ($page == -1  && $prevReport){
			$sql = 	"SELECT count(r.id) as cnt" .
					" FROM reports r" .
					" LEFT JOIN reports_types rt ON ( r.type = rt.id )" .
					" LEFT JOIN reports_statuses rs ON ( r.status = rs.id )" .
					" LEFT JOIN users u USING (user_id)" .
					$join .
					" WHERE r.corpora = $cid" . 
					" AND r.id<$prevReport ".
					$where_sql .
					$group_sql .
					" ORDER BY $order";	
			if($group_sql != ""){
				$sql = "SELECT COUNT(*) FROM (".$sql.") AS a";
			}

			$prevCount = intval(db_fetch_one($sql));
			$page = (int)($prevCount/$limit);
			$from = $limit * $page;
			$page++;
		}


		$sql = 	"SELECT " .
				$select .
				"	r.title, " .
				"	r.status, " .
				"	r.id, " .
				"	r.tokenization," .
				" 	rt.name AS type_name, " .
				"	rs.status AS status_name, " .
				"	u.screename, " .
				"   IFNULL(cs.name, '[unassigned]') AS subcorpus_id, " .
				"   r.content " .
				" FROM reports r" .
				" LEFT JOIN reports_types rt ON ( r.type = rt.id )" .
				" LEFT JOIN reports_statuses rs ON ( r.status = rs.id )" .
				" LEFT JOIN corpus_subcorpora cs ON (r.subcorpus_id=cs.subcorpus_id)" .
				" LEFT JOIN users u USING (user_id)" .
				$join .
				" WHERE r.corpora = {$cid} ".
				$where_sql .
				$group_sql .
				" ORDER BY $order" .
				(count($flags_count) ? "" : " LIMIT {$from},".number_format($limit, 0, '.', '') );

		if (PEAR::isError($r = $mdb2->query($sql)))
			throw new Exception("{$r->getUserInfo()}");
		$rows = $r->fetchAll(MDB2_FETCHMODE_ASSOC);

		$reportIds = array();
		foreach ($rows as $row){
			array_push($reportIds, $row['id']);
		}
        
		// Jeżeli wyszukiwanie po formie bazowej (base) to wyciągnij zdania ją zawierające
        if ($base) {
            $base_sentences = array();
            $base_found_sentences = 0;
            foreach($rows AS $row) {
                $base_sentences[$row['id']]['founds_number'] = count(explode(',',$row['base_tokens_pos']));
                $base_found_sentences += $base_sentences[$row['id']]['founds_number'];
            }
            
            $n = 0;
            reset($rows);
            while ($n < $results_limit && list(, $row) = each($rows)) {
                if ($base_show_found_sentences) {
                    $base_sentences[$row['id']]['founds'] = ReportSearcher::get_sentences_with_base_in_content_by_positions($row['content'],$row['base_tokens_pos']);  
                } else {
                    $base_sentences[$row['id']]['founds'] = array();
                }
                $n++;             
            }
            $this->set('base_sentences', $base_sentences);
            $this->set('base_found_sentences', $base_found_sentences);
            $columns['found_base_form'] = 'Base forms';
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
			
		
			foreach ($rows as $key => $row){
				if(!in_array($row['id'], $reportIds)){
					unset($rows[$key]);
				}
			}
		
			// Obcinanie do limitu
			$i = 0;
			$num = 0;
			foreach ($rows as $key => $row){
				unset($rows[$key]);
				if($i >= $from && $i < $from+$limit){
					$rows[$num] = $row;
					$num++;
				}
				$i++;
			}
		}
		$sql = "SELECT * FROM corpora_flags WHERE corpora_id={$cid} ORDER BY sort";
		$corporaFlags = db_fetch_rows($sql);
		foreach ($corporaFlags as $corporaFlag){
			$columns["flag".$corporaFlag['corpora_flag_id']]=$corporaFlag;
		}
		
		$sql = "SELECT reports_flags.report_id, reports_flags.corpora_flag_id, reports_flags.flag_id, flags.name " .
				"FROM reports_flags " .
				"LEFT JOIN flags ON " .
				" (reports_flags.flag_id=flags.flag_id) " .
				(count($reportIds)>0 ?
				"WHERE reports_flags.report_id IN ('".implode("','",$reportIds)."') " : "") 
				."ORDER BY name"
				;
		$reportFlags = $db->fetch_rows($sql);
		
		$sql = "SELECT * FROM corpora_flags WHERE corpora_id={$cid} ORDER BY sort";
		$reportFlagsMap = array();
		foreach ($reportFlags as $reportFlag){
			if ($reportFlagsMap[$reportFlag['report_id']]){
				$reportFlagsMap[$reportFlag['report_id']][$reportFlag['corpora_flag_id']]['name']=$reportFlag['short'];
				$reportFlagsMap[$reportFlag['report_id']][$reportFlag['corpora_flag_id']]['flag_id']=$reportFlag['flag_id'];
			}
			else {
				$reportFlagsMap[$reportFlag['report_id']]=array();
				$reportFlagsMap[$reportFlag['report_id']][$reportFlag['corpora_flag_id']]['name']=$reportFlag['short'];
				$reportFlagsMap[$reportFlag['report_id']][$reportFlag['corpora_flag_id']]['flag_id']=$reportFlag['flag_id'];
			}
		}		
		$count_rows = count($rows);
		for ($i=0; $i<$count_rows; $i++){
			foreach ($corporaFlags as $corporaFlag){
				$rows[$i]["flag".$corporaFlag['corpora_flag_id']]['name']="NIE GOTOWY";			
				$rows[$i]["flag".$corporaFlag['corpora_flag_id']]['flag_id']=-1;			
				if ($reportFlagsMap[$rows[$i]['id']] && $reportFlagsMap[$rows[$i]['id']][$corporaFlag['corpora_flag_id']]){
					$rows[$i]["flag".$corporaFlag['corpora_flag_id']]['name']=$reportFlagsMap[$rows[$i]['id']][$corporaFlag['corpora_flag_id']]['name'];								
					$rows[$i]["flag".$corporaFlag['corpora_flag_id']]['flag_id']=$reportFlagsMap[$rows[$i]['id']][$corporaFlag['corpora_flag_id']]['flag_id'];								
				};
			}
		}
		$sql = "SELECT * FROM corpora_flags WHERE corpora_id={$cid} ORDER BY sort";
		array_walk($rows, "array_walk_highlight", $search);
		// wszystkie wyniki
		
		if(count($flags_count)){  
			$sql = "SELECT r.id AS id FROM reports r $join WHERE r.corpora={$cid} $where_sql";
			$rows_count = $db->fetch_rows($sql);
			$reportIds = array();
			foreach ($rows_count as $row){
				array_push($reportIds, $row['id']);				
			}
			foreach($flags_count as $flags_where){
				$sql = "SELECT r.id AS id  ".
	  					"FROM reports r " .
  						"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  						"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  						"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
	  					"WHERE r.id IN  ('". implode("','",$reportIds) ."') " .
						$where_flags[$flag_array[$flags_where]['no_space_flag_name']] .
						" GROUP BY r.id " .
						" ORDER BY r.id ASC ";

				$rows_count = $db->fetch_rows($sql);
				$reportIds = array();
				foreach ($rows_count as $row){
					array_push($reportIds, $row['id']);				
				}
				if(isset($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']])){
					foreach($reports_ids_flag_not_ready[$flag_array[$flags_where]['flag_name']] as $flag_not_ready_rep){
						if(!in_array($flag_not_ready_rep,$reportIds))
							array_push($reportIds, $flag_not_ready_rep);
					}
				}
			}
			$rows_all = count($reportIds);
		}
		else{
			$sql = "SELECT COUNT(DISTINCT r.id) FROM reports r $join WHERE r.corpora={$cid} $where_sql";
			if (PEAR::isError($r = $mdb2->query($sql))) 
				die("<pre>{$r->getUserInfo()}</pre>");
			$rows_all = $r->fetchOne();
		}

		// Usuń atrybuty z listy kolejności, dla których nie podano warunku.
		$where_keys = count($where) >0 ? array_keys($where) : array();
		if(count($flags_count)) // Jeżeli są zaznaczone flagi (więcej niż jedna) 
			foreach($flags_count as $flags_where)
				$where_keys[] = $flag_array[$flags_where]['no_space_flag_name'];
		$filter_order = array_intersect($filter_order, $where_keys);
		// Dodaj brakujące atrybuty do listy kolejności
		$filter_order = array_merge($filter_order, array_diff($where_keys, $filter_order) );
		// Dodaj filtr kolejności i limitu wyników, jeśli określony
        if ($limit < $max_results_limit || $random_order) {
            array_push($filter_order, 'order_and_results_limit');
        }

        // ???
        $total = $rows_all;
        
        $result = array();
        foreach($rows as $row){
        	$row['content'] = null;
        	foreach($row as $key => $value){
        		if(strpos($key, "flag") === 0){
        			$row[$key] = getFlagMarkup($value['flag_id'], $value['name']);
        		}
        		else if($key == "title"){
        			$row['title'] = getDocumentAnchor($corpus_id, $row['id'], $row['title']);
        		}

        		if($base){
        			$row['found_base_form'] = getBaseAnchor($base_sentences[$row['id']]['founds_number'], $row['id'], $base);	
        		}
        	}
        	$result[] = array('id' => $row['id'], 'cell' => $row);
        }

        // UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
		echo json_encode(array('page' => $page, 'total' => $total, 'rows' => $result, 'post' => $_POST));
		die;
	}
}

function array_map_replace_spaces(&$value){
	$value['name'] = str_replace(" ", "&nbsp;", $value['name']);
} 

function array_map_replace_null_id(&$value){
	$value['id'] = ($value['id'] ? $value['id'] : "no_annotation" );
}

function array_walk_highlight(&$value, $key, $phrase){
	$value['title'] = str_replace($phrase, "<em>$phrase</em>", $value['title']);
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

function getBaseAnchor($found_count, $report_id, $base){
	$anchor = '<a href="#" class="ajax_link_get_sentences"  data-report_id="'.$report_id.'" data-search_base="'.$base.'">Get sentences (found '.$found_count.' occurrences).</a>';
	return $anchor;
}

function getDocumentAnchor($corpus_id, $document_id, $title = "<i>no title</i>"){
	$title = trim($title)==="" ? "<i>no title</i>" : trim($title); 
	$anchor = '<a href="index.php?page=report&amp;corpus='.$corpus_id.'&amp;id='.$document_id.'" class="tip" title="'.$title.'">'.$title.'</a>';
	return $anchor;
}

function getFlagMarkup($flag_id, $flag_name){
	$markup = '<img src="gfx/flag_'.$flag_id.'.png" title="'.$flag_name.'" style="vertical-align: baseline"/>';
	return $markup;
}
?>
