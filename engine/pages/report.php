<?php

class Page_report extends CPage{
	
	var $isSecure = false;
	
	function checkPermission(){
		global $corpus;
		return true;
	}
	
	function execute(){
		global $mdb2, $auth, $corpus, $user;
						
		$cid = $corpus['id'];
		
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$id 	= intval($_GET['id']);
		$p 		= intval($_GET['p']);
		$edit 	= intval($_GET['edit']);
		$subpage = array_key_exists('subpage', $_GET) ? $_GET['subpage'] : $_COOKIE["{$cid}_".'subpage'];
		$view = array_key_exists('view', $_GET) ? $_GET['view'] : $_COOKIE["{$cid}_".'view'];
		$where = trim(stripslashes($_COOKIE["{$cid}_".'sql_where']));
		$where_prev = trim(stripslashes($_COOKIE["{$cid}_".'sql_where_prev']));
		$where_next = trim(stripslashes($_COOKIE["{$cid}_".'sql_where_next']));
		$join = stripslashes($_COOKIE["{$cid}_".'sql_join']);
		$group = stripcslashes($_COOKIE["{$cid}_".'sql_group']);
		$order = stripcslashes($_COOKIE["{$cid}_".'sql_order']);
		
		// Domyślne wartości dla wymaganych
		$order = strlen($order)==0 ? "r.id ASC" : $order; 
				
		// Walidacja parametrów
		// ******************************************************************************
		// List dostępnych podstron dla danego korpusu
		$subpages = DBReportPerspective::get_corpus_perspectives($cid, $user);
		
		$find = false;
		foreach ($subpages as $s)
			$find = $find || $s->id == $subpage;
		$subpage = $find ? $subpage : 'preview';

		if (!$id)
			header("Location: index.php?page=browse");
		
		// Zapisz parametry w sesjii
		// ******************************************************************************		
		setcookie("{$cid}_".'subpage', $subpage);
		setcookie('view', $view);
		
		if ($corpus['ext']){
			$sql = "SELECT r.*, e.*, r.id, rs.status AS status_name, rt.name AS type_name" .
					" FROM reports r" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" LEFT JOIN reports_ext_{$corpus['id']} e ON (r.id=e.id) " .
					" WHERE r.id={$id}";
		}else{
			$sql = "SELECT r.*, rs.status AS status_name, rt.name AS type_name" .
					" FROM reports r" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" WHERE r.id={$id}";
		}
		$row = db_fetch($sql); 
		
		// Ustal warunki wyboru następnego/poprzedniego
		$fields = explode(" ", $order);
		$column = str_replace("r.", "", $fields[0]);
		$where_next = "r.$column < '{$row[$column]}'";
		$where_prev = "r.$column > '{$row[$column]}'";
		
		$year = date("Y", strtotime($row['date']));
		$month = date("n", strtotime($row['date']));
				
		// Lista adnoatcji
		$annotations = db_fetch_rows("SELECT a.*, u.screename" .
				" FROM reports_annotations a" .
				" JOIN annotation_types t " .
					" ON (a.type=t.name)" .
				" LEFT JOIN users u USING (user_id)" .
				" WHERE a.report_id=$id");		
		$allCount = db_fetch_one("SELECT count(ran.id) cnt FROM reports_annotations ran " .
								"JOIN annotation_types aty " .
								"ON (ran.report_id={$id} " .
								"AND ran.type=aty.name " .
								"AND aty.group_id IN " .
									"(SELECT annotation_set_id " .
									"FROM annotation_sets_corpora  " .
									"WHERE corpus_id=$cid))");
		setcookie('allcount',$allCount);

		//pobierz relacje
		/*$sql = 	"SELECT  relations.source_id, " .
						"relations.target_id, " .
						"relation_types.name, " .
						"rasrc.text source_text, " .
						"rasrc.type source_type, " .
						"radst.text target_text, " .
						"radst.type target_type " .
						"FROM relations " .
						"JOIN relation_types " .
							"ON (relations.relation_type_id=relation_types.id " .
							"AND relations.source_id IN " .
								"(SELECT id FROM reports_annotations " .
								"WHERE report_id={$id})) " .
						"JOIN reports_annotations rasrc " .
							"ON (relations.source_id=rasrc.id) " .
						"JOIN reports_annotations radst " .
							"ON (relations.target_id=radst.id) " .
						"ORDER BY relation_types.name";*/
		if ($subpage=="annotator"){
			$sql = 	"SELECT  relations.source_id, " .
							"relations.target_id, " .
							"relation_types.name, " .
							"rasrc.text source_text, " .
							"rasrc.type source_type, " .
							"radst.text target_text, " .
							"radst.type target_type " .
							"FROM relations " .
							"JOIN relation_types " .
								"ON (relations.relation_type_id=relation_types.id " .
								"AND relations.source_id IN " .
									"(SELECT ran.id " .
									"FROM reports_annotations ran " .
									"JOIN annotation_types aty " .
									"ON (ran.report_id={$id} " .
									"AND ran.type=aty.name " .
									"AND aty.group_id IN " .
										"(SELECT annotation_set_id " .
										"FROM annotation_sets_corpora  " .
										"WHERE corpus_id=$cid) " .
									"))) " .
							"JOIN reports_annotations rasrc " .
								"ON (relations.source_id=rasrc.id) " .
							"JOIN reports_annotations radst " .
								"ON (relations.target_id=radst.id) " .
							"ORDER BY relation_types.name";		
			$allRelations = db_fetch_rows($sql);
		}
		if ($subpage=="annotator_anaphora"){
			$sql = 	"SELECT  relations.source_id, " .
							"relations.target_id, " .
							"relations.id, " .
							"relations.relation_type_id, " .
							"relation_types.name, " .
							"rasrc.text source_text, " .
							"rasrc.type source_type, " .
							"radst.text target_text, " .
							"radst.type target_type " .
							"FROM relations " .
							"JOIN relation_types " .
								"ON (relations.relation_type_id=relation_types.id " .
								"AND relation_types.annotation_set_id=9 " .
								"AND relations.source_id IN " .
									"(SELECT ran.id " .
									"FROM reports_annotations ran " .
									"JOIN annotation_types aty " .
									"ON (ran.report_id={$id} " .
									"AND ran.type=aty.name " .
									"AND aty.group_id IN " .
										"(SELECT annotation_set_id " .
										"FROM annotation_sets_corpora  " .
										"WHERE corpus_id=$cid) " .
									"))) " .
							"JOIN reports_annotations rasrc " .
								"ON (relations.source_id=rasrc.id) " .
							"JOIN reports_annotations radst " .
								"ON (relations.target_id=radst.id) " .
							"ORDER BY relation_types.name";		
			$allRelations = db_fetch_rows($sql);
			
			$sql = "SELECT * FROM relation_types WHERE annotation_set_id=9";
			$availableRelations = db_fetch_rows($sql);
			
		}		
		

		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text, t.group_id, ans.description setname, ansub.description subsetname, ansub.annotation_subset_id, t.name typename, t.short_description typedesc, an.stage, t.css, an.source"  .
				" FROM reports_annotations an" .
				" LEFT JOIN annotation_types t ON (an.type=t.name)" .
				" LEFT JOIN annotation_subsets ansub ON (t.annotation_subset_id=ansub.annotation_subset_id)" .
				" LEFT JOIN annotation_sets ans on (t.group_id=ans.annotation_set_id)" .
				" WHERE report_id = {$row['id']} ";
		$sql2 = "";
		$sql3 = "";

		if ($subpage=="annotator_anaphora"){
			$sql2 = $sql;
			$sql2 .= 
					" AND ans.annotation_set_id IN" .
						"(SELECT annotation_set_id " .
						"FROM annotation_sets_corpora  " .
						"WHERE corpus_id=$cid) " .
					" AND t.group_id=1 ";
			$sql .= " AND t.name='anafora_wyznacznik' "; 
		}

		else if ($subpage=="annotator" || $subpage=="autoextension"){
			$sql = $sql .
					" AND ans.annotation_set_id IN" .
						"(SELECT annotation_set_id " .
						"FROM annotation_sets_corpora  " .
						"WHERE corpus_id=$cid)";
			$sql2 = $sql;
			$sql3 = $sql;
			
			if ($_COOKIE['clearedLayer'] && $_COOKIE['clearedLayer']!="{}"){
				$sql = $sql . " AND group_id " .
						"NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedLayer']) . ") " ;
				$sql2 = $sql; 
			} 
			if ($_COOKIE['clearedSublayer'] && $_COOKIE['clearedSublayer']!="{}"){
				$sql = $sql . " AND (ansub.annotation_subset_id " .
						"NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedSublayer']) . ") " .
								"OR ansub.annotation_subset_id IS NULL) ";
				$sql2 = $sql; 
				//echo $sql;
			} 
			
