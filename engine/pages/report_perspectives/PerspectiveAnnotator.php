<?php

class PerspectiveAnnotator extends CPerspective {
	
	function execute()
	{
		$this->set_annotation_menu();		
	}
	
	function set_annotation_menu()
	{
		global $mdb2;
		$sql = "SELECT t.*, s.description, ss.description AS subset FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
				" WHERE c.corpus_id = {$this->document['corpora']}" .
				" ORDER BY subset, t.name";
		$select_annotation_types = new HTML_Select('annotation_type', 1, false, array("id"=>"annotation_type", "disabled"=>"true"));
		$select_annotation_types->loadQuery($mdb2, $sql, 'name', 'name', "");		

		$annotation_types = db_fetch_rows($sql);
		
		$annotation_grouped = array();
		foreach ($annotation_types as $an){
			$set = $an['description'];
			$subset = $an['subset'] ? $an['subset'] : "none"; 
			if (!isset($annotation_grouped[$set]))
				$annotation_grouped[$set] = array();
			if (!isset($annotation_grouped[$set][$subset]))
				$annotation_grouped[$set][$subset] = array();
			$annotation_grouped[$set][$subset][] = $an;
		}
					 							
		$this->page->set('select_annotation_types', $select_annotation_types->toHtml());				
		$this->page->set('annotation_types', $annotation_grouped);
	}
}

?>