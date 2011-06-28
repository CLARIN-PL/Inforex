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
		$tagger = new WSTagger($config->takipi_wsdl);
		$tagger->tag($text);
	  	try {
	  		$takipiDoc = TakipiReader::createDocumentFromText("<doc>".$tagger->tagged."</doc>");
	  	}
	  	catch (Exception $e){
			echo json_encode(array("error"=>"TakipiReader error", "exception"=>$e->getMessage()));
			return;
	  	}		
  		db_execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
  		$takipiText="";
  		$tokensTags="INSERT INTO `tokens_tags` (`token_id`,`base`,`ctag`,`disamb`) VALUES ";
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
	  			$tokensTags .= "($token_id, \"$base\", \"$ctag\", $disamb),";
	  		}
	  	}
	  	db_execute(substr($tokensTags,0,-1));
		$json = array( "success"=>1);
		echo json_encode($json);
	}
		
}
?>
