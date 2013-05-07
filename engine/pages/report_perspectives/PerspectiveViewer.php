<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveViewer extends CPerspective {
	
	function execute()
	{
		$content = $this->page->get("content_inline");
		$this->page->set("content_inline", "");
		
		$html = new HtmlStr2($content);
		$anns = DbAnnotation::getReportAnnotationsBySubsetId($this->document['id'], 10);
		$tag_no = 1;
		$replacements = array();
		$exceptions = array();
		
		foreach ( $anns as $ann ){
			$tag = "anonymize" . ($tag_no++);
			$replacements[] = array($tag, str_replace("lps_pn_", "", $ann['name']));	
			try{		
				$html->insertTag($ann['from'], '<' . $tag . '>', $ann['to']+1, '</' . $tag . '>');
			}
			catch(Exception $ex){
				$exceptions[] = $ex->getMessage();
			}
		}
		
		$content = $html->getContent();
		$content_html = $content; 
		foreach ( $replacements as $rep ){
			$tag = $rep[0];
			$name = $rep[1];
			
			$content_html = preg_replace("/<$tag>.*?<\/$tag>/mu", "<span style='color: #aaa'>[" . $name . "]</span>", $content_html);
			$content = preg_replace("/<$tag>.*?<\/$tag>/mu", "[" . $name . "]", $content);
		}
		
		$this->page->set("exceptions", $exceptions);
		$this->page->set("content_source", $content);
		$this->page->set("content_html", $content_html);
	}
}
?>
