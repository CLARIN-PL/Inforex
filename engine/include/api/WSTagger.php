<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class WSTagger{
	
	var $wsdl = null;
	var $tagged = null;
	
	function __construct($wsdl){
		$this->wsdl = $wsdl;
	}
	
	function tag($text, $format, $guesser=true){
		// Create a stub of the web service 
		$client = new SoapClient($this->wsdl);

		// Send a request 
		$request = $client->SendRequest($text, $format, $guesser);			
		$token = $request->token;		
		$timeout = 10;
		echo $token;
	
	    // Check the request status until is 2 (queued) or 3 (in processing) 
	    do 
	    { 
	        sleep(1);  
	        $status = $client->CheckStatus($token)->status;
	        print_r($status);
	    }while (($status == "QUEUE" || $status == 3) && $timeout--); 
	     
	    // If the status is 1 then fetch the result and print it 
	    if ( $status == 1 ){ 
	        $result = $client->GetResultResponse($token);
	        //$json = array("tagged" => $this->align($result->msg, $id));		        
	        $client->DeleteRequest($token);
	    }
	    else{
	    	// TODO komunikat o problemie z otagowanie tekstu
	    	return false;
	    } 
		
		print_r($result);
		$this->tagged = $result->xml;
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
				$sentence[] = array(custom_html_entity_decode($token->orth), $token->getDisamb()->base, $token->getDisamb()->ctag);
			//$sentence[] = array(html_entity_decode($token->orth), $token->getDisamb()->base, $token->getDisamb()->ctag);
			$sentences[] = $sentence;
		}			
		return $sentences;			
	}
	
}
?>