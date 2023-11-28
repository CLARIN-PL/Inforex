<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * This file contains classes to represent annotated document in ccl style.
 * Document contains a set of chunks and set of relations. Chunk contains
 * sentenes. Sentence contains token. Token contains channels. Values in 
 * channels represent annotation numbers.
 */
class CclDocument{
	var $id; // optional	
	var $chunks = array();
	var $fileName = null;
	var $tokens = array(); //array of references to tokens in struct	
	var $relations = array();
	var $errors = array();
	var $subcorpus = null;
	var $report = null;
	
	var $char2token = array();
	
	function addError($error){
		assert('$error instanceof CclError');
		$this->errors[] = $error;
	}
	
	function getErrors(){
		return $this->errors;
	}
	
	function hasErrors(){
		return !empty($this->errors);
	}
	
	function setReport($report){
		$this->report = $report;
	}
	
	function getReport(){
		return $this->report;
	}
		
	
	function setId($id){
		$this->id = $id;
	}	

	function setFileName($fileName){
		$this->fileName = $fileName;
	} 

	function setSubcorpus($subcorpus){
		$this->subcorpus = $subcorpus;
	} 
	
	function addChunk($chunk){
		assert('$chunk instanceof CclChunk');
        $chunk->setChunkIndexInTokens(count($this->chunks)); 
		$this->chunks[] = $chunk;
	}
	
	function addToken($token){
		assert('$token instanceof CclToken');
		$index = count($this->tokens);
		$this->tokens[] = $token;
		for ( $i=$token->getFrom(); $i<=$token->getTo(); $i++)
			$this->char2token[$i] = $index;
	}
	
	function getChunks(){
		return $this->chunks;
	}
	
	function getId(){
		return $this->id;
	}
	
	function getFileName(){
		return $this->fileName;
	}
	
	function getSubcorpus(){
		return $this->subcorpus;
	} 
		
	function getTokens(){
		return $this->tokens;
	}
	
	function getRelations(){
		return $this->relations;
	}
	
	function setAnnotationLemma($annotation_lemma){

		if ( !isset($this->char2token[$annotation_lemma['from']])){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setAnnotationLemma");
			$e->addObject("annotation", $annotation_lemma);
			$e->addComment("Annotation out of range (annotation.from > document.char_count)");
			$this->errors[] = $e;
			return;				
		}

		if ( !isset($this->char2token[$annotation_lemma['to']])){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setAnnotationLemma");
			$e->addObject("annotation", $annotation_lemma);
			$e->addComment("Annotation out of range (annotation.to > document.char_count)");
			$this->errors[] = $e;
			return;				
		}
			
		$i = $this->char2token[$annotation_lemma['from']];
		$token = & $this->tokens[$i];
			
		if (! $token->setAnnotationLemma($annotation_lemma)){
            // 'dead code' - actually CclToken->setAnnotationLemma()
            // returns always true. For invalid data too.
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setAnnotationLemma");
			$e->addObject("annotation_lemma", $annotation_lemma);
			$e->addObject("token", $token);
			$e->addComment("000 cannot set annotation lemma to specific token");
			$this->errors[] = $e;
		}
	}

    function setAnnotationProperty($annotation_property){

        if ( !isset($this->char2token[$annotation_property['from']])){
            $e = new CclError();
            $e->setClassName("CclDocument");
            $e->setFunctionName("setAnnotation");
            $e->addObject("annotation", $annotation_property);
            $e->addComment("Annotation out of range (annotation.from > document.char_count)");
            $this->errors[] = $e;
            return;
        }

        if ( !isset($this->char2token[$annotation_property['to']])){
            $e = new CclError();
            $e->setClassName("CclDocument");
            $e->setFunctionName("setAnnotation");
            $e->addObject("annotation", $annotation_property);
            $e->addComment("Annotation out of range (annotation.to > document.char_count)");
            $this->errors[] = $e;
            return;
        }

        $i = $this->char2token[$annotation_property['from']];
        $token = & $this->tokens[$i];

        if (! $token->setAnnotationProperty($annotation_property)){
            $e = new CclError();
            $e->setClassName("CclDocument");
            $e->setFunctionName("setAnnotationProperty");
            $e->addObject("annotation_property", $annotation_property);
            $e->addObject("token", $token);
            $e->addComment("000 cannot set annotation property to specific token");
            $this->errors[] = $e;
        }
    }
	
