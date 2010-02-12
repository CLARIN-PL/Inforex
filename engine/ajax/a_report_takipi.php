<?php
/**
 * Dodaje nową adnotację do bazy, generuje identyfikator adnotacji, 
 * wstawia go do raportu i zapisuje zaktualizowany raport do bazy.
 * 
 */
class Ajax_report_takipi{
	
	function execute(){
		global $mdb2;
		$content = strval($_POST['content']);
		$content = preg_replace('/<(\/)?[pP]>/s', ' ', $content);
	    $content = preg_replace('/<br(\/)?>/s', ' ', $content);
		$content_clean = trim(strip_tags($content));
		$id = intval($_POST['id']);

		//$content = stripslashes($content);
		$content_clean = stripcslashes($content_clean);

		// Location of the WSDL file 
		//$url = "http://localhost/clarin/ws/takipi/takipi_local.wsdl"; 
		$url = "http://plwordnet.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl"; 
		 
		// Create a stub of the web service 
		$client = new SoapClient($url); 
		 
		// Send a request 
		$request = $client->Tag($content_clean, "XML", false); 
		 
		$token = $request->msg; 
		$status = $request->status;
		$json = array(); 
		
		//$token = "286:3a23e80b6b86661277011fc30e705d1bc3cfa3bd3d0dc1c95be34be20b740a24";
		//$status = 2;
		
		/*
		global $conf_global_path;
		$content_tagged = file_get_contents($conf_global_path . "/include/sample_tagged_document.txt");
		$json = array("tagged" => $this->align($content_tagged, $id));
		$status = 1;
		*/ 
		
		// Check whether the request was queued 
		if ( $status == 2 ){ 
		    // Check the request status until is 2 (queued) or 3 (in processing) 
		    do 
		    { 
		        $status = $client->GetStatus($token);  
		    }while ( $status == 2 || $status == 3 ); 
		     
		    // If the status is 1 then fetch the result and print it 
		    if ( $status == 1 ){ 
		        $result = $client->GetResult($token);
		        //$json = array("tagged" => $this->align($result->msg, $id));		        
		        $json = array("tagged" => $this->takipi_to_html($result->msg)); 
		        $client->DeleteRequest($token);
		    } 
		} 
		
		echo json_encode($json);
	}
	
	function takipi_to_html($text){
        return preg_replace_callback('/<tok>(?:.*?)<orth>(.*?)<\/orth>(.*?)<\/tok>/s', "report_takipi_callback", $text);
	}
	
	/**
	 * 
	 */
	function align($content_tagged, $id){
		global $global_word_sequence, $mdb2;
		$global_word_sequence = array();
        
        $content = $mdb2->query("SELECT content FROM reports WHERE id=$id")->fetchOne();
        $aligner = new TextAligner($content);
        
        preg_replace_callback('/<tok>(?:.*?)<orth>(.*?)<\/orth>(.*?)<\/tok>/s', "report_takipi_callback", $content_tagged);
        
        $spans = array();
        $counter = 0;
        $spans_ann = array();
        for ($i = 0; $i<count($global_word_sequence); $i++){
        	$word = $global_word_sequence[$i]['word'];
        	$word = html_entity_decode($word);
        	$lex = $global_word_sequence[$i]['lex'];
        	
//        	if (!$aligner->align($word)){
//        		$spans[] = "<pre>".implode("\n", $aligner->logs)."</pre>";
//        	}
        	
        	if ($aligner->is_begin){
        		$spans_ann = array();        		
        	}

        	if ($aligner->annotation_name){
				if ($aligner->is_end_inside)
				{
					$word = mb_substr($word, 0, $aligner->inside_end_at)."<b style='color:red' title='Wykryto koniec adnotacji wewnątrz tokenu'>".mb_substr($word, $aligner->inside_end_at)."</b>";
				}

				$spans_ann[] = "<span class='w' label='".$lex."'>".$word."</span>";
				
				$counter--;
				
				if ($aligner->is_end || $aligner->is_end_inside){
					$spans[] = "<span class='ann ".$aligner->annotation_name."'>".implode(" ", $spans_ann)."</span>";
					$spans_ann = null;
				}        		
        	}
        	else
        		$spans[] = "<span class='w' label='".$lex."'>".$word."</span>";
        	        	        	
        }
        
        if ($spans_ann!=null){
			$spans[] = "<span class='ann ".$aligner->annotation_name."'>".implode(" ", $spans_ann)."</span>";        	
        }
        
		return implode(" ",$spans);		
	} 
	
}

// ----
$global_word_sequence = null;

function report_takipi_callback($matches){
	//global $global_word_sequence;
	$lex = preg_replace_callback('/<lex(.*?)><base>(.*?)<\/base><ctag>(.*?)<\/ctag><\/lex>/s', "report_takipi_lex_callback", $matches[2]);
	//$global_word_sequence[] = array("word" => $matches[1], "lex" => $lex);
	return "<span style='border-width: 1px' label='" . $lex . "'>" . $matches[1] . "</span>";
}

function report_takipi_lex_callback($matches){
	if ($matches[1])
		return "<div><b>".$matches[2]." ".$matches[3]."</b></div>";
	else
		return "<div>".$matches[2]." ".$matches[3]."</div>";
}
?>