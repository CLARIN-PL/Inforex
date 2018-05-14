<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

// ToDo: Move common methods to an external file
require_once("{$config->path_engine}/page/page_ner.php");

/**
 */
class Ajax_ner_process extends CPagePublic {
	
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user, $corpus, $config;
		
		$annotations = array();
		$timestamp_start = time();	
		$text = stripslashes(strval($_POST['text']));
		$parts = explode("|", stripslashes(strval($_POST['wsdl'])));
		$wsdl = $parts[0];
		$model = $parts[1];
		$annotation_types = null;
		$api = null;
		
		foreach ($config->liner2_api as $m){
			if ($m['wsdl'] == $wsdl && $m['model'] == $model){
				if ( isset($m['annotations'] )){
					$annotation_types = $m['annotations'];
				}
				$api = $m; 
				break;
			}
		}
				
		$type_ignore = array();
		if (isset($api["type_ignore"])){
			foreach ($api["type_ignore"] as $type){
				$type_ignore[$type] = 1;
			}
		}
		$text = preg_replace('/(\p{L}|\p{N})$/m', '$1', $text);
		
		$liner2 = new WSLiner2($wsdl);
		$jsons = json_decode($liner2->chunk($text, "PLAIN:WCRFT", "JSON-ANNOTATIONS", $model), true);
		
		$htmlStr = new HtmlStr2($text);

		// Insert relations TODO add this information in JSON-ANNOTATIONS format and re-write
		/*$relation_tuple_pattern = "/\(([0-9]+),([0-9]+),(.*),\"(.*)\",([0-9]+),([0-9]+),(.*),\"(.*)\",(.*)\)/"; 
		if (preg_match_all($relation_tuple_pattern, $tuples, $matches, PREG_SET_ORDER)){
			foreach ($matches as $m){
				$an_from_start = intval($m[1]);
				$an_from_end = intval($m[2]);
				$an_to_start = intval($m[5]);
				$an_to_end = intval($m[6]);
				$rel = $m[9];
				try{
					$htmlStr->insertTag( $an_from_start, "", $an_from_end+1, "<sup class=\"rel\">↦$an_to_start</sup>", false);
					$htmlStr->insertTag( $an_to_start, "<sup class=\"rel\">$an_to_start</sup>", $an_to_end+1, "", false);
				}
				catch(Exception $ex){
					fb($ex);	
				}
			}
		}*/
				
		// Insert annotations 
		$has_metadata = false;
		foreach ($jsons as $item){
			$annotation_type = strtolower($item["type"]);
			if ( $annotation_types == null or in_array($annotation_type, $annotation_types)){
				$from = $item["from"];
				$to = $item["to"];
				$key = sprintf("%d_%d_%s", $from, $to, $annotation_type);
				$metadata = null;
				if (array_key_exists("metadata", $item)){
					$tag = sprintf("<span class='%s %s' title='%s:%s'>", strtolower($annotation_type), $key, strtolower($annotation_type), json_encode($item["metadata"]));
					$metadata = $item["metadata"];
					$has_metadata = true;
				}
				else 
					$tag = sprintf("<span class='%s %s' title='%s'>", strtolower($annotation_type), $key, strtolower($annotation_type));					
				try{
					$htmlStr->insertTag( $from, $tag, $to+1, "</span>");
				}
				catch(Exception $ex){
		
				}
				$annotations[$annotation_type][] = array("text"=>trim($item["text"], '"'), "key"=>$key, "metadata"=>$metadata);
			}
		}		
		fb($type_ignore);
		$timestamp_end = time();
		$duration_sec = $timestamp_end - $timestamp_start;
		$duration = (floor($duration_sec/60) ? floor($duration_sec/60) . " min(s), " : "") . $duration_sec%60 ." sec(s)"; 
		
		$html = $htmlStr->getContent();
		$html = str_replace("\n", "<br/>", $html);

		return array("html"=>$html, "annotations"=>$this->format_list_of_annotations_table($annotations, $has_metadata), "duration"=>$duration);
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
	
	/**
	 * Konwertuje listę anotacji do postaci tabelki
	 * @param unknown $annotations
	 * @return string
	 */
	function format_list_of_annotations_table($annotations, $has_metadata){
		$html = "<table class='table table-sm table-bordered' cellspacing='1'><tbody>";
		ksort($annotations);
		foreach ($annotations as $name=>$v){
			$name_lower = strtolower($name);
			
			if ( $has_metadata ){
				$html .= "<tr class='type bg-primary'><th colspan='2'>$name_lower</th></tr>";
			}
			else{
				$html .= "<tr class='type bg-primary'><th>$name_lower</th></tr>";
			}
			$annotation_group = "";
			foreach ($v as $an){
				$metadata = "";
				if ( $has_metadata ){
					$metadata = "<td>";
					if ( isset($an['metadata']) && is_array($an['metadata']) ){
						$metadata .= "<table class='tablesorter'>";
						foreach ($an['metadata'] as $key=>$val ){
							$metadata .= "<tr><th style='width: 30px'><b>$key</b>:</th><td>$val</td></tr>";
						}
						$metadata .= "</table>";
					}
					$metadata .= "</td>";
				}
				$html .= "<tr class='annotation $name_lower'><td><span class='$name_lower' key='${an['key']}'>${an['text']}</span></td>$metadata</tr>";
			}
		}
		$html .= "</tbody></table>";
		return $html;
	}
	
}
