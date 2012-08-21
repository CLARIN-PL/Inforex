<?php

class PerspectiveAnnotator extends CPerspective {
	
	function execute()
	{
		$this->set_panels();
		$this->set_annotation_menu();
		$this->set_relations();
		$this->set_relation_sets();		
		$this->set_events();
		$this->set_annotations();
	}
	
	/**
	 * Set up twin panels.
	 */
	function set_panels()
	{
		$this->page->set('showRight', $_COOKIE['showRight']=="true"?true:false);
	}
	
	/**
	 * 
	 */
	function set_annotation_menu()
	{
		global $mdb2;
		$sql = "SELECT t.*, s.description as `set`" .
				"	, ss.description AS subset" .
				"	, ss.annotation_subset_id AS subsetid" .
				"	, s.annotation_set_id AS groupid" .
				"	, ac.annotation_name AS common" .
				" FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" LEFT JOIN annotation_types_common ac ON (t.name = ac.annotation_name)" .
				" LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
				" WHERE c.corpus_id = {$this->document['corpora']}" .
				" ORDER BY `set`, subset, t.short_description, t.name";
		$select_annotation_types = new HTML_Select('annotation_type', 1, false, array("id"=>"annotation_type", "disabled"=>"true"));
		$select_annotation_types->loadQuery($mdb2, $sql, 'name', 'name', "");		
		$annotation_types = db_fetch_rows($sql);
		$annotationCss = "";
		$annotation_grouped = array();
		foreach ($annotation_types as $an){
			if ($an['css']!=null && $an['css']!="") $annotationCss = $annotationCss . "span." . $an['name'] . " {" . $an['css'] . "} \n"; 
			$set = $an['set'];
			$subset = $an['subset'] ? $an['subset'] : "none"; 
			if (!isset($annotation_grouped[$set])){
				$annotation_grouped[$set] = array();
				$annotation_grouped[$set]['groupid']=$an['groupid']; 
			}
			if (!isset($annotation_grouped[$set][$subset])){
				$annotation_grouped[$set][$subset] = array();
				$annotation_grouped[$set][$subset]['subsetid']=$an['subsetid'];
			}
			$annotation_grouped[$set][$subset][$an[name]] = $an;
		}
		$this->page->set('select_annotation_types', $select_annotation_types->toHtml());				
		$this->page->set('annotation_types', $annotation_grouped);
	}
	
