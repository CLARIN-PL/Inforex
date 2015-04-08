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
		
		$annotationSets = $this->getBootstrappedAnnotationsSummary($db, $report_id);

		if ( count($annotationSets)==1 && $annotation_set_id == 0 ){
			$annotation_set_id = $annotationSets[0]['annotation_set_id'];
		}

		$annotationsNew = $this->getNewBootstrappedAnnotations($db, $report_id, $annotation_set_id);
		$annotationsOther = $this->getOtherBootstrappedAnnotations($db, $report_id, $annotation_set_id);
		$content = $this->document['content'];
		
		try{
			$htmlStr = new HtmlStr2($content, true);
			foreach ($annotationsNew as $ann){
				try{
					$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");
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
			$annotationSetTypes[$asetid] = $this->getAnnotationTypesForChangeList($db, $asetid);
		}

		if ( count($exceptions) > 0 ){
			$this->page->set("exceptions", $exceptions);
		}		
				
		$this->page->set('verify', $verify);
		$this->page->set('annotations', $annotationsNew);
		$this->page->set('content', Reformat::xmlToHtml($content));
		$this->page->set('annotation_types', $annotationSetTypes);
		$this->page->set('annotation_sets', $annotationSets);
		$this->page->set('annotation_set_id', $annotation_set_id);
	}
	
	function getBootstrappedAnnotationsSummary($db, $report_id){
		$sql = "SELECT s.description AS annotation_set_name," .
				"	 s.annotation_set_id," .
				"	 SUM(IF(an.stage='new',1,0)) AS count_new," .
				"	 SUM(IF(an.stage='final',1,0)) AS count_final," .
				"	 SUM(IF(an.stage='discarded',1,0)) AS count_discarded" .
				" FROM reports_annotations_optimized an" .
				" JOIN annotation_types t ON (an.type_id = t.annotation_type_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" WHERE an.source='bootstrapping' AND an.report_id = ?" .
				" GROUP BY t.group_id" .
				" ORDER BY an.from, an.to, an.text";
		$annotations =	$db->fetch_rows($sql, array($report_id));
		return $annotations;		
	}
	
	/**
	 * Loads new annotations marked as source=bootstrapping.
	 */
	function getNewBootstrappedAnnotations($db, $report_id, $annotation_set_id){
		$sql = "SELECT an.*, t.name AS type, t.group_id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types t ON (an.type_id = t.annotation_type_id)" .
				" WHERE an.stage='new'" .
				" 	AND an.source='bootstrapping' " .
				"	AND an.report_id = ?" .
				"	AND t.group_id = ?" .
				" ORDER BY an.from, an.to, an.text";
		$annotations =	$db->fetch_rows($sql, array($report_id, $annotation_set_id));
		return $annotations;
	}	

	/**
	 * Loads bootstrapped annotations that are not marked as new
	 */
	function getOtherBootstrappedAnnotations($db, $report_id, $annotation_set_id){
		$sql = "SELECT * FROM reports_annotations an" .
				" JOIN annotation_types t ON (an.type_id = t.annotation_type_id)" .
				" WHERE an.stage='final' " .
				"	AND an.source='bootstrapping' " .
				"	AND an.report_id = ?" .
				"	AND t.group_id = ?";
		$annotations =$db->fetch_rows($sql, array($report_id, $annotation_set_id));
		return $annotations;
	}

	function getAnnotationTypesForChangeList($db, $annotation_set_id){
		$sql = "SELECT * FROM annotation_types WHERE group_id=? ORDER BY name";
		return $db->fetch_rows($sql, array($annotation_set_id));
	}
			
}

?>