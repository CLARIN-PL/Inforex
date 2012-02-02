<?php
/*
 * Created on 2012-01-11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
/**
 * Tworzy dokument CclDocument na podstawie trescci i tablicy tokenow.
 */
class CclFactory{
	
	/**
	 * $tokens --- tablica wartosci from, to i eos
	 */
	function createFromReportAndTokens($report, $tokens){
		$content = $report['content'];
		$fileName = preg_replace("/[^\p{L}|\p{N}]+/u","_",$report['title']);
		$fileName .= (mb_substr($fileName, -1)=="_" ? "" : "_") . $report['id'] . ".xml";
		
		$ccl = new CclDocument();
		$ccl->setFileName($fileName);

		
		/* Match chunks */
		/*
		preg_match_all('/<chunk type="(.*?)">(.*)<\/chunk>/us', $content, $chunkMatches, PREG_SET_ORDER);
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
			
		// Podziel tokeny miedzy chunkami
		$tokenIndex = 0;
		$sentenceIndex = 0;
		foreach ($chunks as $chunk){	
			$c = new CclChunk();
			$c->setType($chunk['type']);
			$s = new CclSentence();		
			$s->setId($sentenceIndex);
			$sentenceIndex++;
			while ( $tokenIndex < count($tokens) && (int)$tokens[$tokenIndex]["to"] < (int)$chunk["to"] ) {
				$token = $tokens[$tokenIndex];
				$orth = $htmlStr->getText($token['from'], $token['to']);
				$ns = !$htmlStr->isNoSpace();
				$t = new CclToken();
				$t->setOrth($orth);
				$t->setNs($ns);
				$t->setId($tokenIndex);
				$s->addToken($t);
				if ( $token['eos'] ){
					$c->addSentence($s);
					$s = new CclSentence();
					$s->setId($sentenceIndex);
					$sentenceIndex++;
				}
				$tokenIndex++;
			}
			$c->addSentence($s);			
			$ccl->addChunk($c);
			
		}	*/
		
		
		
		//preg_match_all('/<chunk type="(.*?)">(.*)<\/chunk>/us', $content, $chunkMatches, PREG_SET_ORDER);
		$chunkList = explode('</chunk>', $report['content']);
		array_pop($chunkList);
		$from = 0;
		$to = 0;
		foreach ($chunkList as $parts){		
			$chunk = str_replace("<"," <",$parts);
			$chunk = str_replace(">","> ",$chunk);
			$tmpStr = trim(preg_replace("/\s\s+/"," ",html_entity_decode(strip_tags($chunk),ENT_COMPAT, 'UTF-8')));
			$tmpStr2 = preg_replace("/\n+|\r+|\s+/","",$tmpStr);
			$to = $from + mb_strlen($tmpStr2)-1;
			$chunks[]=array(
				"notags" => $tmpStr,
				"nospace" => $tmpStr2,
				"from" => $from,
				"to" => $to,
			);
			$from = $to+1;		
		}	
		
		$htmlStr = new HtmlStr($content);
			
		// Podziel tokeny miedzy chunkami
		$tokenIndex = 0;
		$sentenceIndex = 0;
		$chunkIndex = 0;
		foreach ($chunks as $chunk){	
			$c = new CclChunk();
			$c->setId($chunkIndex);
			$chunkIndex++;
			$s = new CclSentence();		
			$s->setId($sentenceIndex);
			$sentenceIndex++;
			while ( $tokenIndex < count($tokens) && (int)$tokens[$tokenIndex]["to"] <= (int)$chunk["to"] ) {
				$token = $tokens[$tokenIndex];
				$orth = $htmlStr->getText($token['from'], $token['to']);
				$ns = !$htmlStr->isNoSpace();
				
				$t = new CclToken();
				$t->setOrth($orth);
				$t->setNs($ns);
				$t->setId($tokenIndex);
				$t->setFrom($token['from']);
				$t->setTo($token['to']);
				
				$s->addToken($t);
				$ccl->addToken($t);
				if ( $token['eos'] ){
					$c->addSentence($s);
					$s = new CclSentence();
					$s->setId($sentenceIndex);
					$sentenceIndex++;
				}
				$tokenIndex++;
			}
			if ( !$token['eos'] )
				$c->addSentence($s);
			else 
				$sentenceIndex--;
			$ccl->addChunk($c);
		}			
		
		return $ccl;
	}
	
}
?>
