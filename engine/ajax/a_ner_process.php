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
	
		$text = stripslashes(strval($_POST['text']));
		$model = strval($_POST['model']);
		
		$models = array();
		$models[1] = "crf_model_gpw-all-nam_orth-base-ctag.bin";
		$models[2] = "crf_model_gpw-wiki-police-infi_orth-base-ctag_w-3-2_5nam.bin";
		$models[3] = "crf_model_gpw-wiki-police-infi_orth-base-ctag_w-1-1_5nam.bin";

		$tagger = new WSTagger($config->takipi_wsdl);
		$tagger->tag($text);
		$iob = $tagger->getIOB();

		$chunker = new Liner($config->path_liner, $config->path_liner."/models/" . $models[$model]);
		//$chunker = new Liner($config->path_liner, $config->path_liner."/models/crf_model_gpw-wiki-police-infi_orth-base-ctag_5nam.bin");

		$htmlStr = new HtmlStr($text, true);
		$offset = 0;
		$annotations = array();
		
		foreach ($iob as $tokens){
		
			$chunker->chunk($tokens);
			//$chunking_global = $chunker->getChunking();
			$chunking_chars = $chunker->getChunkingChars();
		
			foreach ($chunking_chars as $c){
				try{
					$htmlStr->insertTag($offset + $c[0], sprintf("<span class='%s' title='%s'>", strtolower($c[2]), strtolower($c[2])), $offset + $c[1]+1, "</span>");
					$annotations[$c[2]][] = $htmlStr->getText($offset + $c[0], $offset + $c[1]);
					//$struct .= sprintf("<tr><td>%s</td><td>%s</td></tr>", $c[2], $htmlStr->getText($offset + $c[0], $offset + $c[1]));
				}
				catch(Exception $ex){}
			}
			
			foreach ($tokens as $t)
				$offset += mb_strlen($t[0]);
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
		
		//$struct .= "</tbody></table>";
		$html = $htmlStr->getContent();
		$html = str_replace("\n", "<br/>", $html);
		$json = array( "success"=>1, "html"=>$html, "annotations"=>$annotations_html );
				
		echo json_encode($json);
	}
		
}
?>
