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
	
	function addChunk($chunk){
		assert('$chunk instanceof CclChunk');
		$this->chunks[] = $chunk;
	}
}

class CclChunk{
	var $id; // optional
	var $type; //required
	var $sentencecs = array();	
	
	function addSentence($sentence){
		assert('$sentence instanceof CclSentence');
		$this->sentences[] = $sentence;
	}
	
	function setType($type){
		$this->type = $type;
	}
}

class CclSentence{
	var $id; // optional	
	var $tokens = array();
	
	function addToken($token){
		assert('$token instanceof CclToken');
		$this->tokens[] = $token;		
	}
}

class CclToken{
	var $orth = null;
	// If token is preceded by a white space
	var $ns = null;	
	var $lexems = array();
	
	function __construct($orth, $ns){
		$this->orth = $orth;
		$this->ns = $ns;
	}
}

class CclLexem{
	var $disamb = null;
	var $base = null;
	var $ctag = null;	
}
?>