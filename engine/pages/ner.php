<?php
class Page_ner extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $config;
		
		
		$this->set('models', Page_ner::getModels());
	}
	
	static function getModels(){
	
		$models = array();
		$models[] = array("file" => "crf_model_4corpora-5nam_7x24-feat-dict-gen.ini", "description" => "TMP");
		$models[] = array("file" => "crf_model_4corpora-5nam_7x24-feat-gen-dict.ini", "description" => "+ First names, surnames, cities, countries and roads (trained on 4 corpora with context [-3,+3] using 38 features: basic, lexical, dictonaries)");
		$models[] = array("file" => "crf_model_gpw-all-nam_orth-base-ctag.ini", "description" => "+ All proper names (trained on Wikinews with context [-1,+1])");
		$models[] = array("file" => "crf_model_gpw-wiki-police-infi_orth-base-ctag_w-3-2_5nam.ini", "description" => "- First names, surnames, cities, countries and roads (trained on 4 corpora with context [-3,+2])");
		$models[] = array("file" => "crf_model_gpw-wiki-police-infi_orth-base-ctag_w-1-1_5nam.ini", "description" => "- First names, surnames, cities, countries and roads (trained on 4 corpora with context [-1,+1])");
		$models[] = array("file" => "crf_model_gpw-5nam_10-feat.ini", "description" => "- 5 types of names with 10 features");
		$models[] = array("file" => "crf_model_gpw-5nam_7x24-feat.ini", "description" => "- 5 types of names with 24 features and context [-3,+3]");
		$models[] = array("file" => "crf_model_4corpora-5nam_7x24-feat.ini", "description" => "- 5 types of names with 24 features, trained on 4 corpora and context [-3,+3]");
		return $models;		
	} 
}


?>
