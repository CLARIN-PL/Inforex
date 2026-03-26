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

class CclToken{
	var $id = null;
	var $orth = null;
	// If token is preceded by a white space
	var $ns = false;	
	var $lexemes = array();
	var $from = null;
	var $to = null;
    private $parentSentenceIndex = null; 
        // parent sentence index in chunk sentences[] array
    private $parentChunkIndex = null;
        // parent chunk index in document chunks[] array
	var $channels = array(); //same as in sentence, but with unique according number
	var $prop = null;
	
	function setOrth($orth){
		$this->orth = $orth;
	}	
	
	function setNs($ns){
		$this->ns = $ns;
	}
	
	function setId($id){
		$this->id = $id;
	}
	
	function setFrom($from){
		$this->from = $from;
	}
	
	function setTo($to){
		$this->to = $to;
	}
	
	function setAnnotationLemma($annotation_lemma){
        $type = (isset($annotation_lemma["type"]) && ($annotation_lemma["type"])) ? $annotation_lemma["type"] : '';
        $lemma = (isset($annotation_lemma["lemma"]) && ($annotation_lemma["lemma"])) ? $annotation_lemma["lemma"] : null;
		$this->prop[$type.":lemma"] = $lemma;
		return true;
	}

    function setAnnotationProperty($annotation_property){
        $this->prop[$annotation_property["type"].":".$annotation_property["name"]] = $annotation_property["value"];
        return true;
    }
	
	public function setAnnotation($annotation,$parentChannels = null){

		$type = $annotation['type'];
		if ($type=="sense"){
			/*
			 * Caution! Now WSD annotations are not part of any relations
			 * and all instances (even having more than 1 name in db) can 
			 * be renumbered in 'sense' channel, e.g.
			 * [metrów] as wsd_m got number 6, but in db this instance
			 * was described also as wsd_metr (#3767), so there will be next 
			 * assignment of channel number from parent sentence, which will be 7. 
			 */

			//if more than 1 annotation with the same name length covers one token (#3767):
			if ($this->prop && (count($this->prop) == count($annotation['value'])) ){
				return false;
			}
			
			else if (!$this->prop || (count($this->prop) < count($annotation['value'])) ){
				$this->prop = $annotation['value'];	
			}			
		}
        else {
			if (array_key_exists($type, $this->channels) && $this->channels[$type]!=0 ){
				return false;
			}		
			
            if (is_array($parentChannels) && !array_key_exists($type, $parentChannels)  ){
				return false;
			}
		}
        // add to typed channel and return true if not exited earlier
        $this->channels[$type] = $annotation['id'];
		return true;
	} // setAnnotation()
	
	function setContinuousAnnotation2($type,$parentChannels = null){

        // $parentChannels may be null or sth
        if(!is_array($parentChannels))
            return false;
		//annotation might exist in more than one sentence
		if (!array_key_exists($type, $parentChannels)  )
			return false;
		$this->channels[$type] = $parentChannels[$type];
		return true;
	}		
	
	function fillChannel($type){
		if (!array_key_exists($type, $this->channels))
			$this->channels[$type]=0;		
	}
	
	function addLexeme($lexeme){
		$this->lexemes[] = $lexeme;
	}

	function getOrth(){
		return $this->orth;
	}
	
	function getNs(){
		return $this->ns;
	}
	
	function getLexemes(){
		return $this->lexemes;
	}
	
	function getChannels(){
		return $this->channels;
	}
	
	function getChannel($type){
		if (!array_key_exists($type, $this->channels))
			return 0;
		return $this->channels[$type];
	}
	
	function getId(){
		return $this->id;
	}
	
	function getFrom(){
		return $this->from;
	}
	
	function getTo(){
		return $this->to;
	}
	
	function isIn($annotation){
		return ($this->from >= $annotation['from'] && $this->to <= $annotation['to']);
	}
	
	/**
	 * Return base for first disamb lexem.
	 * If no dismb lexems is found then the base of a first lexem is returned.
	 */
	function getBase(){	
		foreach ($this->lexemes as $lexem){
			if ($lexem->getDisamb()){
				return $lexem->getBase();
			}
		}
		if ( count($this->lexemes) > 0){
			return $this->lexemes[0]->getBase();
		}
		return null;
	}

    public function setParentSentenceIndex($parentSentenceIndex) {
        $this->parentSentenceIndex = $parentSentenceIndex;
    } // setParentSentenceIndex

    public function getParentSentenceIndex() {
        return $this->parentSentenceIndex;
    } // getParentSentenceIndex
	
    public function setParentChunkIndex($parentChunkIndex) {
        $this->parentChunkIndex = $parentChunkIndex;
    } // setParentChunkIndex

    public function getParentChunkIndex() {
        return $this->parentChunkIndex;
    } // getParentChunkIndex

} // CclToken class

?>
