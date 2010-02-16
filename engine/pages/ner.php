<?php
class Page_ner extends CPage{
	
	function execute(){		
		global $mdb2;
		mb_internal_encoding('UTF-8');

		$content = stripslashes($_POST['content']);

		if ($_POST['process']){

			// Create a stub of the web service 
			$client = new SoapClient(TAKIPI_WSDL); 

			// Send a request 
			$request = $client->SplitIntoSentences($content, "XML", false);			
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

			preg_match_all('/<chunk type="s">(.*?)<\/chunk>/s', $result->msg, $matches, PREG_SET_ORDER);
			$content_marked = "";
			foreach ($matches as $match){
				$sentence = $match[1];
				$path = PATH_ENGINE . "/ner";
				$cmd = "LANG=en_US.utf-8; java -cp {$path}/lingpipe-3.8.2.jar:{$path}/neDemo.jar RunChunker {$path}/gpw-person.model " . '"' . str_replace('"', '\"', $sentence) . '"';
				$result = exec($cmd);
				
				$matches = array();
				preg_match_all("/([0-9]+)-([0-9]+):PERSON/", $result, $matches, PREG_SET_ORDER);
				
				$result = $sentence;				
				foreach (array_reverse($matches) as $match){
					$result = mb_substr($result, 0, $match[2])."</b>".mb_substr($result, $match[2]);
					$result = mb_substr($result, 0, $match[1])."<b style='background: yellow'>".mb_substr($result, $match[1]);
				}

				$content_marked .= $result; 				
			}
		}
		$this->set('content', $content);
		$this->set('result', $content_marked);
		$this->set('matches', $matches);
	}
}


?>