	//function for normal annotations (not continuous)
	function setAnnotation($annotation){
		$found = false;
		$sentence = null; //parent sentence 
		$type = $annotation['type'];

		if ( !isset($this->char2token[$annotation['from']]) ){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setAnnotation");
			$e->addObject("annotation", $annotation);
			$e->addComment("Annotation out of range (annotation.from > document.char_count)");
			$this->errors[] = $e;
			return;				
		}

		if ( !isset($this->char2token[$annotation['to']]) ){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setAnnotation");
			$e->addObject("annotation", $annotation);
			$e->addComment("Annotation out of range (annotation.to > document.char_count)");
			$this->errors[] = $e;
			return;				
		}
		
		for ($i = $this->char2token[$annotation['from']]; $i<= $this->char2token[$annotation['to']]; $i++){
			$token = $this->tokens[$i];
			if (!$found){
                $sentence = $this->getSentenceByToken($token);
				if( $sentence != null) {
					$sentence->incChannel($type);
				}
				$found = true;
			}	
			if ( $annotation['value'] ){
				$prop_name = sprintf("sense:%s", $annotation['name']);
				$token->prop[$prop_name] = $annotation['value'];
			}
			if (! $token->setAnnotation($annotation,$this->getSentenceByToken($token)->channels)){					
				$e = new CclError();
				$e->setClassName("CclDocument");
				$e->setFunctionName("setAnnotation");
				$e->addObject("annotation", $annotation);
				$e->addObject("token", $token);
				$e->addComment("000 cannot set annotation to specific token");
				$this->errors[] = $e;	
			}
		}
		
		if ($sentence==null){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setAnnotation");
			$e->addObject("annotation", $annotation);
			$e->addComment("014 cannot set annotation");
			$this->errors[] = $e;		
		}
		else {
			$sentence->fillChannel($type);
		}
		
	}
	
