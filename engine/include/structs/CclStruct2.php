<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class CclChannel {
	public $name = null; 
	public $value = null;
	
	function __construct($name, $value){
		$this->name = $name;
		$this->value = $value;
	}
	
	function getXml(){
		return "    <ann chan=\"{$this->name}\">{$this->value}</ann>\n";		
	}
}

class CclLexem {
	public $disamb = null;
	public $base = null;
	public $ctag = null;	
	function __construct($disamb, $base, $ctag){
		$this->disamb = $disamb;
		$this->base = htmlspecialchars($base);
		$this->ctag = $ctag;
	}
	
	function getXml(){
        //return "";
		$xml = $this->disamb ? "    <lex disamb=\"1\">\n" : "    <lex>\n";
		$xml .= "     <base>{$this->base}</base>\n";
		$xml .= "     <ctag>{$this->ctag}</ctag>\n";
		return $xml . "    </lex> \n";
	}
}

class CclToken {
	public $orth = null;
	public $lexemes = null;
	public $channels = null;
	public $ns = null;
	
	function __construct($orth){
		$this->orth = htmlspecialchars($orth);
		$this->lexemes = array();
		$this->channels = array();
		$this->ns = false;
	}
	
	function getXml($channelTypes){
		$xml =  "   <tok>\n";
		$xml .= "    <orth>{$this->orth}</orth>\n";
		foreach ($this->lexemes as $lexeme)
			$xml .= $lexeme->getXml();
		
		foreach ($channelTypes as $annType)
			$xml .= $this->channels[$annType]->getXml();
		if ($this->ns) return $xml . "   </tok>\n   <ns/>\n";	
		return $xml . "   </tok>\n";		
			
	}
}

class CclSentence {
	public $tokens = null;
	public $channelTypes = null;
	public $id = null;
	
	function __construct($id){
		$this->tokens = array();
		$this->channelTypes = array();
		$this->id = $id;
	}
		
	function getXml(){
		$usedTypes = array_keys($this->channelTypes);
		$xml = "  <sentence id=\"s{$this->id}\">\n";
		foreach ($this->tokens as $token)
			$xml .= $token->getXml($usedTypes);
		return $xml . "  </sentence>\n";
	}
}

class CclChunk {
	public $id = null;
	public $sentences = null;
	function __construct($id){
		$this->id = $id;
		$this->sentences = array();
	}
	
	function getXml(){
		$xml = " <chunk id=\"{$this->id}\">\n";
		foreach ($this->sentences as $sentence)
			$xml .= $sentence->getXml();		
		return $xml . " </chunk>\n";
	}
} 

class CclDocument {
	public $chunks = null;
	
	function __construct(){
		$this->chunks = array();
	}
	
	function getXml(){
		$xml = "<chunkList>\n";
		foreach ($this->chunks as $chunk)
			$xml .= $chunk->getXml();
		return $xml . "</chunkList>\n";
		
	}
	
}



?>
