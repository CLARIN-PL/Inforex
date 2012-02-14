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
	function createFromReportAndTokens($report, $tokens, $tags){
		//$content = $report['content'];
		
		$content = html_entity_decode($report['content'], ENT_COMPAT, "UTF-8");
		$fileName = preg_replace("/[^\p{L}|\p{N}]+/u","_",$report['title']);
		$fileName .= (mb_substr($fileName, -1)=="_" ? "" : "_") . $report['id'] . ".xml";
		
		$ccl = new CclDocument();
		$ccl->setFileName($fileName);

		
		/* Match chunks */
		/*
		preg_match_all('/<chunk type="(.*?)"[^>]*>(.*?)<\/chunk>/us', $content, $chunkMatches, PREG_SET_ORDER);
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
				//$orth = html_entity_decode(strip_tags($orth),ENT_COMPAT, 'UTF-8');
				//$orth = htmlspecialchars($orth);
				$ns = !$htmlStr->isNoSpace();
				
				$t = new CclToken();
				$t->setOrth($orth);
				$t->setNs($ns);
				$t->setId($tokenIndex);
				$t->setFrom($token['from']);
				$t->setTo($token['to']);
				
				foreach ($tags[$token['token_id']] as $tag){
					$l = new CclLexeme();
					$l->setBase($tag['base']);
					$l->setCtag($tag['ctag']);
					$l->setDisamb($tag['disamb']);
					$t->addLexeme($l);
				}
				
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
			if (count($tokens)){
				if (!$token['eos'] )
					$c->addSentence($s);
				else 
					$sentenceIndex--;
			}
			$ccl->addChunk($c);
		}			
		
		return $ccl;
	}
	
	function setAnnotationsAndRelations($ccl, $annotations, $relations){
		$annotationsById = array();
		$continuousAnnotationIds = array();
		$continuousAnnotations = array();
		$continuousRelations = array();
		$normalRelations = array();
		foreach ($relations as &$relation){
			if ($relation['relation_type_id']==1){
				$continuousAnnotationIds[] = $relation['source_id'];
				$continuousAnnotationIds[] = $relation['target_id'];
				$continuousRelations[] = &$relation;
			}
			else
				$normalRelations[] = &$relation;
		}
		foreach ($annotations as &$annotation){
			if ( !in_array($annotation['id'], $continuousAnnotationIds)){
				$ccl->setAnnotation($annotation);
			}
			else
				$continuousAnnotations[$annotation['id']]=&$annotation;
			$annotationsById[$annotation['id']]=&$annotation;	
		}
		
		foreach ($continuousRelations as &$cRelation){
			$source_id = $cRelation['source_id'];
			$target_id = $cRelation['target_id'];
			if (array_key_exists($source_id, $annotationsById) && 
				array_key_exists($target_id, $annotationsById)){
				$ccl->setContinuousAnnotation(
					$continuousAnnotations[$source_id],
					$continuousAnnotations[$target_id]);
			}
			else if (array_key_exists($source_id, $annotationsById) || 
				array_key_exists($target_id, $annotationsById)) {
				$e = new CclError();
				$e->setClassName("CclFactory");
				$e->setFunctionName("setAnnotationsAndRelations");
				$e->addObject("relation", $cRelation);
				$e->addComment("008 no source or target annotation in continuous relation");
				$ccl->addError($e);					
			}
			//else no error
		}
		
		foreach ($normalRelations as &$nRelation){
			$source_id = $nRelation['source_id'];
			$target_id = $nRelation['target_id'];
			if (array_key_exists($source_id, $annotationsById) && 
				array_key_exists($target_id, $annotationsById)){			
				$ccl->setRelation(
					$annotationsById[$nRelation['source_id']],
					$annotationsById[$nRelation['target_id']],
					$nRelation);
			}
			else {
				//throw new Exception("Cannot set relation {$nRelation['id']}, no source and/or target!");
				//echo "Cannot set relation {$nRelation['id']}, no source and/or target!\n";
				$e = new CclError();
				$e->setClassName("CclFactory");
				$e->setFunctionName("setAnnotationsAndRelations");
				$e->addObject("relation", $nRelation);
				$e->addComment("009 no source or target annotation in normal relation");
				$ccl->addError($e);						
			}
		}
		
		
	}
	
}
?>
