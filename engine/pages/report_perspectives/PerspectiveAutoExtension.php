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
//		foreach ($annotationsOther as $ann){
//			try{
//				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:__%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");											
//			}
//			catch(Exception $ex){
//				fb($ann);
//			}											
//		}
				
		$this->page->set('verify', $verify);
		$this->page->set('annotations', $annotationsNew);
		$this->page->set('content', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('annotation_types', $this->getAnnotationTypesForChangeList());
	}
	
	/**
	 * Loads new annotations marked as source=bootstrapping.
	 */
	function getNewBootstrappedAnnotations(){
		$report_id = intval($this->document[id]);
		$sql = "SELECT an.*, t.name AS type" .
				" FROM reports_annotations an" .
				" JOIN annotation_types t ON (an.type_id = t.annotation_type_id)" .
				" WHERE an.stage='new' AND an.source='bootstrapping' AND an.report_id = ?" .
				" ORDER BY an.from, an.to, an.text";
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
			
}

?>