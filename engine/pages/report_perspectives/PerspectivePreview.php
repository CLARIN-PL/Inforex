<?php

class PerspectivePreview extends CPerspective {
	
	function execute()
	{
		global $mdb2;
		$sql = "SELECT t.*, s.description as `set`, ss.description AS subset, s.annotation_set_id as groupid FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
				" WHERE c.corpus_id = {$this->document['corpora']}";// .
				//" ORDER BY `set`, subset, t.name";
		$annotation_types = db_fetch_rows($sql);
		$annotationCss = "";
		foreach ($annotation_types as $an){
			if ($an['css']!=null && $an['css']!="") $annotationCss = $annotationCss . "span." . $an['name'] . " {" . $an['css'] . "} \n"; 
		
		}
		$this->page->set('new_style',$annotationCss);
		
	}
	
}

?>
