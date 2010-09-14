<?php
/**
 * Iterative TaKIPI file reader.
 * @author Michał Marcińczuk <michal.marcinczuk@pwr.wroc.pl>
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
	
	/**
	 * 
	 * Enter description here ...
	 */
	function __construct(){
		$this->reader = new XMLReader();
		$this->readerSentence = new XMLReader();		
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $file
	 */
	function loadFile($file){
		
		$xml = file_get_contents($file);
		$xml = "<doc>$xml</doc>";
		$this->reader->xml($xml);
		// Read the top node.
		$this->reader->read(); 			
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $text
	 */
	function loadText($text){
		
		$this->reader->xml($text);
		// Read the top node.
		$this->reader->read(); 					
	}

	/**
	 * Move the reader to a next sentence.
	 * @return TRUE if pointer was set to the next sentence, 
	 * FALSE if the end of the document was reached.
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

	/**
	 * Reads next token in a sentence. Can be used after invoking nextSentence().
	 * @return FALSE if end of sentence was reached or object of TakipiToken.
	 */
	function readToken(){
		
		if ( $this->readerSentence->localName == "chunk" && $this->readerSentence->nodeType == XMLReader::ELEMENT ){
			// Move inside the chunk
			while ($this->readerSentence->localName != "tok")
				$this->readerSentence->read();
		}
								
		if ($this->readerSentence->localName == "tok"){			
			$e = new SimpleXMLElement($this->readerSentence->readOuterXML());
			$t = new TakipiToken((string)$e->orth);
			foreach ($e->lex as $lex){
				$a = $lex->attributes();
				$t->addLex((string)$lex->base, (string)$lex->ctag, $a['disamb']=="1");
				
				// Parse <iob> element
				if (isset($e->iob)){
					$iobs = explode(" ", trim($e->iob));
					if ( count($iobs)>0 ){
						foreach ($iobs as $iob){
							if (preg_match("/^([BIO])->([A-Z_]+)$/", $iob, $matches)){
								$iob_type = $matches[1];
								$iob_name = $matches[2];
								$t->channels[$iob_name] = $iob_type;
							}else
								throw new Exception("IOB tag is malformed: '$iob'");
						}	
					}				
				}
			}
			$this->readerSentence->next(); // go to inner content
			$this->readerSentence->next(); // go to next tag (<tok>, <ns/> or </chunk>)

			if ( $this->readerSentence->localName == "ns" ){
				$this->readerSentence->next();
				$this->readerSentence->next();
				$t->setNS(true);
			}
			return $t;
		}else
			return false;
	}
	
	/**
	 * Reads next sentence from the file. 
	 * @return If the sentence exists returns object of TakipiSentence, in other case returns FALSE
	 */
	function readSentence(){
		
		if ($this->nextSentence()){
			$sentence = new TakipiSentence();
			while ($t = $this->readToken()){
				$sentence->tokens[] = $t;
			}
			return $sentence;
		}else{
			return false;
		}
	}
} 

?>