			/*if ($subpage=="annotator" && $_COOKIE['leftLayer'] && $_COOKIE['leftLayer']!="{}"){
				$sql = $sql . " AND group_id " .
						"IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['leftLayer']) . ") " ;
				$sql2 = $sql2 . " AND group_id " .
						"NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['leftLayer']) . ") " ;
			} else {
				$sql = $sql . " AND group_id=0 ";
			}*/

			if ($subpage=="annotator" && $_COOKIE['leftSublayer'] && $_COOKIE['leftSublayer']!="{}"){
				$sql = $sql . " AND (ansub.annotation_subset_id " .
						"IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['leftSublayer']) . ") " .
								"OR ansub.annotation_subset_id IS NULL) ";
				$sql2 = $sql2 . " AND ansub.annotation_subset_id " .
						"NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['leftSublayer']) . ") " ;
			} else {
				$sql = $sql . " AND ansub.annotation_subset_id=0 "; //?
			}

									
		}
		if ($subpage=="autoextension")		
			$sql = $sql . " ORDER BY `typename` ASC, `text` ASC";	
		else if ($subpage=="annotator"){
			$sql = $sql . " ORDER BY `from` ASC, `level` DESC"; 
			$sql2 = $sql2 . " ORDER BY `from` ASC, `level` DESC"; 
			
		} else
			$sql = $sql . " ORDER BY `from` ASC, `level` DESC"; 
		//echo $sql;				
		
		$anns = db_fetch_rows($sql);
		/*echo "<pre>";
		var_dump($anns);
		echo "</pre>";*/
		
		
		$anns2 = null;
		$anns3 = null;
		if ($subpage=="annotator"){
			$anns2 = db_fetch_rows($sql2);
			$anns3 = db_fetch_rows($sql3);
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



		}
		else if ($subpage=="annotator_anaphora"){
			$anns2 = db_fetch_rows($sql2);
		}
		
		
		
		
		$exceptions = array();
		$htmlStr = new HtmlStr($row['content'], true);
		$htmlStr2 = new HtmlStr($row['content'], true);			
		
		if ( in_array($subpage, array("annotator", "autoextension", "preview", "tokenization", "annotator_anaphora"))){
		
			foreach ($anns as $ann){
				try{
					if ($subpage=="annotator"){
						if ($ann['stage']!="discarded")
							$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'], $ann['annotation_subset_id']), $ann['to']+1, "</an>");					
					}
//					else if ($subpage=="autoextension"){
//						if ($ann['stage']!="new")
//							$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", $ann['id'], "__".$ann['type'], $ann['group_id']), $ann['to']+1, "</an>");					
//						else					
//							$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", $ann['id'], $ann['type'], $ann['group_id']), $ann['to']+1, "</an>");					
//					}
					else if ($subpage=="preview"){
						if ($ann['stage']!="discarded")
						$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", $ann['id'], $ann['type'], $ann['group_id']), $ann['to']+1, "</an>");					
					}
					else if ($subpage!="tokenization"){
						$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");					
					}
					
				}catch (Exception $ex){
					try{
						$exceptions[] = sprintf("Annotation could not be displayed due to invalid border [%d,%d,%s]", $ann['from'], $ann['to'], $ann['text']);
						if ($ann['from'] == $ann['to']){
							$htmlStr->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
						}
						else{				
							$htmlStr->insertTag($ann['from'], "<b class='invalid_border_start' title='{$ann['from']}'>", $ann['from']+1, "</b>");
//							for ($i=$ann['from']+1; $i<$ann['to']; $i++)				
//								$htmlStr->insertTag($i, "<b class='invalid_border_middle' title='$i'>", $i+1, "</b>");
//							$htmlStr->insertTag($ann['to'], "<b class='invalid_border_end' title='{$ann['to']}'>", $ann['to']+1, "</b>");
						}
					}
					catch (Exception $ex2){
						fb($ex2);				
					}				
				}
			}
		}
		
		if ($subpage=="annotator" || $subpage=="annotator_anaphora"){
			foreach ($anns2 as $ann){
				try{
					if ($ann['stage']!="discarded")
						$htmlStr2->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'],  $ann['annotation_subset_id']), $ann['to']+1, "</an>");					
				}catch (Exception $ex){
					try{
						//$exceptions[] = sprintf("Annotation could not be displayed due to invalid border [%d,%d,%s]", $ann['from'], $ann['to'], $ann['text']);
						if ($ann['from'] == $ann['to']){
							$htmlStr2->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
						}
						else{				
							$htmlStr2->insertTag($ann['from'], "<b class='invalid_border_start' title='{$ann['from']}'>", $ann['from']+1, "</b>");
//							for ($i=$ann['from']+1; $i<$ann['to']; $i++)				
//								$htmlStr2->insertTag($i, "<b class='invalid_border_middle' title='$i'>", $i+1, "</b>");
//							$htmlStr2->insertTag($ann['to'], "<b class='invalid_border_end' title='{$ann['to']}'>", $ann['to']+1, "</b>");
						}
					}
					catch (Exception $ex2){
						fb($ex2);				
					}				
				}
			}									
		}
		
				
		
		if ( count($exceptions) > 0 )
			$this->set("exceptions", $exceptions);	
		
		//obsluga tokenow	 
		if ($subpage=="annotator" || $subpage=="tokenization" || $subpage=="annotator_anaphora"){

			$sql = "SELECT `from`, `to`" .
					" FROM tokens " .
					" WHERE report_id={$id}" .
					" ORDER BY `from` ASC";		
			$tokens = db_fetch_rows($sql);
			
			foreach ($tokens as $ann){
				try{
					$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", 0, "token", 0), $ann['to']+1, "</an>");
					if ($subpage=="annotator"){
						$htmlStr2->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", 0, "token", 0), $ann['to']+1, "</an>");
					}						
				}
				catch (Exception $ex){	
				}
			}
						
		}
		
		if ( $subpage=="annotator_anaphora" ){
			$sql_relations = "SELECT an.*, at.group_id" .
								" FROM relations r" .
								" JOIN reports_annotations an ON (r.source_id=an.id)" .
								" JOIN relation_types t ON (r.relation_type_id=t.id)" .
								" JOIN annotation_types at ON (an.type=at.name)" .
								" WHERE an.report_id = ?" .
								"   AND t.annotation_set_id" .
								" ORDER BY an.to DESC";
			$relations = db_fetch_rows($sql_relations, array($id));
			foreach ($relations as $r){
				if ($r[group_id] == 1)
					$htmlStr2->insert($r[to]+1, "<sup class='rel'>↦</sup>", false, true, false);
				else
					$htmlStr->insert($r[to]+1, "<sup class='rel'>↦</sup>", false, true, false);
			}
		}
		
		
		if ($subpage=="annotator"){
			/*****obsluga zdarzeń********/
			//lista dostepnych grup zdarzen dla danego korpusu
			$sql = "SELECT DISTINCT event_groups.event_group_id, event_groups.name " .
					"FROM corpus_event_groups " .
					"JOIN event_groups " .
						"ON (corpus_event_groups.corpus_id=$cid AND corpus_event_groups.event_group_id=event_groups.event_group_id) " .
					"JOIN event_types " .
						"ON (event_groups.event_group_id=event_types.event_group_id)";
			$event_groups = db_fetch_rows($sql);
			
			//lista zdarzen przypisanych do raportu
			$sql = "SELECT reports_events.report_event_id, " .
						  "event_groups.name AS groupname, " .
						  "event_types.name AS typename, " .
						  "event_types.event_type_id, " .
						  "count(reports_events_slots.report_event_slot_id) AS slots " .
						  "FROM reports_events " .
						  "JOIN reports " .
						  	"ON (reports_events.report_id={$id} " .
						  	"AND reports_events.report_event_id=reports.id) " .
					  	  "JOIN event_types " .
					  	  	"ON (reports_events.event_type_id=event_types.event_type_id) " .
				  	  	  "JOIN event_groups " .
				  	  	  	"ON (event_types.event_group_id=event_groups.event_group_id) " .
			  	  	  	  "LEFT JOIN reports_events_slots " .
			  	  	  	  	"ON (reports_events.report_event_id=reports_events_slots.report_event_id) " .
		  	  	  	  	  "GROUP BY (reports_events.report_event_id)";		
			$events = db_fetch_rows($sql);			
		}

		/*****flags******/
		$sql = "SELECT corpora_flags.corpora_flag_id AS id, corpora_flags.name, reports_flags.flag_id, flags.name AS fname " .
				"FROM corpora_flags " .
				"LEFT JOIN reports_flags " .
					"ON corpora_flags.corpora_id={$cid} " .
					"AND reports_flags.report_id={$id} " .
					"AND corpora_flags.corpora_flag_id=reports_flags.corpora_flag_id " .
				"LEFT JOIN flags " .
					"ON reports_flags.flag_id=flags.flag_id " .
				"WHERE corpora_flags.corpora_id={$cid}";
		$corporaflags = db_fetch_rows($sql);
		$sql = "SELECT flag_id AS id, name FROM flags ";
		$flags = db_fetch_rows($sql);

		
		// Kontrola dostępu do podstron
		if (!hasRole("admin") && !isCorpusOwner() ){
			if ( $subpage == "annotator" && !hasCorpusRole("annotate") ){
				$subpage = "";
				$this->set("page_permission_denied", "Brak dostępu do edytora anotacji");
			}else if ($subpage == "edit" && !hasCorpusRole("edit_documents") ){
				$subpage = "";
				$this->set("page_permission_denied", "Brak dostępu do edytora treści dokumentu");			
			}
		}
		$this->set_up_navigation_links($id, $corpus['id'], $where, $join, $group, $order, $where_prev, $where_next);
		$this->set('row', $row);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('p', $p);
		$this->set('status', $row['status']);
		$this->set('edit', $edit);
		$this->set('view', $view);
		$this->set('subpage', $subpage);
		$this->set('subpage_file', "inc_report_{$subpage}.tpl");
		$this->set('content_formated', reformat_content($row['content']));
		$this->set('annotations', $annotations);
		$this->set('sets', $annotation_set_map);
		$this->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->set('content_inline2', Reformat::xmlToHtml($htmlStr2->getContent()));
		$this->set('content_edit', $htmlStr->getContent());
		$this->set('subpages', $subpages);
		$this->set('allrelations',$allRelations);
		$this->set('event_groups',$event_groups);
		$this->set('events',$events);
		$this->set('report_id',$id);
		$this->set('corporaflags',$corporaflags);
		$this->set('flags',$flags);
		$this->set('anns',$anns);
		$this->set('availableRelations',$availableRelations);
		
	 	
		// Load and execute the perspective 
		$subpage = $subpage ? $subpage : "preview";
		$perspective_class_name = "Perspective".ucfirst($subpage);
		$perspective = new $perspective_class_name($this, $row);
		$perspective->execute();			
	}

	/**
	 * 
	 */
	function set_up_navigation_links($id, $corpus_id, $where, $join, $group, $order, $where_next, $where_prev)
	{
		$order_reverse = str_replace(array("ASC", "DESC"), array("<<<", ">>>"), $order);
		$order_reverse = str_replace(array("<<<", ">>>"), array("DESC", "ASC"), $order_reverse);
		
		$row_first = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order LIMIT 1");
		$row_prev = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 1");
		$row_prev_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 9,10");
		$row_prev_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group ORDER BY $order_reverse LIMIT 99,100");

		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_prev $group";
		$row_prev_c = $group ? count(db_fetch_rows($sql)) : intval(db_fetch_one($sql));

		$row_last = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order_reverse LIMIT 1");
		$row_next = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 1");
		$row_next_10 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 9,10");		
		$row_next_100 = db_fetch_one("SELECT r.id FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group ORDER BY $order LIMIT 99,100");			
		
		$sql = "SELECT COUNT(*) FROM reports r $join WHERE r.corpora = $corpus_id $where AND $where_next $group";
		$row_next_c = $group ? count(db_fetch_rows($sql)) : intval(db_fetch_one($sql));
		
		$this->set('row_prev_c', $row_prev_c);
		$this->set('row_number', $row_prev_c + 1);
		$this->set('row_first', $row_first);
		$this->set('row_prev', $row_prev);
		$this->set('row_prev_10', $row_prev_10);
		$this->set('row_prev_100', $row_prev_100);
		$this->set('row_last', $row_last);
		$this->set('row_next', $row_next);
		$this->set('row_next_10', $row_next_10);
		$this->set('row_next_100', $row_next_100);
		$this->set('row_next_c', $row_next_c);		
	}
}

?>


