<?php
class Page_ner extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2;
		mb_internal_encoding('UTF-8');

		$content = stripslashes($_REQUEST['content']);
		
		if ($content){

			// Create a stub of the web service 
			$client = new SoapClient(TAKIPI_WSDL); 

			// Send a request 
			$request = $client->Tag($content, "XML", false);			
			$token = $request->msg; 
			$status = $request->status;
		
			// Check whether the request was queued 
			if ( $status == 2 ){ 
			    // Check the request status until is 2 (queued) or 3 (in processing) 
			    do 
			    { 
			        sleep(1);  
			        $status = $client->GetStatus($token);
			    }while ( $status == 2 || $status == 3 ); 
			     
			    // If the status is 1 then fetch the result and print it 
			    if ( $status == 1 ){ 
			        $result = $client->GetResult($token);
			        //$json = array("tagged" => $this->align($result->msg, $id));		        
			        $client->DeleteRequest($token);
			    } 
			}

			$takipiDocument = TakipiDocument::createFromText("<doc>".$result->msg."</doc>");
	
			// Naprawa skrótów ps.
			$to_remove = array();
			foreach ($takipiDocument->sentenceEnds as $end){
				if ($end>0 && $takipiDocument->tokens[$end-1]->orth=="ps")
					$to_remove[] = $end; 
			}
			$takipiDocument->sentenceEnds = array_diff($takipiDocument->sentenceEnds, $to_remove);
	
			//preg_match_all('/<chunk type="s">(.*?)<\/chunk>/s', $result->msg, $matches, PREG_SET_ORDER);
			//$content_marked = "";
			$begin = 0;
			foreach ($takipiDocument->sentenceEnds as $end){
				$sentence = "";
				for ($i=$begin; $i<=$end; $i++) $sentence .= $takipiDocument->tokens[$i]->orth . " ";
				$begin = $end + 1;
				
				$sentence = trim($sentence);
				$path = PATH_ENGINE . "/ner";
				//$model = "gpw-person.model";
				$model = "gpw-person-company.model";
				$cmd = "LANG=en_US.utf-8; java -cp {$path}/lingpipe-3.8.2.jar:{$path}/neDemo.jar RunChunker {$path}/{$model} " . '"' . str_replace('"', '\"', $sentence) . '"';
				$result = exec($cmd);
				
				$matches = array();
				preg_match_all("/([0-9]+)-([0-9]+):(PERSON|COMPANY)/", $result, $matches, PREG_SET_ORDER);
				
				$result = $sentence;				
				foreach (array_reverse($matches) as $match){
					$annotation_text = trim(mb_substr($sentence, $match[1], $match[2]-$match[1]+1));
					$result = mb_substr($result, 0, $match[2])."</b>".mb_substr($result, $match[2]);
					if (ner_filter($annotation_text))
						$result = mb_substr($result, 0, $match[1])."<b style='background: yellow'>".mb_substr($result, $match[1]);
					else
						$result = mb_substr($result, 0, $match[1])."<b style='background: red'>".mb_substr($result, $match[1]);						
				}

				$content_marked .= $result ."<hr/>"; 				
				
			}
//			foreach ($matches as $match){
//				$sentence = $match[1];
//				$path = PATH_ENGINE . "/ner";
//				//$model = "gpw-person.model";
//				$model = "gpw2004.model";
//				$cmd = "LANG=en_US.utf-8; java -cp {$path}/lingpipe-3.8.2.jar:{$path}/neDemo.jar RunChunker {$path}/{$model} " . '"' . str_replace('"', '\"', $sentence) . '"';
//				$result = exec($cmd);
//				
//				$matches = array();
//				preg_match_all("/([0-9]+)-([0-9]+):PERSON/", $result, $matches, PREG_SET_ORDER);
//				
//				$result = $sentence;				
//				foreach (array_reverse($matches) as $match){
//					$result = mb_substr($result, 0, $match[2])."</b>".mb_substr($result, $match[2]);
//					$result = mb_substr($result, 0, $match[1])."<b style='background: yellow'>".mb_substr($result, $match[1]);
//				}
//
//				$content_marked .= $result; 				
//			}
		}
		$this->set('content', $content);
		$this->set('result', $content_marked);
		$this->set('matches', $matches);
	}
}


?>
