<?php

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
		
		$this->page->set('content_inline', Reformat::xmlToHtml($content));		
		$this->page->set('relations', $rows);
	}
	
		/**
	 * 
	 */
	function load_document_content($relations){

		$index = array();
		$next_id = 1;

		try{
			$htmlStr = new HtmlStr(html_entity_decode($this->document['content'], ENT_COMPAT, "UTF-8"));
			foreach ($relations as $ann){
			
				if ( !isset($index[$ann[target_id]]) ){
					$element_id = $next_id++;
					$index[$ann[target_id]]	= $element_id;
					$sup = "<#$element_id>";
					$htmlStr->insertTag($ann['ant_from'], sprintf("$sup<an#%d:%s>", $ann['target_id'], $ann['ant_type']), $ann['ant_to']+1, "</an>");
				}
			}
					
			foreach ($relations as $ann){
				$element_id = $index[$ann[target_id]];
				$sup = "<#↦$element_id>";
				if ( !isset($index[$ann[source_id]]) ){
					$htmlStr->insertTag($ann['ans_from'], sprintf("<an#%d:%s>", $ann['source_id'], $ann['ans_type']), $ann['ans_to']+1, "</an>$sup");
				}
				else{
					$htmlStr->insert($ann['ans_to']+1, $sup);					
				}
			}
		}catch (Exception $ex){
			custom_exception_handler($ex);
		}
		
		$content = $htmlStr->getContent();
		$content = preg_replace("/<#((↦)?[0-9]+)>/", '<sup class="rel">$1</sup>', $content);
		
		return Reformat::xmlToHtml($content);
	}
}

?>
