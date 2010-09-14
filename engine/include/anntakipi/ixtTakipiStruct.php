<?php
/**
 * Class represents a sentence as an array of tokens. 
 * @author czuk
 *
 */
class TakipiSentence{
	
	var $tokens = array();
	
	/**
	 * Add annotation to the sentence. 
	 */
	function addAnnotation($type, $from, $to=null){
		$to = $to===null ? $from : $to;
		$c = count($this->tokens);
		if ($from < 0 || $from >= $c || $to < 0 || $to >= $c ){
			throw new Exception("Token is out of sentence border, token=($from, $to), sentence=$c");
		}
		if (!isset($this->tokens[0]->channels[$type])){
			foreach ($this->tokens as &$t)
				$t->channels[$type] = "O";			
		}
		$this->tokens[$from]->channels[$type] = "B";
		for ($i=$from+1; $i<=$to; $i++)
			$this->tokens[$i]->channels[$type] = "I";
	}
}

/**
 * Class represents single token.
 */
class TakipiToken{
	var $orth = null;
	var $lex = array();
	var $ns = false;
	var $channels = array();
	
	function __construct($orth){
		$this->orth = $orth;
	}
	
	function addLex($base, $ctag, $disamb){
		$this->lex[] = new TakipiLex($base, $ctag, $disamb);
	}
	
	function setNS($flag){
		$this->ns = $flag;
	}
	
	function getDisamb(){
		foreach ($this->lex as $lex)
			if ($lex->disamb)
				return $lex;
		return null;
	}
	
}

/**
 * Class represents a single morphology interpretation.
 */
class TakipiLex{
	var $disamb = false;
	var $base = null;
	var $ctag = null;
	
	function __construct($base, $ctag, $disamb){
		$this->base = $base;
		$this->ctag = $ctag;
		$this->disamb = $disamb;
	}
	
	function getPos(){
		$p = strpos($this->ctag, ":");
		if ($p===false)
			return $this->ctag;
		else
			return substr($this->ctag, 0, $p);
	}
}

?>