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
		global $db;

		$exceptions = array();
		$verify = isset($_REQUEST['verify']) ? true : false;
		$report_id = intval($this->document['id']);
		$annotation_set_id = intval($_GET['annotation_set_id']);
		
		$annotationSets = DbAnnotation::getBootstrappedAnnotationsSummary($db, $report_id);

		if ( count($annotationSets)==1 && $annotation_set_id == 0 ){
			$annotation_set_id = $annotationSets[0]['annotation_set_id'];
		}

		$annotationsNew = DbAnnotation::getNewBootstrappedAnnotations($db, $report_id, $annotation_set_id);
		$annotationsOther = DbAnnotation::getOtherBootstrappedAnnotations($db, $report_id, $annotation_set_id);
		$content = $this->document['content'];
		
		try{
			$htmlStr = new HtmlStr2($content, true);
			foreach ($annotationsNew as $ann){
				try{
                    $htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", $ann['id'], $ann['type'], $ann['group_id']), $ann['to']+1, "</an>");
                }
				catch(Exception $ex){
					$exceptions[] = $ex->getMessage();
				}											
			}
			$content = $htmlStr->getContent();
		}
		catch(Exception $ex){
			$exceptions[] = $ex->getMessage();			
		}

		$annotationSetTypes = array();
		foreach ($annotationSets as $set){
			$asetid = $set['annotation_set_id'];
			$annotationSetTypes[$asetid] = DbAnnotation::getAnnotationTypesForChangeList($db, $asetid);
		}

		if ( count($exceptions) > 0 ){
			$this->page->set("exceptions", $exceptions);
		}


		$this->page->set('verify', $verify);
		$this->page->set('annotations', $annotationsNew);
		$this->page->set('content', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('annotation_types', $annotationSetTypes);
		$this->page->set('annotation_sets', $annotationSets);
		$this->page->set('annotation_set_id', $annotation_set_id);
	}
}

?>