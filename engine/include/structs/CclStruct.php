<?
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
	
	function setId($id){
		$this->id = $id;
	}	

	function setFileName($fileName){
		$this->fileName = $fileName;
	} 

	
	function addChunk($chunk){
		assert('$chunk instanceof CclChunk');
		$this->chunks[] = $chunk;
	}
	
	function addToken($token){
		$this->tokens[] = $token;
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
	
	function getTokens(){
		return $this->tokens;
	}
	
	function getRelations(){
		return $this->relations;
	}
	
	//function for normal annotations (not continuous)
	function setAnnotation($annotation){
		$found = false;
		$sentence = null; //parent sentence 
		$type = $annotation['type'];
		foreach ($this->tokens as $token){
			if ($token->isIn($annotation)){
				if (!$found){
					$sentence = $token->getParent();
					$sentence->incChannel($type);
					$found = true;
				}	
				if (! $token->setAnnotation($annotation)){					
					$e = new CclError();
					$e->setClassName("CclDocument");
					$e->setFunctionName("setAnnotation");
					$e->addObject("annotation", $annotation);
					$e->addObject("token", $token);
					$e->addComment("000 cannot set annotation to specific token");
					$this->errors[] = $e;	
				}
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
	
	function setContinuousAnnotation($annotation1, $annotation2){
		$type = $annotation1['type'];
		if ($type != $annotation2['type']){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setContinuousAnnotation");
			$e->addObject("annotation1", $annotation1);
			$e->addObject("annotation2", $annotation2);
			$e->addComment("001 Continuous annotations must be the same type");
			$errors[] = $e;
			return false;
			//throw new Exception("Continuous annotations must be the same type");
		}
		$tokens = array();
		$sentence = null;
		$found = false;		
		foreach ($this->tokens as $token){
			//collect all tokens belonging to continuous annotations 
			if ($token->isIn($annotation1) || $token->isIn($annotation2)){
				$tokens[] = $token;
				if (!$found){
					$sentence = $token->getParent();
					$found = true;
				}
				else {
					if ($token->getParent() != $sentence){
						$e = new CclError();
						$e->setClassName("CclDocument");
						$e->setFunctionName("setContinuousAnnotation");
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
		if ($sentence->getChannel($type)==0){ //if no channel type in sentence
			$sentence->incChannel($type); //increment value (set initial = 1)
			$channelValue = $sentence->getChannel($type); //value 1 will be set for all tokens in continuous anns (cont. tokens)
			$srcSentenceChannel = $channelValue; //max value to restore the same as for continuous tokens
		}
		else { //for sure in sentence exists at least 1 token with min value = 1
			$srcSentenceChannel = $sentence->getChannel($type); //get current max value of channel in sentence		
			foreach ($tokens as $token){
				$tokenChannel = $token->getChannel($type); 
				if ($channelValue==0 && $tokenChannel!=0) //all continuous tokens can have value = 0 or at most X  
					$channelValue = $tokenChannel;
				else if ($channelValue!=0 && $tokenChannel!=0 && $channelValue!=$tokenChannel){ //0 or X or Y - WRONG
					$e = new CclError();
					$e->setClassName("CclDocument");
					$e->setFunctionName("setContinuousAnnotation");
					$e->addObject("annotation1", $annotation1);
					$e->addObject("annotation2", $annotation2);								
					$e->addComment("003 annotations annotation1 and annotation2 cannot be set as continuous, more than one channel is set here");
					$this->errors[] = $e;			
					return false;			
					//throw new Exception("Annotations {$annotation1['id']} and {$annotation2['id']} cannot be set as continuous, more than one channel is set here");
				}
			}
			if ($channelValue==0){ //no token belongs partially to continuous annotation, this part must have bigger channel number
				$sentence->incChannel($type); //increment value 
				$channelValue = $sentence->getChannel($type); //incremented value will be set for all continuous tokens
				$srcSentenceChannel = $channelValue; //max value to restore the same as for continuous tokens
			}
		}
		$sentence->setChannel($type, $channelValue);//set proper channel value to set for all continuous tokens
		foreach ($tokens as $token){
			if ( !$token->setContinuousAnnotation($type)){ //set value 
				$e = new CclError();
				$e->setClassName("CclDocument");
				$e->setFunctionName("setContinuousAnnotation");
				$e->addObject("annotation1", $annotation1);
				$e->addObject("annotation2", $annotation2);		
				$e->addObject("token", $token);
				$e->addObject("type", $type);						
				$e->addComment("004 cannot set continuous annotation to specific token");
				$this->errors[] = $e;		
				return false;
			}				
			
			
		}
		$sentence->setChannel($type, $srcSentenceChannel); //restore max channel value
		$sentence->fillChannel($type); //fill rest with zeros (if needed)
	}
	
	function setContinuousAnnotation2($annotation1, $annotation2){
		$type = $annotation1['type'];
		if ($type != $annotation2['type']){
			$e = new CclError();
			$e->setClassName("CclDocument");
			$e->setFunctionName("setContinuousAnnotation");
			$e->addObject("annotation1", $annotation1);
			$e->addObject("annotation2", $annotation2);
			$e->addComment("001 Continuous annotations must be the same type");
			$errors[] = $e;
			return false;
			//throw new Exception("Continuous annotations must be the same type");
		}
		$tokens = array();
		$sentence = null;
		$found = false;		
		foreach ($this->tokens as $token){			
			//collect all tokens belonging to continuous annotations 
			if ($token->isIn($annotation1) || $token->isIn($annotation2)){
				$tokens[] = $token;
				if (!$found){
					$sentence = $token->getParent();
					$found = true;
				}
				else {
					if ($token->getParent() != $sentence){
						$e = new CclError();
						$e->setClassName("CclDocument");
						$e->setFunctionName("setContinuousAnnotation");
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
		 
		if ($sentence->getChannel($type)==0){ //if no channel type in sentence
			$sentence->incChannel($type); //increment value (set initial = 1)
			$channelValue = $sentence->getChannel($type); //value 1 will be set for all tokens in continuous anns (cont. tokens)
			$srcSentenceChannel = $channelValue; //max value to restore the same as for continuous tokens
		}
		else { //for sure in sentence exists at least 1 token with min value = 1
			$srcSentenceChannel = $sentence->getChannel($type); //get current max value of channel in sentence		
			foreach ($tokens as $token){
				$tokenChannel = $token->getChannel($type); 
				if ($tokenChannel!=0 && !in_array($tokenChannel, $otherChannelValues)){
					$otherChannelValues[] = $tokenChannel;
					if ($channelValue==0 || $channelValue>$tokenChannel) 
						$channelValue = $tokenChannel;
				}   
			}
			if ($channelValue==0){ //no token belongs partially to continuous annotation, this part must have bigger channel number
				$sentence->incChannel($type); //increment value 
				$channelValue = $sentence->getChannel($type); //incremented value will be set for all continuous tokens
				$srcSentenceChannel = $channelValue; //max value to restore the same as for continuous tokens
			}
		}
		$sentence->setChannel($type, $channelValue);//set proper channel value to set for all continuous tokens
		foreach ($tokens as $token){
			if ( !$token->setContinuousAnnotation2($type)){ //set value 
				$e = new CclError();
				$e->setClassName("CclDocument");
				$e->setFunctionName("setContinuousAnnotation");
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
				$token->setContinuousAnnotation2($type);
			
		}
		
		$sentence->setChannel($type, $srcSentenceChannel); //restore max channel value
		$sentence->fillChannel($type); //fill rest with zeros (if needed)
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
		$r->setFromSentence($token1->getParent()->getId());
		$r->setToSentence($token2->getParent()->getId());
		$r->setFromChannel($token1->getChannel($type1));
		$r->setToChannel($token2->getChannel($type2));
		$r->setFromType($type1);
		$r->setToType($type2);
		$r->setName($name);
		
		$this->relations[] = $r;
	}
	
}

class CclChunk{
	var $id; // optional
	var $type; //required
	var $sentences = array();	
	
	function addSentence($sentence){
		assert('$sentence instanceof CclSentence');
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
}

class CclSentence{
	var $id; // optional	
	var $tokens = array();
	var $channels = array(); //key: annotation_type, value: last channel number
	
	
	function setId($id){
		$this->id = $id;
	}
	
	function addToken($token){
		assert('$token instanceof CclToken');
		$token->setParent($this);
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
		if (!array_key_exists($type, $this->channels))
			return 0;
		else return $this->channels[$type];
	}
	
}

class CclToken{
	var $id = null;
	var $orth = null;
	// If token is preceded by a white space
	var $ns = false;	
	var $lexemes = array();
	var $from = null;
	var $to = null;
	var $parentSentence = null; //parent sentence
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
	
	function setParent($sentence){
		assert('$sentence instanceof CclSentence');
		$this->parentSentence = $sentence;
	}
	
	function setAnnotation($annotation){
		$type = $annotation['type'];
		if (array_key_exists($type, $this->channels) && $this->channels[$type]!=0 ){
			//var_dump($this);
			//throw new Exception("canot set annotation {$type} to specific token {$this->id}!");
			return false;
		}		
		
		if (!array_key_exists($type, $this->parentSentence->channels)  ){
			//annotation might exist in more than one sentence
			return false;
		}
		if ($type=="sense")
			$this->prop = $annotation['value'];	
		
		$this->channels[$type] = $this->parentSentence->channels[$type];
		
		return true;
	}
	
	// TODO: this function can be deleted	
	function setContinuousAnnotation($type){
		
		if (!array_key_exists($type, $this->parentSentence->channels)  ){
			//annotation might exist in more than one sentence
			return false;
		}		
		$correctValue = $this->parentSentence->channels[$type];
		if (array_key_exists($type, $this->channels) && $this->channels[$type]!=0 && $this->channels[$type]!=$correctValue ){
			//echo "Type: {$type}\n";
			//echo "Current value: {$this->channels[$type]}\n";
			//echo "Expected value: {$correctValue}\n";
			//var_dump($this);
			//throw new Exception("cannot set annotation {$type} to specific token {$this->id}!");
			return false;		
		}	
		$this->channels[$type] = $correctValue;
		return true;
	}	
	
	function setContinuousAnnotation2($type){
		//annotation might exist in more than one sentence
		if (!array_key_exists($type, $this->parentSentence->channels)  )
			return false;
		$this->channels[$type] = $this->parentSentence->channels[$type];
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
	
	function getParent(){
		return $this->parentSentence;
	}
	
	function isIn($annotation){
		return ($this->from >= $annotation['from'] && $this->to <= $annotation['to']);
	}
	

	
}

class CclLexeme{
	var $disamb = null;
	var $base = null;
	var $ctag = null;	
	
	function setDisamb($disamb){
		$this->disamb = $disamb;
	}
	
	function setBase($base){
		$this->base = $base;
	}
	
	function setCtag($ctag){
		$this->ctag = $ctag;
	}
	
	function getDisamb(){
		return $this->disamb;
	}
	
	function getBase(){
		return $this->base;
	}
	
	function getCtag(){
		return $this->ctag;
	}
	
}

class CclRelation{
	var $name = null;
	var $set = null;
	var $fromSentence = null;
	var $fromChannel = null;
	var $toSentence = null;
	var $toChannel = null;	
	var $fromType = null;
	var $toType = null;

	function getName(){
		return $this->name;
	}
	
	function getSet(){
		return $this->set;
	}
	
	function getFromSentence(){
		return $this->fromSentence;
	}
	
	function getToSentence(){
		return $this->toSentence;
	}
	
	function getFromChannel(){
		return $this->fromChannel;
	}
	
	function getToChannel(){
		return $this->toChannel;
	}
	
	function getFromType(){
		return $this->fromType;
	}	

	function getToType(){
		return $this->toType;
	}

	function setName($name){
		$this->name = $name;
	}
	
	function setSet($set){
		$this->set = $set;
	}
	
	function setFromSentence($fromSentence){
		$this->fromSentence = $fromSentence;
	}
	
	function setToSentence($toSentence){
		$this->toSentence = $toSentence;
	}
	
	function setFromChannel($fromChannel){
		$this->fromChannel = $fromChannel;
	}
	
	function setToChannel($toChannel){
		$this->toChannel = $toChannel;
	}
	
	function setFromType($fromType){
		$this->fromType = $fromType;
	}	

	function setToType($toType){
		$this->toType = $toType;
	}		
	
}

class CclError{
	var $className = null;
	var $functionName = null;
	var $objects = array();
	var $comments = array();
	
	
	function setClassName($className){
		$this->className = $className;
	}
	
	function setFunctionName($functionName){
		$this->functionName = $functionName;
	}
	
	function addObject($key, $value){
		$this->objects[$key] = $value;
	}
	
	function addComment($value){
		$this->comments[] = $value;
	}
	
	
	function getClassName(){
		return $this->className;
	}
	
	function getFunctionName(){
		return $this->functionName;
	}
	
	function getObjects(){
		return $this->objects;
	}
	
	function getComments(){
		return $this->comments;
	}	
	
	
	function __toString(){
		$str =  "---------------------ERROR-------------------------\n";
		$str .= "class:    {$this->className}\n";
		$str .= "function: {$this->functionName}\n";
		$str .= "comments: \n";
		foreach ($this->comments as $comment)
			$str .= "  $comment\n";
		$str .= "objects: \n";		
		
		foreach ($this->objects as $key=>$obj){
			if ($key=="token"){
				$str .= "  Token:\n";
				$str .= "    Orth: {$obj->getOrth()}\n"; 
				$str .= "    From: {$obj->getFrom()}\n";
				$str .= "    To  : {$obj->getTo()}\n";
			}
			elseif (strpos($key, "annotation") === 0){
				$str .= "  Annotation:\n";
				$str .= "    Key : $key \n";
				$str .= "    Type: {$obj['type']}\n"; 
				$str .= "    From: {$obj['from']}\n"; 
				$str .= "    To  : {$obj['to']}\n";
				$str .= "    Text: {$obj['text']}\n"; 
			}
			elseif ($key=="relation"){
				$str .= "  Relation:\n";
				$str .= "    Source id: {$obj['source_id']}\n"; 
				$str .= "    Target id: {$obj['target_id']}\n"; 
			}
			else {
				$str .= "  $key\n";
				$str .= "    build your own user-friendly dump\n";
			}			
		}		
		return $str;
	}
	
	
	
}

?>