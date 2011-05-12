<?php

require_once("{$config->path_engine}/pages/ner.php");

/**
 */
class Ajax_report_tokenization_process extends CPage {
	
	var $isSecure = false;
	
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user, $corpus, $config;
	
		$text = stripslashes(strval($_POST['text']));
		// Location of the WSDL file 
		$url = "http://nlp.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl"; 
		// Create a stub of the web service 
		$client = new SoapClient($config->takipi_wsdl); 
		// Send a request 
		$request = $client->Tag($text, "TXT", true); 
		$token = $request->msg; 
		$status = $request->status; 
		// Check whether the request was queued 
		if ( $request->status == 2 ){ 
		    // Check the request status until is 2 (queued) or 3 (in processing) 
		    do { 
		        $status = $client->GetStatus($token); 
		    }while ( $status == 2 || $status == 3 ); 
		    // If the status is 1 then fetch the result and print it 
		    if ( $status == 1 ){ 
		        $result = $client->GetResult($token); 
		        //echo $result->msg; 
		    } 
		} 		
		$json = array( "success"=>1, "result"=>$result->msg);
		/*$tagger = new WSTagger($config->takipi_wsdl);
		$tagger->tag($text);
		$iob = $tagger->getIOB();

		//$chunker = new Liner($config->path_liner, $config->path_liner."/models/" . $models[$model]);
		$chunker = new Liner($config->path_liner, $config->path_liner."/models/" . $models[$model]['file']);
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
				*/
		echo json_encode($json);
	}
		
}
?>
