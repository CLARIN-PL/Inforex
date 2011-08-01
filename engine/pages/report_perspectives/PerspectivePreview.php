<?php

class PerspectivePreview extends CPerspective {
	
	function execute()
	{
		global $mdb2;
		$sql = "SELECT t.*, s.description as `set`, ss.description AS subset, s.annotation_set_id as groupid FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
				" WHERE c.corpus_id = {$this->document['corpora']}";
		$annotation_types = db_fetch_rows($sql);
		$annotationCss = "";
		foreach ($annotation_types as $an){
			if ($an['css']!=null && $an['css']!="") $annotationCss = $annotationCss . "span." . $an['name'] . " {" . $an['css'] . "} \n"; 
		}
		$this->page->set('new_style',$annotationCss);
		$this->set_annotations();
		
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
				" WHERE report_id = {$row['id']} " .
				" ORDER BY `from` ASC, `level` DESC"; 
		
		$anns = db_fetch_rows($sql);
		$exceptions = array();
		$htmlStr = new HtmlStr($row['content'], true);
		
		foreach ($anns as $ann){
			try{
				if ($ann['stage']!="discarded")
					$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", $ann['id'], $ann['type'], $ann['group_id']), $ann['to']+1, "</an>");					
				
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
		
		if ( count($exceptions) > 0 )
			$this->page->set("exceptions", $exceptions);	
		
		//obsluga tokenow	 
		$this->page->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		//$this->page->set('content_edit', $htmlStr->getContent());
		$this->page->set('anns',$anns);
	}

	
}

?>
