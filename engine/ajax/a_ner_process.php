<?php

require_once("{$config->path_engine}/pages/ner.php");

/**
 */
class Ajax_ner_process extends CPage {
	
	var $isSecure = false;
	
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user, $corpus, $config;
		
		$timestamp_start = time();
	
		$text = stripslashes(strval($_POST['text']));
		$model = strval($_POST['model']);
		
		$models = Page_ner::getModels();

		$tagger = new WSTagger($config->takipi_wsdl);
		$tagger->tag($text);
		$sentences = $tagger->getIOB();

		$chunker = new Liner($config->path_liner, $config->path_liner."/models/" . $models[$model]['file']);

		$htmlStr = new HtmlStr($text, true);
		$offset = 0;
		$annotations = array();
		
		$chunker->chunkSentences($sentences);
		
		$annotations = array();
		$chunkings = $chunker->getChunkingChars();
		$i = 0;
		// Zdanie po zdaniu
		foreach ($chunkings as $chunking){
			// Treść zdania
			$text = $chunker->cseq[$i];
			
			foreach ($chunking as $c){
				$htmlStr->insertTag($offset + $c[0], sprintf("<span class='%s' title='%s'>", strtolower($c[2]), strtolower($c[2])), $offset + $c[1]+1, "</span>");
				$annotations[$c[2]][] = $htmlStr->getText($offset + $c[0], $offset + $c[1]);
			}
				
			foreach ($sentences[$i] as $token)
				$offset += mb_strlen($token[0]);
				
			$i++;			
		}
		
		$annotations_html = "";
		ksort($annotations);
		foreach ($annotations as $name=>$v){
			$annotation_group = "";
			foreach ($v as $an){
				$name_lower = strtolower($name);
				$annotation_group .= "<li><span class='$name_lower'>$an</span></li>";
			}
			$annotations_html .= "<li><b>$name</b><ul>$annotation_group</ul></li>";
		}
		$annotations_html = "<ul>$annotations_html</ul>";
		
		$timestamp_end = time();
		$duration_sec = $timestamp_end - $timestamp_start;
		$duration = (floor($duration_sec/60) ? floor($duration_sec/60) . " min(s), " : "") . $duration_sec%60 ." sec(s)"; 
		
		$html = $htmlStr->getContent();
		$html = str_replace("\n", "<br/>", $html);
		$json = array( "success"=>1, "html"=>$html, "annotations"=>$annotations_html, "duration"=>$duration);
				
		echo json_encode($json);
	}
		
}
?>
