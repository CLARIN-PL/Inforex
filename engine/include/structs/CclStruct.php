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
	
	function addToken(&$token){
		$this->tokens[$token->getId()] = &$token;
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
	
	
}

class CclChannel {
	var $name;
	var $value;
	
	function setName($name){
		$this->name = $name;
	}
	
	function setValue($value){
		$this->value = $value;
	}
	
	function getName(){
		return $this->name;
	}
	
	function getValue(){
		return $this->value;
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
}

class CclToken{
	var $id = null;
	var $orth = null;
	// If token is preceded by a white space
	var $ns = false;	
	var $lexemes = array();
	var $from = null;
	var $to = null;
	
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
	
	function getId(){
		return $this->id;
	}
	
	function getFrom(){
		return $this->from;
	}
	
	function getTo(){
		return $this->to;
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
?>