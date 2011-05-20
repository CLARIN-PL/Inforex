<?
class WSTagger{
	
	var $wsdl = null;
	var $tagged = null;
	
	function __construct($wsdl){
		$this->wsdl = $wsdl;
	}
	
	function tag($text){
		global $config;

		// Create a stub of the web service 
		$client = new SoapClient($this->wsdl);

		// Send a request 
		$request = $client->Tag($text, "TXT", false);			
		$token = $request->msg; 
		$status = $request->status;
		
		$counter = 30;
	
		// Check whether the request was queued 
		if ( $status == 2 ){ 
		    // Check the request status until is 2 (queued) or 3 (in processing) 
		    do 
		    { 
		        sleep(1);  
		        $status = $client->GetStatus($token);
		    }while ( $status == 2 || $status == 3 || $counter--); 
		     
		    // If the status is 1 then fetch the result and print it 
		    if ( $status == 1 ){ 
		        $result = $client->GetResult($token);
		        //$json = array("tagged" => $this->align($result->msg, $id));		        
		        $client->DeleteRequest($token);
		    }
		    else{
		    	// TODO komunikat o problemie z otagowanie tekstu
		    	return false;
		    } 
		}
		
		$this->tagged = $result->msg;
		return true;		
	}
	
	/**
	 * 
	 */
	function getXml(){
		return $this->tagged;
	}
	
	/**
	 * 
	 */
	function getIOB(){
		$takipiDocument = TakipiReader::createDocumentFromText("<doc>".$this->tagged."</doc>");
		$sentences = array();
		foreach ($takipiDocument->sentences as $tokens){
			$sentence = array();
			foreach ($tokens->tokens as $token)
				$sentence[] = array(html_entity_decode($token->orth), $token->getDisamb()->base, $token->getDisamb()->ctag);
			$sentences[] = $sentence;
		}			
		return $sentences;			
	}
	
}
?>