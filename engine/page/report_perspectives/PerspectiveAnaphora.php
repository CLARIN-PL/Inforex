<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveAnaphora extends CPerspective {
	
	function execute()
	{
		$document_id = $this->document[id];
		
		$rows = db_fetch_rows("SELECT ans.from AS ans_from, ans.to AS ans_to, ans.type AS ans_type, ans.text AS ans_text," .
									" ant.from AS ant_from, ant.to AS ant_to, ant.type AS ant_type, ant.text AS ant_text," .
								    " r.*, t.name AS relation_name" .
								" FROM relations r" .
								" JOIN relation_types t ON (t.id=r.relation_type_id)" .
								" JOIN reports_annotations ans ON (ans.id=r.source_id)" .
								" JOIN reports_annotations ant ON (ant.id=r.target_id) " .
								" WHERE ans.report_id = ?" .
								"   AND t.id >=6 AND t.id<=14 " .
								" ORDER BY ant_from ASC", array($document_id));		
		
		$content = $this->load_document_content($rows);
		
		$this->page->set('content_inline', $content);		
		$this->page->set('relations', $rows);
	}
	
		/**
	 * 
	 */
	function load_document_content($relations){

		$index = array();
		$inserted = array();
		$next_id = 1;

		$elements = array();

		try{
			//$htmlStr = new HtmlStr(html_entity_decode($this->document['content'], ENT_COMPAT, "UTF-8"));
			$htmlStr = new HtmlStr($this->document['content']);
			
			foreach ($relations as $ann){			
				if ( !isset($elements[$ann[target_id]]) ){
					$annotation = sprintf("<an#%d:%s>", $ann['target_id'], $ann['ant_type']);
					$elements[$ann[target_id]] = array("annotation"=>$annotation,
														"from"=>$ann['ant_from'],
														"to"=>$ann['ant_to'],
														"before"=>"", 
														"after"=>array());

					$element_id = $next_id++;
					$index[$ann[target_id]]	= $element_id;
					$sup = "<#$element_id>";
					
					$elements[$ann[target_id]][before] = $sup;
				}				
			}
					
			foreach ($relations as $ann){
				$element_id = $index[$ann[target_id]];
				$sup = "<#↦$element_id>";
				
				if ( !isset($elements[$ann[source_id]]) ){
					$annotation = sprintf("<an#%d:%s>", $ann['source_id'], $ann['ans_type']);
					$elements[$ann[source_id]] = array("annotation"=>$annotation, 
														"from"=>$ann['ans_from'],
														"to"=>$ann['ans_to'],
														"before"=>"", 
														"after"=>array());					
				}
				
				$elements[$ann[source_id]][after][] = $sup;
				
			}
			
			foreach ($elements as $id=>$e){
				$htmlStr->insertTag($e['from'], $e[before].$e[annotation], $e['to']+1, "</an>" . implode($e[after]));				
			}
		}catch (Exception $ex){
			custom_exception_handler($ex);
		}
		
		//$content = $htmlStr->getContent();
		$content = custom_html_entity_decode($htmlStr->getContent());
		$content = preg_replace("/<#((↦)?[0-9]+)>/", '<sup class="rel">$1</sup>', $content);
		
		return Reformat::xmlToHtml($content);
	}
}

?>
