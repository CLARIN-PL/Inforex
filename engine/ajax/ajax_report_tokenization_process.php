<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

// ToDo: Move common methods to an external file
//  loaded earlier by composer classmap mechanism
//require_once(Config::Cfg()->get_path_engine()."/page/page_ner.php");

/**
 */
class Ajax_report_tokenization_process extends CPageCorpus {
	
	var $isSecure = false;
	
	function execute(){
		global $user, $corpus;
		$report_id = strval($_POST['report_id']);
		$report = $this->getDb()->fetch("SELECT *, f.format" .
				" FROM reports r" .
				" JOIN reports_formats f ON (r.format_id=f.id)" .
				" WHERE r.id=?",
				array($report_id));
		
		$tagger = new WSTagger("http://ws.clarin-pl.eu/wsg/tagger/index.php?wsdl");
		$content = $report['content'];
		
		$format = "plain";
		
		$content = strip_tags($content);
		$tagger->tag($content, $format);
		echo "<pre>".$tagger->tagged."</pre>";
	  	try {
	  		$takipiDoc = TakipiReader::createDocumentFromText("<doc>".$tagger->tagged."</doc>");
	  	}
	  	catch (Exception $e){
			throw new Exception("TakipiReader error. Exception: ".$e->getMessage());
	  	}		
		$this->getDb()->execute("UPDATE reports SET tokenization = 'none' WHERE id = ?", array($report_id));		  	
  		DbToken::deleteReportTokens($report_id);

  		$takipiText="";
  		$bases = "INSERT IGNORE INTO `bases` (`text`) VALUES ";
  		$tokensTags="INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag`,`disamb`) VALUES ";
  		foreach ($takipiDoc->sentences as $sentence){
  			$lastId = count($sentence->tokens)-1;
  			foreach ($sentence->tokens as $index=>$token){
		  		$from =  mb_strlen($takipiText);
		  		//$takipiText = $takipiText . html_entity_decode($token->orth);
		  		$takipiText = $takipiText . custom_html_entity_decode($token->orth);
		  		$to = mb_strlen($takipiText)-1;
				$lastToken = $index==$lastId ? 1 : 0;
		  		$this->getDb()->execute("INSERT INTO `tokens` (`report_id`, `from`, `to`,`eos`) VALUES (?, ?, ?, ?)", array($report_id, $from, $to, $lastToken));
		  		$token_id = $this->getDb()->last_id();
		  		foreach ($token->lex as $lex){
		  			$base = addslashes(strval($lex->base));
		  			$ctag = addslashes(strval($lex->ctag));
		  			$disamb = $lex->disamb ? "true" : "false";
		  			$bases .= "(\"$base\"),";
		  			$tokensTags .= "($token_id, (SELECT id FROM bases WHERE text=\"$base\"), \"$ctag\", $disamb),";
		  		}
  			}
  		}
	  	$this->getDb()->execute(substr($bases,0,-1));
	  	$this->getDb()->execute(substr($tokensTags,0,-1));
		$this->getDb()->execute("UPDATE reports SET tokenization = 'takipi' WHERE id = ?", array($report_id));		  	
	}
		
}
