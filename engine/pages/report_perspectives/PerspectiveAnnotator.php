<?php

class PerspectiveAnnotator extends CPerspective {
	
	function execute()
	{
		$this->set_annotation_menu();		
	}
	
	function set_annotation_menu()
	{
		global $mdb2;
		$sql = "SELECT * FROM annotation_types t JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id) WHERE c.corpus_id = {$this->document['corpora']} ORDER BY t.name";
		$select_annotation_types = new HTML_Select('annotation_type', 1, false, array("id"=>"annotation_type", "disabled"=>"true"));
		$select_annotation_types->loadQuery($mdb2, $sql, 'name', 'name', "");		

		$annotation_types = db_fetch_rows($sql);
					 							
		$this->page->set('select_annotation_types', $select_annotation_types->toHtml());				
		$this->page->set('annotation_types', $annotation_types);
	}
}

?>
