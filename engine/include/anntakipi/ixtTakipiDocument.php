<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-01-13
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
require_once("ixtTakipiReader.php"); 
 
class TakipiDocument{
		
	var $tokens = array();
	// Index of tokens which end sentences.
	var $sentenceEnds = array();
	
	function __construct(){
	}	

	static function createFromFile($file){
		$d = new TakipiDocument();
		$r = new TakipiReader();
		$r->loadFile($file);
		while ($r->nextSentence()){
			while ($t = $r->readToken())
				$d->tokens[] = $t;
			$d->sentenceEnds[] = count($d->tokens)-1;
		}
		return $d;
	}	
	
	static function createFromText($content){
		$d = new TakipiDocument();
		$r = new TakipiReader();
		$r->loadText($content);
		while ($r->nextSentence()){
			while ($t = $r->readToken())
				$d->tokens[] = $t;
			$d->sentenceEnds[] = count($d->tokens)-1;
		}
		return $d;
	}
}
?>
