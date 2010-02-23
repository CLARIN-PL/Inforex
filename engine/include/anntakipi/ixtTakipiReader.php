<?php
/**
 * TakipiReader is a class used to read the result of tagging with TaKIPI.
 * 
 * @author Michał Marcińczuk <michal.marcinczuk@pwr.wroc.pl>
 * 
 */

/**
 * Class represents single token.
 */
class TakipiToken{
	var $orth = null;
	var $lex = array();
	
	function __construct($orth){
		$this->orth = $orth;
	}
	
	function addLex($base, $ctag, $disamb){
		$this->lex[] = new TakipiLex($base, $ctag, $disamb);
	}
	
	function getDisamb(){
		foreach ($this->lex as $lex)
			if ($lex->disamb)
				return $lex;
		return null;
	}
}

/**
 * Class represents a single morphology interpretation.
 */
class TakipiLex{
	var $disamb = false;
	var $base = null;
	var $ctag = null;
	
	function __construct($base, $ctag, $disamb){
		$this->base = $base;
		$this->ctag = $ctag;
		$this->disamb = $disamb;
	}
	
	function getPos(){
		$p = strpos($this->ctag, ":");
		if ($p===false)
			return $this->ctag;
		else
			return substr($this->ctag, 0, $p);
	}
}

/**
 * Iterative TaKIPI file reader.
 * 
 * @example
 * $r = new TakipiReader();
 * $r->loadText($tagged_text);
 * while ($r->nextSentence()){
 *	 while ($t = $r->readToken()){
 *     echo $t->orth . "\n";
 *   }
 * }
 */
class TakipiReader{
	
	var $reader = null;
	var $isNewSentence = false;
	var $readerSentence = null;
	
	function __construct(){
		$this->reader = new XMLReader();
		$this->readerSentence = new XMLReader();		
	}
	
	function loadFile($file){
		$xml = file_get_contents($file);
		$xml = "<doc>$xml</doc>";
		$this->reader->xml($xml);
		// Read the top node.
		$this->reader->read(); 			
	}
	
	function loadText($text){
		$this->reader->xml($text);
		// Read the top node.
		$this->reader->read(); 					
	}

	/**
	 * Move the reader to a next sentence.
	 */
	function nextSentence(){
		// Move to a first CHUNK
		if ($this->reader->localName == "doc"){
			do {
				$read = $this->reader->read();
			}while ($read && $this->reader->localName != "chunk");
			
			if (!$read)
				throw new Exception("CHUNK node not found!");
				
			$this->readerSentence->xml($this->reader->readOuterXML());
			$this->readerSentence->read();
			return true;					
		}
		else{
			if ($this->reader->next("chunk")){
				$this->readerSentence->xml($this->reader->readOuterXML());
				$this->readerSentence->read();
				return true;			
			}else{
				return false;
			}
		}
	}
	
	function readToken(){
		$read = true;
		
		if ( $this->readerSentence->localName == "chunk" ){
			// Move inside the chunk
			$this->readerSentence->read();
		}
								
		while ($this->readerSentence->next() && ($this->readerSentence->localName == "#text" || $this->readerSentence->localName == "ns")) {}				

		if ($this->readerSentence->localName == "tok"){			
			$e = new SimpleXMLElement($this->readerSentence->readOuterXML());
			$t = new TakipiToken((string)$e->orth);
			foreach ($e->lex as $lex){
				$a = $lex->attributes();
				$t->addLex((string)$lex->base, (string)$lex->ctag, $a['disamb']=="1");
			}
			return $t;
		}else
			return false;
	}
	
} 

?>
