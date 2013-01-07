<?
/**
 * Klasa obsługuje funkcjonalność NER-WS
 */
class WSLiner2{
	
	var $wsdl = null;
	var $tagged = null;
	
	function __construct($wsdl){
		$this->wsdl = $wsdl;
	}
	
	function chunk($text, $input_format="PLAIN", $output_format="TUPLES"){
		$client = new SoapClient($this->wsdl);
		$row = $client->Annotate($input_format, $output_format, $text);
		$counter = 120;
		
		$token = $row->msg;
		$status = $row->status;
		
		// Check whether the request was queued 
		if ( $status == 1 ){ 
		    // Check the request status until is 2 (queued) or 3 (in processing) 
		    do 
		    { 
		        sleep(1);		         
		        $result = $client->GetResult($token);
		        $status = $result->status;
		    }while (($status == 1 || $status == 2) && $counter--);
		    
		    if ( $status != 3)
		    	return false; 		     
		}
		return $result->msg;
	}
	
}
?>