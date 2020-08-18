<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_report_set_tokens extends CAction{
		
	var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $corpus, $user;
		if ($_FILES["file"]["error"] > 0){
			$this->set("error","file upload error");
			return null;
		}

	  	$report_id = $_GET['id']; 
	  	$xcesFileName = $_FILES["xcesFile"]["tmp_name"];
	  	try {
	  		$takipiDoc = TakipiReader::createDocument($xcesFileName);
	  	}
	  	catch (Exception $e){
			$this->set("error","file upload error");
	  		return null;
	  	}
	  	$takipiText = "";
	  	
 	
	  	$tokensValues = "";
	  	foreach ($takipiDoc->getTokens() as $token){
	  		$takipiText = $takipiText . $token->orth;
	  	}
		$dbHtml = new HtmlStr(
					normalize_content(
						$this->getDb()->queryOne("SELECT content " .
										"FROM reports " .
										"WHERE id=$report_id")), 
					true);
		//$takipiText = html_entity_decode($takipiText, ENT_COMPAT, "UTF-8");
		$takipiText = custom_html_entity_decode($takipiText);
		//$dbText = preg_replace("/\n+|\r+|\s+/","",$dbHtml->getText(0, null));
		$dbText = preg_replace("/\n+|\r+|\s+/","",custom_html_entity_decode($dbHtml->getText(0, null)));
		if ($takipiText==$dbText){
	  		$takipiText = "";
	  		DbToken::deleteReportTokens($report_id);
		  	foreach ($takipiDoc->getTokens() as $token){
		  		//var_dump($token);				  		
		  		$from =  mb_strlen($takipiText);
		  		$takipiText = $takipiText . $token->orth;
		  		$to = mb_strlen($takipiText)-1;
		  		$this->getDb()->execute("INSERT INTO `tokens` (`report_id`, `from`, `to`) VALUES ($report_id, $from, $to)");
		  		$token_id = $this->getDb()->last_id();
		  		foreach ($token->lex as $lex){
		  			$base = addslashes(strval($lex->base));
		  			$ctag = addslashes(strval($lex->ctag));
		  			$cts = explode(":",$ctag);
		  			$pos = $cts[0];
		  			$disamb = $lex->disamb ? "true" : "false";
                    $this->getDb()->execute("INSERT IGNORE INTO `bases` (`text`) VALUES (\"$base\")");
                    $this->getDb()->execute("INSERT IGNORE INTO `tokens_tags_ctags` (`text`) VALUES (\"$base\")");
		  			$this->getDb()->execute("INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag_id`,`disamb`,`pos`) VALUES ($token_id, (SELECT id FROM bases WHERE text=\"$base\"), (SELECT id FROM tokens_tags_ctags WHERE ctag=\"$ctag\"), $disamb, $pos)");
		  		}
		  	}
		  	$this->set("message","Tokens successfully set");
	  	}
	  	else {
	  		$this->set("error","Wrong file format");		  		
	  	}
		  	
		  	
		return null;
	}
	
} 

?>