	function setContinuousAnnotation2($annotation1, $annotation2){
		$type = $annotation1['type'];
		if ($type != $annotation2['type']){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setContinuousAnnotation2");
			$e->addObject("annotation1", $annotation1);
			$e->addObject("annotation2", $annotation2);
			$e->addComment("001 Continuous annotations must be the same type");
			$this->errors[] = $e;
			return false;
		}
		$tokens = array();
		$sentence = null;
		$found = false;		

		foreach ($this->tokens as $token){
			//collect all tokens belonging to continuous annotations 
			if ($token->isIn($annotation1) || $token->isIn($annotation2)){
				$tokens[] = $token;
				if (!$found){
                    $sentence = $this->getSentenceByToken($token);
					$found = true;
				}
				else {
					if ($this->getSentenceByToken($token) != $sentence){
						$e = new CclError();
						$e->setClassName("CclDocument");
						$e->setFunctionName("setContinuousAnnotation2");
						$e->addObject("annotation1", $annotation1);
						$e->addObject("annotation2", $annotation2);								
						$e->addComment("002 Continuous annotations annotation1 and annotation2 must be in the same sentence");
						$this->errors[] = $e;	
						return false;			
						//throw new Exception("Continuous annotations {$annotation1['id']} and {$annotation2['id']} must be in the same sentence");
					}
				}
			}
		}
		
		$channelValue = 0; //value to set for all tokens of continuous annotation
		$srcSentenceChannel = 0; //current max sentence channel value (to restore)
		$otherChannelValues = array(); //for all channel values in continuous tokens

		if($sentence != null ) {
			if ($sentence->getChannel($type) == 0) { //if no channel type in sentence
				$sentence->incChannel($type); //increment value (set initial = 1)
				$channelValue = $sentence->getChannel($type); //value 1 will be set for all tokens in continuous anns (cont. tokens)
				$srcSentenceChannel = $channelValue; //max value to restore the same as for continuous tokens
			} else { //for sure in sentence exists at least 1 token with min value = 1
				$srcSentenceChannel = $sentence->getChannel($type); //get current max value of channel in sentence
				foreach ($tokens as $token) {
					$tokenChannel = $token->getChannel($type);
					if ($tokenChannel != 0 && !in_array($tokenChannel, $otherChannelValues)) {
						$otherChannelValues[] = $tokenChannel;
						if ($channelValue == 0 || $channelValue > $tokenChannel)
							$channelValue = $tokenChannel;
					}
				}
				if ($channelValue == 0) { //no token belongs partially to continuous annotation, this part must have bigger channel number
					$sentence->incChannel($type); //increment value
					$channelValue = $sentence->getChannel($type); //incremented value will be set for all continuous tokens
					$srcSentenceChannel = $channelValue; //max value to restore the same as for continuous tokens
				}
			}
			$sentence->setChannel($type, $channelValue);//set proper channel value to set for all continuous tokens

		foreach ($tokens as $token){
			if ( !$token->setContinuousAnnotation2($type,$this->getSentenceByToken($token)->channels)){ //set value 
				$e = new CclError();
				$e->setClassName("CclDocument");
				$e->setFunctionName("setContinuousAnnotation2");
				$e->addObject("annotation1", $annotation1);
				$e->addObject("annotation2", $annotation2);		
				$e->addObject("token", $token);
				$e->addObject("type", $type);						
				$e->addComment("004 cannot set continuous annotation to specific token, no parent channel in sentence");
				$this->errors[] = $e;		
				return false;
			}				
		}
		
		//renumber all tokens in sentence (if necessary)
		$tokens = $sentence->getTokens();
		foreach ($tokens as $token){
			$tokenChannelValue = $token->getChannel($type); 
			if ($tokenChannelValue!=$channelValue && in_array($tokenChannelValue, $otherChannelValues) )
				$token->setContinuousAnnotation2($type,$this->getSentenceByToken($token)->channels);
			
		}
		
		$sentence->setChannel($type, $srcSentenceChannel); //restore max channel value
		$sentence->fillChannel($type); //fill rest with zeros (if needed)
		}else{
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("Sentence problem");
			$e->addObject("document", $this->report);
			$e->addObject("type", $type);
			$e->addComment("no sentence to add channel");
			$this->errors[] = $e;
			return false;
		}
	}
	
	
	function setRelation($annotation1, $annotation2, $relation){
		$name = $relation['name'];
		$type1 = $annotation1['type'];
		$type2 = $annotation2['type'];		
		$token1 = null;
		$token2 = null;
		
		foreach ($this->tokens as $token){
			if ($token->isIn($annotation1) && $token1==null)
				$token1 = $token;
			if ($token->isIn($annotation2) && $token2==null)
				$token2 = $token;
			if ($token1 && $token2) break;
		}
		if (!($token1 && $token2) ){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setRelation");
			$e->addObject("annotation1", $annotation1);
			$e->addObject("annotation2", $annotation2);
			$e->addObject("relation", $relation);
			$e->addComment("005 cannot set relation 1");
			$this->errors[] = $e;					
			return false;
		}
		
		$fromChannel = $token1->getChannel($type1);		
		$toChannel = $token2->getChannel($type2);
		if (!($fromChannel && $toChannel) ){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setRelation");
			$e->addObject("annotation1", $annotation1);
			$e->addObject("annotation2", $annotation2);
			$e->addObject("relation", $relation);
			$e->addComment("006 cannot set relation 2");
			$this->errors[] = $e;					
			return false;
		}		
		
		$r = new CclRelation();
		$r->setSet($relation['rsname']);
		$r->setFromSentence($this->getSentenceByToken($token1)->getId());
		$r->setToSentence($this->getSentenceByToken($token2)->getId());
		$r->setFromChannel($token1->getChannel($type1));
		$r->setToChannel($token2->getChannel($type2));
		$r->setFromType($type1);
		$r->setToType($type2);
		$r->setName($name);
		
		$this->relations[] = $r;
	}

    private function getSentenceByIndex($chunkIndex,$sentenceIndex){
        // chunkIndex may be digit 0
        if( is_numeric($chunkIndex) && $chunkIndex < count($this->chunks) ) {
            return $this->chunks[$chunkIndex]->getSentenceByIndex($sentenceIndex);
        } else {
            return null;
        }           
    } // getSentenceByIndex
	
    protected function getSentenceByToken($token){

        if($token instanceof CclToken)
            return $this->getSentenceByIndex($token->getParentChunkIndex(),$token->getParentSentenceIndex());    
        else
            return null;

    } // getSentenceByToken

} // CclDocument

?>
