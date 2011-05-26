<?php

class PerspectiveAutoExtension extends CPerspective {
	
	function execute()
	{
		$this->page->set('models', PerspectiveAutoExtension::getModels());
		$this->set_annotation_menu();		
	}
	
	function set_annotation_menu()
	{
		global $mdb2;
		$sql = "SELECT t.*, s.description as `set`, ss.description AS subset, s.annotation_set_id as groupid FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
				" WHERE c.corpus_id = {$this->document['corpora']}" .
				" ORDER BY `set`, subset, t.name";
		$select_annotation_types = new HTML_Select('annotation_type', 1, false, array("id"=>"annotation_type", "disabled"=>"true"));
		$select_annotation_types->loadQuery($mdb2, $sql, 'name', 'name', "");		

		$annotation_types = db_fetch_rows($sql);
		$annotationCss = "";
		//var_dump($annotation_types);
		$annotation_grouped = array();
		foreach ($annotation_types as $an){
			if ($an['css']!=null && $an['css']!="") $annotationCss = $annotationCss . "span." . $an['name'] . " {" . $an['css'] . "} \n"; 
			$set = $an['set'];
			$subset = $an['subset'] ? $an['subset'] : "none"; 
			if (!isset($annotation_grouped[$set])){
				$annotation_grouped[$set] = array();
				$annotation_grouped[$set]['groupid']=$an['groupid']; 
			}
			if (!isset($annotation_grouped[$set][$subset]))
				$annotation_grouped[$set][$subset] = array();
			$annotation_grouped[$set][$subset][] = $an;
		}
					 							
		$this->page->set('select_annotation_types', $select_annotation_types->toHtml());				
		$this->page->set('annotation_types', $annotation_grouped);
	}
	
	static function getModels(){
	
		$models = array();
		$models[] = array("file" => "crf_model_4corpora-5nam_7x24-feat-dict-gen.ini", "description" => "TMP model", "name"=>"TMP model");
		$models[] = array("file" => "crf_model_4corpora-5nam_7x24-feat-gen-dict.ini", "description" => "+ First names, surnames, cities, countries and roads (trained on 4 corpora with context [-3,+3] using 38 features: basic, lexical, dictonaries)", "name"=>"model1" );
		$models[] = array("file" => "crf_model_gpw-all-nam_orth-base-ctag.ini", "description" => "+ All proper names (trained on Wikinews with context [-1,+1])", "name"=>"model2");
		$models[] = array("file" => "crf_model_gpw-wiki-police-infi_orth-base-ctag_w-3-2_5nam.ini", "description" => "- First names, surnames, cities, countries and roads (trained on 4 corpora with context [-3,+2])", "name"=>"model3");
		$models[] = array("file" => "crf_model_gpw-wiki-police-infi_orth-base-ctag_w-1-1_5nam.ini", "description" => "- First names, surnames, cities, countries and roads (trained on 4 corpora with context [-1,+1])", "name"=>"model4");
		$models[] = array("file" => "crf_model_gpw-5nam_10-feat.ini", "description" => "- 5 types of names with 10 features", "name"=>"model5");
		$models[] = array("file" => "crf_model_gpw-5nam_7x24-feat.ini", "description" => "- 5 types of names with 24 features and context [-3,+3]", "name"=>"model6");
		$models[] = array("file" => "crf_model_4corpora-5nam_7x24-feat.ini", "description" => "- 5 types of names with 24 features, trained on 4 corpora and context [-3,+3]", "name"=>"model7");
		return $models;		
	} 	
}

?>