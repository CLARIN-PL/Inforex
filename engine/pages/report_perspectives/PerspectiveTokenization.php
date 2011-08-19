<?php

class PerspectiveTokenization extends CPerspective {
	
	function execute()
	{
		$this->set_annotations();
				
		//$topics = db_fetch_rows("SELECT * FROM reports_types ORDER BY `name`");
		//$this->page->set('topics', $topics);
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
		
		if ( count($exceptions) > 0 )
			$this->set("exceptions", $exceptions);	
	

		$sql = "SELECT `from`, `to`" .
				" FROM tokens " .
				" WHERE report_id={$id}" .
				" ORDER BY `from` ASC";		
		$tokens = db_fetch_rows($sql);
		
		foreach ($tokens as $ann){
			try{
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", 0, "token", 0), $ann['to']+1, "</an>", true);
			}
			catch (Exception $ex){	
			}
		}
		
		$this->page->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		//$this->page->set('content_edit', $htmlStr->getContent());
		$this->page->set('anns',$anns);
	}

	
}

?>
