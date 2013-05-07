<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAutoExtension extends CPerspective {
	
	function execute()
	{
		$verify = isset($_REQUEST[verify]) ? true : false;
		$annotationsNew = $this->getNewBootstrappedAnnotations();
		$annotationsOther = $this->getOtherBootstrappedAnnotations();

		$htmlStr = new HtmlStr($this->document[content], true);
		foreach ($annotationsNew as $ann){
			try{
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");
			}
			catch(Exception $ex){
				fb($ann);
			}											
		}
		foreach ($annotationsOther as $ann){
			try{
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:__%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");											
			}
			catch(Exception $ex){
				fb($ann);
			}											
		}
				
		$this->page->set('verify', $verify);
		$this->page->set('annotations', $annotationsNew);
		$this->page->set('content', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('models', PerspectiveAutoExtension::getModels());		
		$this->page->set('annotation_types', $this->getAnnotationTypesForChangeList());
	}
	
	/**
	 * Loads new annotations marked as source=bootstrapping.
	 */
	function getNewBootstrappedAnnotations(){
		$report_id = intval($this->document[id]);
		$sql = "SELECT *" .
				" FROM reports_annotations" .
				" WHERE stage='new' AND source='bootstrapping' AND report_id = ?" .
				" ORDER BY `from`, `to`, `text`";
		$annotations =	db_fetch_rows($sql, array($report_id));
		return $annotations;
	}

	/**
	 * Loads bootstrapped annotations that are not marked as new
	 */
	function getOtherBootstrappedAnnotations(){
		$report_id = intval($this->document[id]);
		$sql = "SELECT * FROM reports_annotations WHERE stage='final' AND source='bootstrapping' AND report_id = ?";
		$annotations =	db_fetch_rows($sql, array($report_id));
		return $annotations;
	}

	function getAnnotationTypesForChangeList(){
		$sql = "SELECT * FROM annotation_types WHERE group_id=1 ORDER BY name";
		return db_fetch_rows($sql);
	}
		


	
	static function getModels(){
	
		$models = array();
		$models[] = array("name"=>"5 names", "file" => "crf_model_4corpora-5nam_7x24-feat-dict-gen.ini", "description" => "+ First names, surnames, cities, countries and roads (trained on 4 corpora with context [-3,+3] using 38 features: basic, lexical, dictonaries)" );
		$models[] = array("name"=>"50+ names", "file" => "crf_model_gpw-all-nam_orth-base-ctag.ini", "description" => "+ All proper names (trained on Wikinews with context [-1,+1])");
		return $models;		
	} 	
	
}

?>