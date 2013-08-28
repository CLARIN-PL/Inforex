<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
require_once("{$config->path_engine}/pages/ner.php");

/**
 */
class Ajax_report_tokenization_process extends CPage {
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2, $user, $corpus, $config;
		$report_id = strval($_POST['report_id']);
		$text = db_fetch_one("SELECT content FROM reports WHERE id=?",array($report_id));
		$text = str_replace("<"," <",$text);
		$text = str_replace(">","> ",$text);
		$tagger = new WSTagger($config->takipi_wsdl);
		if (substr($text, 0, 5) != "<?xml"){
			$text = strip_tags($text);
			//$text = html_entity_decode($text);
			$text = custom_html_entity_decode($text);
			$tagger->tag($text);
		  	try {
		  		$takipiDoc = TakipiReader::createDocumentFromText("<doc>".$tagger->tagged."</doc>");
		  	}
		  	catch (Exception $e){
				throw new Exception("TakipiReader error. Exception: ".$e->getMessage());
		  	}		
			db_execute("UPDATE reports SET tokenization = 'none' WHERE id = ?", array($report_id));		  	
	  		db_execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
	  		$takipiText="";
	  		$tokensTags="INSERT INTO `tokens_tags` (`token_id`,`base`,`ctag`,`disamb`) VALUES ";
	  		foreach ($takipiDoc->sentences as $sentence){
	  			$lastId = count($sentence->tokens)-1;
	  			foreach ($sentence->tokens as $index=>$token){
			  		$from =  mb_strlen($takipiText);
			  		//$takipiText = $takipiText . html_entity_decode($token->orth);
			  		$takipiText = $takipiText . custom_html_entity_decode($token->orth);
			  		$to = mb_strlen($takipiText)-1;
					$lastToken = $index==$lastId ? 1 : 0;
			  		db_execute("INSERT INTO `tokens` (`report_id`, `from`, `to`,`eos`) VALUES (?, ?, ?, ?)", array($report_id, $from, $to, $lastToken));
			  		$token_id = $mdb2->lastInsertID();
			  		foreach ($token->lex as $lex){
			  			$base = addslashes(strval($lex->base));
			  			$ctag = addslashes(strval($lex->ctag));
			  			$disamb = $lex->disamb ? "true" : "false";
			  			$tokensTags .= "($token_id, \"$base\", \"$ctag\", $disamb),";
			  		}
	  			}
	  		}
		  	db_execute(substr($tokensTags,0,-1));
			db_execute("UPDATE reports SET tokenization = 'takipi' WHERE id = ?", array($report_id));		  	
		}
		else {
			db_execute("UPDATE reports SET tokenization = 'none' WHERE id = ?", array($report_id));		  	
	  		db_execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
	  		$takipiText="";
	  		$tokensTags="INSERT INTO `tokens_tags` (`token_id`,`base`,`ctag`,`disamb`) VALUES ";
			$reader = new XMLReader();
			$reader->xml($text);
			do {
				$read = $reader->read();
				if ($reader->localName == "chunk" && $reader->nodeType == XMLReader::ELEMENT){
					$text = $reader->readString();	
					$text = strip_tags($text);
					//$text = html_entity_decode($text);
					$text = custom_html_entity_decode($text);
					$tagger->tag($text);
				  	try {
				  		$takipiDoc = TakipiReader::createDocumentFromText("<doc>".$tagger->tagged."</doc>");
				  	}
				  	catch (Exception $e){
						throw new Exception("TakipiReader error. Exception: ".$e->getMessage());
				  	}		
			  		foreach ($takipiDoc->sentences as $sentence){
	  					$lastId = count($sentence->tokens)-1;
			  			foreach ($sentence->tokens as $index=>$token){
					  		$from =  mb_strlen($takipiText);
					  		//$takipiText = $takipiText . html_entity_decode($token->orth);
					  		$takipiText = $takipiText . custom_html_entity_decode($token->orth);
					  		$to = mb_strlen($takipiText)-1;
					  		$lastToken = $index==$lastId ? 1 : 0;
					  		db_execute("INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`) VALUES (?, ?, ?, ?)", array($report_id, $from, $to, $lastToken));
					  		$token_id = $mdb2->lastInsertID();
					  		foreach ($token->lex as $lex){
					  			$base = addslashes(strval($lex->base));
					  			$ctag = addslashes(strval($lex->ctag));
					  			$disamb = $lex->disamb ? "true" : "false";
					  			$tokensTags .= "($token_id, \"$base\", \"$ctag\", $disamb),";
					  		}
			  			}
			  		}
									
				}				
			}
			while ( $read );
			db_execute(substr($tokensTags,0,-1));
			db_execute("UPDATE reports SET tokenization = 'takipi' WHERE id = ?", array($report_id));		  				
		}
		
		return;
	}
	
	
		
}
?>
