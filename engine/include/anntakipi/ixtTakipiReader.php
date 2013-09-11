<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
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
		$this->setNs = false;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $file
	 */
	function loadFile($file){
		
		$xml = file_get_contents($file);
						
		if (substr($xml, 0, 5) != "<?xml"){
			$xml = str_replace("<chunkList>", "", $xml);
			$xml = str_replace('</chunkList>', "", $xml);
			$xml = "<doc>$xml</doc>";			
		}
			
		$this->reader->xml($xml);
		$this->reader->read();
		$this->line++; 			
	}
	
	function close(){
		$this->reader->close();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $text
	 */
	function loadText($xml){
		
		if (substr($xml, 0, 5) != "<?xml"){
			$xml = str_replace("<chunkList>", "", $xml);
			$xml = str_replace('</chunkList>', "", $xml);
			$xml = "<doc>$xml</doc>";			
		}

		$this->reader->xml($xml);
		// Read the top node.
		$this->reader->read(); 					
		$this->line++; 			
	}

	/**
	 * Ustawia wskaźnik na następny znacznik CHUNK. 
	 * Jeżeli czytnik nie znajdzie kolejnego CHUNK-a, to zwracany jest false.
	 */
	function nextChunk(){
		do {
			$read = $this->reader->read();
		}while ( $read && !($this->reader->localName == "chunk" && $this->reader->nodeType == XMLReader::ELEMENT));
			
		if (!$read)
			return false;
							
		return true;							
	}

	/**
	 * Move the reader to a next sentence.
	 * @return TRUE if pointer was set to the next sentence, 
	 * FALSE if the end of the document was reached.
	 */
	function nextSentence(){
				
		// Move to a first CHUNK
		if ($this->reader->localName == "doc" || $this->reader->localName == "cesAna" ){
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
								
		if ( $this->reader->localName == "ns" ){
			$this->reader->next();
			$this->reader->next();
			$this->setNs = true;
		}
								
								
		if ($this->reader->localName == "tok"){						
			$e = new SimpleXMLElement($this->reader->readOuterXML());
			//print_r($e);
			$t = new TakipiToken((string)$e->orth);
			if ($this->setNs) {
				//print "set ns \n";
				$t->setNS(true);
				$this->setNs=false;
			}
			/* Wczytaj lexemy tokenu */
			foreach ($e->lex as $lex){
				$a = $lex->attributes();
				$t->addLex((string)$lex->base, (string)$lex->ctag, $a['disamb']=="1");
			}
						
			/* Wczytaj informacje o kanałach */
			if ( isset($e->ann) && $e->ann ){
				foreach ($e->ann as $ann){
					$t->channels[strval($ann['chan'])] = strval($ann);
				}
			}
			if(isset($e->prop) && $e->prop){
				foreach($e->prop as $key => $prop){
					$at_lemma = explode(":",$prop["key"]);
					if(count($at_lemma) == 2 && $at_lemma[1] == "lemma"){
						$annotation_type = $at_lemma[0];
						$annotation_lemma = (string)$prop;
						$t->lemmas[$annotation_type] = $annotation_lemma;
					}
				}
			}
			
			$this->reader->next(); // go to inner content
			$this->reader->next(); // go to next tag (<tok>, <ns/> or </chunk>)

			if ( $this->reader->localName == "ns" ){
				$this->reader->next();
				$this->reader->next();
				$this->setNs = true;
			}
			$this->token_index++;
			
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
	
	/**
	 * Wczytuje chunk tekstu (sekwencję zdań oznaczonych jako jeden chunk).
	 * Wewnętrzny wskaźnik musi być uwstawiony na znacznik <chunk>
	 */
	function readChunk(){
		
		$id = null;
		$chunk = null;
		
		if ($this->reader->localName != "chunk")
			throw new Exception("The reader is not set on CHUNK tag.");
			
		$id = $this->reader->getAttribute("id");
//		if ( !$id ) 
//			throw new Exception("Attribute ID in CHUNK tag not found.");

		$chunk = new TakipiChunk($id);

		/* Przejdź do elementu zagnieżdżone, którym powinien być <SENTENCE> */
		$this->reader->read();
		$this->reader->read();

		if ($this->reader->localName != "sentence")
			throw new Exception("SENTENCE tag inside CHUNK not found");
			
		while ( $this->reader->localName == "sentence" && $this->reader->nodeType == XMLReader::ELEMENT ) {

			$id = $this->reader->getAttribute("id");
			
			$sentence = new TakipiSentence($id);
			
			/* Przesuń do pierwszego TOK w SENTENCE. */
			$this->reader->read();
			$this->reader->read();
						
			while ($t = $this->readToken()){
				$sentence->tokens[] = $t;
			}
			
			$chunk->sentences[] = $sentence;

			/* Przejdź do pierwszego elementu po tagu zamykającym SENTENCE */			
			$this->reader->read();
			$this->reader->read();
		}
		
		return $chunk;		
	}
	
	function readDocument(){
		$document = new TakipiDocument();
		while ( ( $sentence = $this->readSentence()) !== false ){
			$document->sentences[] = $sentence;
		}
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
