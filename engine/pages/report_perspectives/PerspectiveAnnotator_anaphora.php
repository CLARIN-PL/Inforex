<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAnnotator_anaphora extends CPerspective {
	
	function execute()
	{
		$this->set_annotation_menu();		
		$this->set_relations();		
		$this->set_annotations();
	}
	
	function set_annotation_menu()
	{
		global $mdb2;
		$sql = "SELECT t.*, s.description as `set`, ss.description AS subset, ss.annotation_subset_id AS subsetid, s.annotation_set_id as groupid FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
				" WHERE c.corpus_id = {$this->document['corpora']}" .
				" ORDER BY `set`, subset, t.name";
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
			$annotation_grouped[$set][$subset][] = $an;
		}
		$this->page->set('select_annotation_types', $select_annotation_types->toHtml());				
		$this->page->set('annotation_types', $annotation_grouped);
	}
	
	function set_relations(){
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
								"ON (ran.report_id={$this->page->id} " .
								"AND ran.type=aty.name " .
								"AND aty.group_id IN " .
									"(SELECT annotation_set_id " .
									"FROM annotation_sets_corpora  " .
									"WHERE corpus_id={$this->page->cid}) " .
								"))) " .
						"JOIN reports_annotations rasrc " .
							"ON (relations.source_id=rasrc.id) " .
						"JOIN reports_annotations radst " .
							"ON (relations.target_id=radst.id) " .
						"ORDER BY relation_types.name";		
		$allRelations = db_fetch_rows($sql);
		$sql = "SELECT * FROM relation_types WHERE annotation_set_id=9";
		$availableRelations = db_fetch_rows($sql);
		$this->page->set('allrelations',$allRelations);
		$this->page->set('availableRelations',$availableRelations);
	}
	
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
				" WHERE report_id = {$row['id']} ";
		$sql2 = $sql;
		$sql2 .= 
				" AND ans.annotation_set_id IN" .
					"(SELECT annotation_set_id " .
					"FROM annotation_sets_corpora  " .
					"WHERE corpus_id=$cid) " .
				" AND t.group_id=1 ";
		$sql .= " AND t.name='anafora_wyznacznik' "; 
		$sql .= " ORDER BY `from` ASC, `len` DESC";
		$sql2 .= " ORDER BY `from` ASC, `len` DESC"; 
		
		$anns = db_fetch_rows($sql);
		$anns2 = db_fetch_rows($sql2);
		
		$exceptions = array();
		$htmlStr = new HtmlStr($row['content'], true);
		$htmlStr2 = new HtmlStr($row['content'], true);
		foreach ($anns as $ann){
			try{
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");					
			}catch (Exception $ex){
				try{
					$exceptions[] = sprintf("Annotation could not be displayed due to invalid border [%d,%d,%s]", $ann['from'], $ann['to'], $ann['text']);
					if ($ann['from'] == $ann['to']){
						$htmlStr->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					}
					else{				
						$htmlStr->insertTag($ann['from'], "<b class='invalid_border_start' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					}
				}
				catch (Exception $ex2){
					fb($ex2);				
				}				
			}
		}
		
		foreach ($anns2 as $ann){
			try{
				if ($ann['stage']!="discarded")
					$htmlStr2->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'],  $ann['annotation_subset_id']), $ann['to']+1, "</an>");					
			}catch (Exception $ex){
				try{
					if ($ann['from'] == $ann['to']){
						$htmlStr2->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					}
					else{				
						$htmlStr2->insertTag($ann['from'], "<b class='invalid_border_start' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					}
				}
				catch (Exception $ex2){
					fb($ex2);				
				}				
			}
		}									
		
		if ( count($exceptions) > 0 )
			$this->page->set("exceptions", $exceptions);	
		
		//obsluga tokenow	 

		$sql = "SELECT `from`, `to`" .
				" FROM tokens " .
				" WHERE report_id={$id}" .
				" ORDER BY `from` ASC";		
		$tokens = db_fetch_rows($sql);
		
		foreach ($tokens as $ann){
			try{
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", 0, "token", 0), $ann['to']+1, "</an>");
			}
			catch (Exception $ex){	
			}
		}
		
		$sql_relations = "SELECT an.*, at.group_id, r.target_id" .
							" FROM relations r" .
							" JOIN reports_annotations an ON (r.source_id=an.id)" .
							" JOIN relation_types t ON (r.relation_type_id=t.id)" .
							" JOIN annotation_types at ON (an.type=at.name)" .
							" WHERE an.report_id = ?" .
							"   AND t.annotation_set_id = 9" .
							" ORDER BY an.to ASC";
		$relations = db_fetch_rows($sql_relations, array($id));
		
		foreach ($relations as $r){
			if ($r[group_id] == 1)
				$htmlStr2->insert($r[to]+1, "<sup class='rel' target='".$r['target_id']."'></sup>", false, true, false);
			else
				$htmlStr->insert($r[to]+1, "<sup class='rel' target='".$r['target_id']."'/></sup>", false, true, false);
		}
		
		$this->page->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('content_inline2', Reformat::xmlToHtml($htmlStr2->getContent()));
		$this->page->set('anns',$anns);
	}


	
}

?>