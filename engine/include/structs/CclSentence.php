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

class CclSentence{
	var $id; // optional	
	var $tokens = array();
	var $channels = array(); //key: annotation_type, value: last channel number
	
	
	function setId($id){
		$this->id = $id;
	}
	
	function addToken($token){
		assert('$token instanceof CclToken');
		$this->tokens[] = $token;		
	}
	
	function getTokens(){
		return $this->tokens;
	}
	
	function getId(){
		return $this->id;
	}	
	
	function setChannel($type, $value){
		$this->channels[$type] = $value;
	}
	
	function incChannel($type){
		if (!array_key_exists($type, $this->channels))
			$this->channels[$type]=1;
		else $this->channels[$type]++;
	}
	
	function fillChannel($type){
		foreach ($this->tokens as $token){
			$token->fillChannel($type);
		}
	}
	
	function getChannel($type){
		if ($type == null) return 0;
		if (!array_key_exists($type, $this->channels))
			return 0;
		else return $this->channels[$type];
	}
	
    public function setSentenceIndexInTokens($sentenceIndex) {
        foreach($this->tokens as $token) {
            $token->setParentSentenceIndex($sentenceIndex);
        }
    }   // setSentenceIndexInTokens

    public function setChunkIndexInTokens($chunkIndex) {
        foreach($this->tokens as $token) {
            $token->setParentChunkIndex($chunkIndex);
        }
    }   // setChunkIndexInTokens

} // CclSentence class

?>
