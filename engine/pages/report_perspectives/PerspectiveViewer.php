<?php

class PerspectiveViewer extends CPerspective {
	
	function execute()
	{
		$content = $this->page->get("content_inline");
		$this->page->set("content_inline", "");
		
		$html = new HtmlStr2($content);
		$anns = DbAnnotation::getReportAnnotationsBySubsetId($this->document['id'], 10);
		$tag_no = 1;
		$replacements = array();
		foreach ( $anns as $ann ){
			$tag = "anonymize" . ($tag_no++);
			$replacements[] = array($tag, str_replace("lps_pn_", "", $ann['name']));			
			$html->insertTag($ann['from'], '<' . $tag . '>', $ann['to']+1, '</' . $tag . '>');
		}
		
		$content = $html->getContent(); 
		foreach ( $replacements as $rep ){
			$tag = $rep[0];
			$name = $rep[1];
			
			$content = preg_replace("/<$tag>.*?<\/$tag>/mu", "[" . $name . "]", $content);
		}
		
		$this->page->set("content_inline", $content);
	}
}
?>
