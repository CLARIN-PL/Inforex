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
}

class CclChunk{
	var $id; // optional
	var $type; //required
	var $sentencecs = array();	
}

class CclSentence{
	var $id; // optional	
	var $tokens = array();
}

class CclToken{
	var $orth = null;
	// If token is preceded by a white space
	var $ns = null;	
	var $lexems = array();
}

class CclLexem{
	var $disamb = null;
	var $base = null;
	var $ctag = null;	
}
?>