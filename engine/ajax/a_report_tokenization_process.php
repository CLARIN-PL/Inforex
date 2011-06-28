<?php

require_once("{$config->path_engine}/pages/ner.php");

/**
 */
class Ajax_report_tokenization_process extends CPage {
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2, $user, $corpus, $config;
		$report_id = strval($_POST['report_id']);
		$text = strip_tags(db_fetch_one("SELECT content FROM reports WHERE id=?",array($report_id)));
		// Create a stub of the web service 
		$client = new SoapClient($config->takipi_wsdl); 
		// Send a request 
		$request = $client->Tag($text, "TXT", true); 
		$token = $request->msg; 
		$status = $request->status; 
		$counter = 30;
		// Check whether the request was queued 
		if ( $request->status == 2 ){ 
		    // Check the request status until is 2 (queued) or 3 (in processing) 
		    do { 
		    	sleep(1);
		        $status = $client->GetStatus($token); 
		    }while ( ($status == 2 || $status == 3) && $counter--); 
		    // If the status is 1 then fetch the result and print it 
		    if ( $status == 1 ){ 
		        $result = $client->GetResult($token); 
		        $resultMsg = $result->msg;
		        $takipiDoc = null;
			  	try {
			  		$takipiDoc = TakipiReader::createDocumentFromText("<doc>$resultMsg</doc>");
			  	}
			  	catch (Exception $e){
					echo json_encode(array("error"=>"TakipiReader error", "exception"=>$e->getMessage()));
					return;
			  	}
		  		db_execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
		  		$takipiText="";
			  	foreach ($takipiDoc->getTokens() as $token){
			  		$from =  mb_strlen($takipiText);
			  		$takipiText = $takipiText . $token->orth;
			  		$to = mb_strlen($takipiText)-1;
			  		db_execute("INSERT INTO `tokens` (`report_id`, `from`, `to`) VALUES (?, ?, ?)", array($report_id, $from, $to));
			  		$token_id = $mdb2->lastInsertID();
			  		foreach ($token->lex as $lex){
			  			$base = addslashes(strval($lex->base));
			  			$ctag = addslashes(strval($lex->ctag));
			  			$disamb = $lex->disamb ? "true" : "false";
			  			db_execute("INSERT INTO `tokens_tags` (`token_id`,`base`,`ctag`,`disamb`) VALUES (?, \"?\", \"?\", ?)", array($token_id, $base, $ctag, $disamb));
			  		}
			  	}
		    }
		    else {
				$json = array( "error"=>"Takipi-WS error");
				echo json_encode($json);
				return;
		    } 
		} 		
		$json = array( "success"=>1);
		echo json_encode($json);
	}
		
}
?>
