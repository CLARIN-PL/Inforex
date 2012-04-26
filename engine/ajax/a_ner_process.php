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
		
		$annotations = array();
		$timestamp_start = time();	
		$text = stripslashes(strval($_POST['text']));
		
		$liner2 = new WSLiner2("http://nlp1.synat.pcss.pl/nerws/nerws.wsdl");
		$tuples = $liner2->chunk($text, "PLAIN", "TUPLES");
		
		$htmlStr = new HtmlStr($text, true);
				
		if (preg_match_all("/\((.*),(.*),(.*)\)/", $tuples, $matches, PREG_SET_ORDER)){
			foreach ($matches as $m){
				list($from, $to) = split(',', $m[1]);
				$tag = sprintf("<span class='%s' title='%s'>", strtolower($m[2]), strtolower($m[2]));
				$htmlStr->insertTag( $from, $tag, $to+1, "</span>");
				$annotations[$m[2]][] = trim($m[3], '"');
			}
		}
						
		$timestamp_end = time();
		$duration_sec = $timestamp_end - $timestamp_start;
		$duration = (floor($duration_sec/60) ? floor($duration_sec/60) . " min(s), " : "") . $duration_sec%60 ." sec(s)"; 
		
		$html = $htmlStr->getContent();
		$html = str_replace("\n", "<br/>", $html);
		$json = array( "success"=>1, "html"=>$html, "annotations"=>$this->format_list_of_annotations($annotations), "duration"=>$duration);
				
		echo json_encode($json);
	}
		
	/**
	 * 
	 */
	function format_list_of_annotations($annotations){
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
		return $annotations_html;		
	} 
}
?>
