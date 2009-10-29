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
		$content = strip_tags($content);		

		// Location of the WSDL file 
		$url = "http://plwordnet.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl"; 
		 
		// Create a stub of the web service 
		$client = new SoapClient($url); 
		 
		// Send a request 
		$request = $client->Tag($content, "XML", false); 
		 
		$token = $request->msg; 
		$status = $request->status;
		$json = array(); 
		
		//$token = "286:3a23e80b6b86661277011fc30e705d1bc3cfa3bd3d0dc1c95be34be20b740a24";
		//$status = 2;
		 
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
		        
		        $content = $result->msg;
		        //$content = preg_replace_callback("/<tok>.*?<orth>(.*)<\/orth>.*?<\/tok>/", "report_takipi_callback", $content);
		        $content = preg_replace_callback('/<tok>(?:.*?)<orth>(.*?)<\/orth>(.*?)<\/tok>/s', "report_takipi_callback", $content);
		         
		        $json = array("tagged" => $content); 
		    } 
		} 
		
		echo json_encode($json);
	}
	
}

function report_takipi_callback($matches){
	$lex = preg_replace_callback('/<lex(.*?)><base>(.*?)<\/base><ctag>(.*?)<\/ctag><\/lex>/s', "report_takipi_lex_callback", $matches[2]);
	return "<span style='border-width: 1px' label='" . $lex . "'>" . $matches[1] . "</span>";
}

function report_takipi_lex_callback($matches){
	if ($matches[1])
		return "<div><b>".$matches[2]." ".$matches[3]."</b></div>";
	else
		return "<div>".$matches[2]." ".$matches[3]."</div>";
}

?>
