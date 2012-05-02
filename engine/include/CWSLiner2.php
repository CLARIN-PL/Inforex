<?
class WSLiner2{
	
	var $wsdl = null;
	var $tagged = null;
	
	function __construct($wsdl){
		$this->wsdl = $wsdl;
	}
	
	function chunk($text, $input_format, $output_format){
		$client = new SoapClient("http://nlp1.synat.pcss.pl/nerws/nerws.wsdl");
		$row = $client->Annotate("PLAIN", "TUPLES", $text);
		$counter = 1000;
		
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