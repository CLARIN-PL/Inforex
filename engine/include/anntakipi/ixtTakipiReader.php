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
	var $token_index = 0;
	
	/**
	 * 
	 * Enter description here ...
	 */
	function __construct(){
		$this->reader = new XMLReader();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $file
	 */
	function loadFile($file){
		
		$xml = file_get_contents($file);
		if (substr($xml, 0, 5) != "<?xml")
			$xml = "<doc>$xml</doc>";
			
		$this->reader->xml($xml);
		// Read the top node.
		while ($this->reader->localName != 'chunk'){
			$this->reader->read();
			$this->line++; 			
		}
	}
	
	function close(){
		$this->reader->close();
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
		$this->line++; 			
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
							
			return true;					
		}
		else{
			if ($this->reader->next("chunk")){
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
		
		if ( $this->reader->localName == "chunk" && $this->reader->nodeType == XMLReader::ELEMENT ){
			// Move inside the chunk
			while ( $this->reader->localName != "tok" )
				$this->reader->read();
		}
								
		if ($this->reader->localName == "tok"){						
			$e = new SimpleXMLElement($this->reader->readOuterXML());
			$t = new TakipiToken((string)$e->orth);
			foreach ($e->lex as $lex){
				$a = $lex->attributes();
				$t->addLex((string)$lex->base, (string)$lex->ctag, $a['disamb']=="1");
				
				// Parse <iob> element
				if (isset($e->iob)){
					$iobs = explode(" ", trim($e->iob));
					if ( count($iobs)>0 ){
						foreach ($iobs as $iob){
							if (strlen($iob)>0){
								if (preg_match("/^([BIO])-([A-Z_]+)$/", $iob, $matches)){
									$iob_type = $matches[1];
									$iob_name = mb_strtoupper($matches[2]);
									$t->channels[$iob_name] = $iob_type;
								}
								else							
								{
									print_r($t);
									throw new Exception("IOB tag is malformed: >$iob<");
								}
							}
						}	
					}				
				}
			}
			$this->reader->next(); // go to inner content
			$this->reader->next(); // go to next tag (<tok>, <ns/> or </chunk>)

			if ( $this->reader->localName == "ns" ){
				$this->reader->next();
				$this->reader->next();
				$t->setNS(true);
			}
			$this->token_index++;
			//print sprintf(str_pad("", 8, chr(8))."%-8d", $this->token_index);
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
	
	function readDocument(){
		$document = new TakipiDocument();
		while ( ( $sentence = $this->readSentence()) !== false )
			$document->sentences[] = $sentence;
		return $document;
	}
	
	/**
	 * Load TaKIPI file and returns as an TakipiDocument object.
	 */
	static function createDocument($filename){
		$reader = new TakipiReader();
		$reader->loadFile($filename);
		$document = $reader->readDocument();
		$reader->close();
		return $document;
	}
	
	static function createDocumentFromText($text){
		$reader = new TakipiReader();
		$reader->loadText($text);
		$document = $reader->readDocument();
		$reader->close();
		return $document;
	}
	
} 

?>