	/**
	 * 
	 */
	function set_relation_sets(){
		global $db;
		$sql = 	"SELECT * FROM relation_sets ";
		$relation_sets = $db->fetch_rows($sql);
		$types = explode(",",preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['active_annotation_types']));
		foreach($relation_sets as $key => $rel_set)
			$relation_sets[$key]['active'] = ($_COOKIE['active_annotation_types'] ? (in_array($rel_set['relation_set_id'],$types) ? 1 : 0) : 1 );
		$this->page->set('relation_sets', $relation_sets);
	}
	
	/**
	 * 
	 */
	function set_relations(){
		$sql = 	"SELECT  relations.id, " .
						"relations.source_id, " .
						"srct.group_id AS source_group_id, " .
						"srct.annotation_subset_id AS source_annotation_subset_id, " .
						"dstt.group_id AS target_group_id, " .
						"dstt.annotation_subset_id AS target_annotation_subset_id, " .
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
								"ON (ran.report_id={$this->page->id} " .
								"AND ran.type=aty.name " .
								"AND aty.group_id IN " .
									"(SELECT annotation_set_id " .
									"FROM annotation_sets_corpora  " .
									"WHERE corpus_id={$this->page->cid}) " .
								"))) " .
							($_COOKIE['active_annotation_types'] && $_COOKIE['active_annotation_types']!="{}" 
							? " AND (relation_types.relation_set_id IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['active_annotation_types']) . ") OR relation_types.name='Continous') " 
							: "") .
						"JOIN reports_annotations rasrc " .
							"ON (relations.source_id=rasrc.id) " .
						"JOIN reports_annotations radst " .
							"ON (relations.target_id=radst.id) " .
						"LEFT JOIN annotation_types srct ON (rasrc.type=srct.name) " .
						"LEFT JOIN annotation_types dstt ON (radst.type=dstt.name) " .
						"ORDER BY relation_types.name";		
		$allRelations = db_fetch_rows($sql);
		$this->page->set('allrelations',$allRelations);
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
		$event_groups = db_fetch_rows($sql);
		
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
		$events = db_fetch_rows($sql);			
		$this->page->set('event_groups',$event_groups);
		$this->page->set('events',$events);
		
	}
	
	/**
	 * 
	 */
	function set_annotations(){
		$subpage = $this->page->subpage;
		$id = $this->page->id;
		$cid = $this->page->cid;
		$row = $this->page->row;
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text, t.group_id, ans.description setname, ansub.description subsetname, ansub.annotation_subset_id, t.name typename, t.short_description typedesc, an.stage, t.css, an.source"  .
				" FROM reports_annotations an" .
				" LEFT JOIN annotation_types t ON (an.type=t.name)" .
				" LEFT JOIN annotation_subsets ansub ON (t.annotation_subset_id=ansub.annotation_subset_id)" .
				" LEFT JOIN annotation_sets ans on (t.group_id=ans.annotation_set_id)" .
				" WHERE report_id = {$row['id']} " .
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
		} 
		
		if ($_COOKIE['rightSublayer'] && $_COOKIE['rightSublayer']!="{}"){
			$sql = $sql . " AND ansub.annotation_subset_id " .
					"NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['rightSublayer']) . ") " ;
			$sql2 = $sql2 . " AND (ansub.annotation_subset_id " .
					"IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['rightSublayer']) . ") " .
							"OR ansub.annotation_subset_id IS NULL) ";
					
		} 
		else {
			$sql2 = $sql2 . " AND ansub.annotation_subset_id=0 "; 
		}
		$sql = $sql . " ORDER BY t.level ASC, t.name, `from` ASC, `len` DESC"; 
		$sql2 = $sql2 . " ORDER BY `from` ASC, `len` DESC";
		$sql3 = $sql3 . " ORDER BY `from` ASC";
		
		$anns = db_fetch_rows($sql);
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

		$exceptions = array();
		$content = str_replace("\n", "\n ", $row['content']);		
		$content2 = $content;
		
		try{
			$htmlStr =  new HtmlStr2($content, true);
			$htmlStr2 = new HtmlStr2($content, true);

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
			$relations = db_fetch_rows($sql_relations, array($id));
			
			$show_relation["leftContent"] = array();
			$show_relation["rightContent"] = array();
			foreach ($anns as $ann){
				if ($ann['stage']=="final" ){
					$show_relation["leftContent"][$ann['id']] = array();
				}			
			}
			foreach ($anns2 as $ann){
				if ($ann['stage']=="final" ){
					$show_relation["rightContent"][$ann['id']] = array();
				}			
			}
				
			foreach ($relations as $r){
				if(array_key_exists($r['source_id'],$show_relation["leftContent"]) && array_key_exists($r['target_id'],$show_relation["leftContent"]))
					$show_relation["leftContent"][$r['source_id']][] = "<sup class='rel' title='".$r['name']."' sourcegroupid='".$r['source_id']."' target='".$r['target_id']."'/></sup>";
				if(array_key_exists($r['source_id'],$show_relation["rightContent"]) && array_key_exists($r['target_id'],$show_relation["rightContent"]))
					$show_relation["rightContent"][$r['source_id']][] = "<sup class='rel' title='".$r['name']."' sourcegroupid='".$r['source_id']."' target='".$r['target_id']."'/></sup>";
			}
					
			foreach ($anns as $ann){
				try{
					if ($ann['stage']=="final" ){
						$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'], $ann['annotation_subset_id']), $ann['to']+1, "</an>".implode($show_relation["leftContent"][$ann['id']]));
					}					
				}
				catch (Exception $ex){
					try{
						$exceptions[] = sprintf("%s, id=%d, from=%d, to=%d, type=%s, text='%s'", $ex->getMessage(), $ann['id'], $ann['from'], $ann['to'], $ann['type'], $ann['text']);
						//$exceptions[] = ;
						if ($ann['from'] == $ann['to']){
							$htmlStr->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
						}
						else{				
							$htmlStr->insertTag($ann['from'], "<b class='invalid_border_start' title='id={$ann['id']},from={$ann['from']}'>", $ann['from']+1, "</b>");
							$htmlStr->insertTag($ann['to'], "<b class='invalid_border_end' title='id={$ann['id']},to={$ann['to']}'>", $ann['to']+1, "</b>");
						}
					}
					catch (Exception $ex2){
						fb($ex2);			
						fb($ann);	
					}				
				}
			}
			
			foreach ($anns2 as $ann){
				try{
					if ($ann['stage']!="discarded"){
						$htmlStr2->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'], $ann['annotation_subset_id']), $ann['to']+1, "</an>".implode($show_relation["rightContent"][$ann['id']]));
					}					
				}
				catch (Exception $ex){
					try{
						//$exceptions[] = sprintf("Annotation could not be displayed due to invalid border [%d,%d,%s]", $ann['from'], $ann['to'], $ann['text']);
						$exceptions[] = $ex->getMessage();
						if ($ann['from'] == $ann['to']){
							$htmlStr2->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
						}
						else{				
							$htmlStr2->insertTag($ann['from'], "<b class='invalid_border_start' title='{$ann['from']}'>", $ann['from']+1, "</b>");
						}
					}
					catch (Exception $ex2){
						fb($ex2);				
						fb($ann);	
					}				
				}
			}
			
			//obsluga tokenow	 
			$sql = "SELECT `from`, `to`, `eos`" .
					" FROM tokens " .
					" WHERE report_id={$id}" .
					" ORDER BY `from` ASC";		
			$tokens = db_fetch_rows($sql);
			
			foreach ($tokens as $ann){
				try{
					$htmlStr->insertTag((int)$ann['from'], sprintf("<an#%d:%s:%d>", 0, "token" . ($ann['eos'] ? " eos" : ""), 0), $ann['to']+1, "</an>", true);
					
					if ($subpage=="annotator"){
						$htmlStr2->insertTag((int)$ann['from'], sprintf("<an#%d:%s:%d>", 0, "token" . ($ann['eos'] ? " eos" : ""), 0), $ann['to']+1, "</an>", true);
					}						
				}
				catch (Exception $ex){
					fb($ex);	
				}
			}
			
			$content = $htmlStr->getContent();
			$content2 = $htmlStr2->getContent();
		}		
		catch (Exception $ex){
			$exceptions[] = $ex->getMessage();
		}
				
		if ( count($exceptions) > 0 )
			$this->page->set("exceptions", $exceptions);
		
		$this->page->set('sets', $annotation_set_map);
		$this->page->set('content_inline', Reformat::xmlToHtml($content));
		$this->page->set('content_inline2', Reformat::xmlToHtml($content2));
		$this->page->set('anns',$anns);
	}	
}

?>