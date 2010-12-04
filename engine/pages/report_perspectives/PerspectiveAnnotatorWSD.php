<?php

class PerspectiveAnnotatorWSD extends CPerspective {
	
	function execute()
	{
		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len" .
				" FROM reports_annotations an" .
				" JOIN annotation_types t ON (an.type=t.name)" .
				" WHERE report_id = {$this->document['id']}" .
				" AND t.group_id = 2" .
				" ORDER BY `from` ASC, `level` DESC";
		$anns = db_fetch_rows($sql);

		try{
			$htmlStr = new HtmlStr(html_entity_decode($this->document['content'], ENT_COMPAT, "UTF-8"));
			foreach ($anns as $ann){
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");
			}
		}catch (Exception $ex){
			custom_exception_handler($ex);
		}
		
		$this->page->set("content_inline", Reformat::xmlToHtml($htmlStr->getContent()));
		$this->set_dropdown_lists();		
	}

	function set_dropdown_lists()
	{
		global $mdb2;
		$sql = "SELECT * FROM annotation_types t JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id) WHERE c.corpus_id = {$this->document['corpora']} AND group_id=2 ORDER BY t.name";
		$select_annotation_types = new HTML_Select('annotation_type', 1, false, array("id"=>"annotation_type", "disabled"=>"true"));
		$select_annotation_types->loadQuery($mdb2, $sql, 'name', 'name', "");		

		$annotation_types = db_fetch_rows($sql);
					 							
		$this->page->set('select_annotation_types', $select_annotation_types->toHtml());				
		$this->page->set('annotation_types', $annotation_types);
	}
}

?>
