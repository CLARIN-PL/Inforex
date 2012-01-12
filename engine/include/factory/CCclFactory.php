<?php
/*
 * Created on 2012-01-11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
/**
 * Tworzy dokument CclDocument na podstawie treści i tablicy tokenów.
 */
class CclFactory{
	
	/**
	 * $tokens --- tablica wartości from, to i eos
	 */
	function createFromPremorphAndTokens($content, $tokens){
		
		$ccl = new CclDocument();
		
		/* Match chunks */
		preg_match_all('/<chunk type="(.*?)">(.*)<\/chunk>/ums', $content, $chunkMatches, PREG_SET_ORDER);
		
		$from = 0;
		$to = 0;
		foreach ($chunkMatches as $parts){		
			$chunk = str_replace("<"," <",$parts[0]);
			$chunk = str_replace(">","> ",$chunk);
			$tmpStr = trim(preg_replace("/\s\s+/"," ",html_entity_decode(strip_tags($chunk),ENT_COMPAT, 'UTF-8')));
			$tmpStr2 = preg_replace("/\n+|\r+|\s+/","",$tmpStr);
			$to = $from + mb_strlen($tmpStr2)-1;
			$chunks[]=array(
				"notags" => $tmpStr,
				"nospace" => $tmpStr2,
				"from" => $from,
				"to" => $to,
				"type" => $parts[1]
			);
			$from = $to+1;		
		}	
		
		$htmlStr = new HtmlStr($content);
			
		// Podziel tokeny między chunkami
		$tokenIndex = 0;
		foreach ($chunks as $chunk){	

			$c = new CclChunk();
			$c->setType($chunk['type']);
			$s = new CclSentence();		

			while ( $tokenIndex < count($tokens) && (int)$tokens[$tokenIndex]["to"] < (int)$chunk["to"] ) {
				$token = $tokens[$tokenIndex];
				$orth = $htmlStr->getText($token['from'], $token['to']);
				$ns = !$htmlStr->isNoSpace();
				$s->addToken(new CclToken($orth, $ns));
				if ( $token['eos'] ){
					$c->addSentence($s);
					$s = new CclSentence();
				}
				$tokenIndex++;
			}
			$c->addSentence($s);
			$ccl->addChunk($c);
		}	
		
		
		return $ccl;
	}
	
}
?>
