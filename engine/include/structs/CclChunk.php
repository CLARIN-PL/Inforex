<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Document contains a set of chunks and set of relations. Chunk contains
 * sentenes. Sentence contains token. Token contains channels. Values in 
 * channels represent annotation numbers.
 */

class CclChunk{
	var $id; // optional
	var $type; //required
	var $sentences = array();	
	
	function addSentence($sentence){
		assert('$sentence instanceof CclSentence');
        $sentence->setSentenceIndexInTokens(count($this->sentences));
		$this->sentences[] = $sentence;
	}
	
	function setType($type){
		$this->type = $type;
	}
	
	function setId($id){
		$this->id = $id;
	}
	
	function getSentences(){
		return $this->sentences;
	}
	
	function getId(){
		return $this->id;
	}
	
	function getType(){
		return $this->type;
	}	

    public function setChunkIndexInTokens($chunkIndex) {
        foreach($this->sentences as $sentence) {
            $sentence->setChunkIndexInTokens($chunkIndex);
        }
    } // setParentIndexesInTokens
	
    public function getSentenceByIndex($sentenceIndex){
        // sentenceIndex may be digit 0
        if( is_numeric($sentenceIndex) and ($sentenceIndex < count($this->sentences)) ) {
            return $this->sentences[$sentenceIndex];
        } else {
            return null;
        }
    } // getSentenceByIndex

} // CclChunk class

?>
