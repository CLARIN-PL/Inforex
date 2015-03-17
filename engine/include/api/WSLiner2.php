<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class WSLiner2{
	
	var $wsdl = null;
	var $tagged = null;
	
	function __construct($wsdl){
		$this->wsdl = $wsdl;
	}
	
	function chunk($text, $input_format, $output_format, $model=""){
		$client = new SoapClient($this->wsdl, array('cache_wsdl' => WSDL_CACHE_NONE));
		$row = $client->Annotate($input_format, $output_format, $model, $text);
		$counter = 20;
		
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
		    
		    if ( $status != 3){
		    	throw new Exception("TIMEOUT");
		    } 		     
		}
		return $result->msg;
	}
	
}
?>
