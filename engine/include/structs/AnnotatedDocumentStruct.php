<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Uproszczona postać dokumentu. Dokument reprezentowany jest jako tablica
 * zdań, tablica anotacji i tablica relacji.  
 */
class AnnotatedDocument{

	var $id = null;
	var $name = null;	
	var $chunks = array();
	var $relations = array();

	function __construct($name){
		$this->name = $name;
	}

	function setId($id){
		$this->id = $id;
	}

	function addChunk($chunk){
		assert('$chunk instanceof AnnotatedDocumentChunk');
		$this->chunks[] = $chunk;
	}
	
	function addRelation($relation){
		assert('$relation instanceof AnnotatedDocumentRelation');
		$this->relations[] = $relation;		
	}

	function getChunks(){
		return $this->chunks;
	}
	
	function getRelations(){
		return $this->relations;
	}
	
	function getName(){
		return $this->name;
	}
	
	function getId(){
		return $this->id;
	}

	/**
	 * Wypisuje postać dokumentu na konsolę.
	 */
	function dump(){
		echo "DOCUMENT\n";
		echo "# META\n";
		echo "# SENTENCES\n";
		foreach ($this->getChunks() as $c)
			foreach ($c->getSentences() as $s){
				echo sprintf("# * [%3s] ", $s->id);
				foreach ($s->getTokens() as $t)
					echo ($t->ns ? "" : " ") . $t->orth;
				echo "\n#\n";
				if (count($s->getAnnotations()) != 0) { 
					foreach ($s->annotations as $a){
						echo sprintf("#   [%s] (%3d, %3d, %15s, %s)\n", 
								$a->getGlobalId(), $a->first, $a->last, $a->type, $a->text);
					}
				}
			echo "#\n";
		}
		
		echo "# RELATIONS";
		if (count($this->getRelations()) == 0)
			echo " NONE\n";
		else{
			echo "\n";
			foreach ($this->getRelations() as $r){
				echo sprintf("  * [%2d] %8s, %8s -> %s, (%s -> %s / %s -> %s)\n", 
						$r->id, $r->type, 
						$r->source->getGlobalId(), $r->target->getGlobalId(), 
						$r->source->text, 
						$r->target->text,
						$r->source->type,
						$r->target->type);
			}
		}
		echo "\n";
	}	
}

class AnnotatedDocumentChunk{
	
	var $sentences = array();
	
	function __construct(){
	}
	
	function addSentence($sentence){
		assert('$sentence instanceof AnnotatedDocumentSentence');
		$this->sentences[] = $sentence;
	} 
	
	function getSentences(){
		return $this->sentences;
	}
	
}

class AnnotatedDocumentSentence{
	
	/** Unikalny identyfikator zdania w obrębie dokumentu */
	var $id = null;
	
	var $tokens = null;
	
	var $annotations = array();
	
	function __construct($id){
		$this->id = $id;
	}

	function addToken($token){
		assert('$token instanceof AnnotatedDocumentToken');
		$this->tokens[] = $token;
	}	
	
	function addAnnotation($annotation){
		assert('$annotation instanceof AnnotatedDocumentAnnotation');
		$this->annotations[] = $annotation;		
	}
	
	function getTokens(){
		return $this->tokens;
	}
	
	function getAnnotations(){
		return $this->annotations;
	}
	
	function getId(){
		return $this->id;
	}
}

/**
 * Klasa reprezentuje token.
 */
class AnnotatedDocumentToken{
	
	/** Unikalny identyfikator tokenu w obrębie zdania */
	var $id = null;
	
	var $orth = null;
	
	var $ns = null;
	
	var $lexems = array();
	
	function __construct($id, $orth, $ns){
		$this->id = $id;
		$this->orth = $orth;
		$this->ns = $ns;
	} 	
	
	function addLexem(&$lexem){
		assert('$lexem instanceof AnnotatedDocumentLexem');
		$this->lexems[] = $lexem;
	}
	
	function getDisambLexem(){
		foreach ($this->lexems as &$lex)
			if ( $lex->getDisamb() === true )
				return $lex;
		return null;
	}
	
	function getId(){
		return $this->id;
	}

	function getLexems(){
		return $this->lexems;
	}
	
	function getNS(){
		return $this->ns;
	}
	
	function getOrth(){
		return $this->orth;
	}
}

/**
 * Klasa reprezentuje pojedynczy leksem przypięty do tokenu.
 */
class AnnotatedDocumentLexem{
	
	var $base = null;
	
	var $ctag = null;
	
	var $disamb = null;
	
	function __construct($base, $ctag, $disamb){
		$this->base = $base;
		$this->ctag = $ctag;
		$this->disamb = $disamb;
	}
	
	function getBase(){
		return $this->base;		
	}
	
	function getCtag(){
		return $this->ctag;
	}
	
	function getDisamb(){
		return $this->disamb;	
	}
	
	function dump(){
		echo sprintf("%10s %10s %d\n", $this->base, $this->ctag, $this->disamb);
	}
}

class AnnotatedDocumentAnnotation{

	/** Unikalny identyfikator anotacji w obrębie zdania */
	var $id = null;
	
	/** Referencja na zdanie, w którym jest anotacja. */
	var $sentence = null;
	var $first = null;
	var $last = null;
	var $type = null;
	var $text = null;
	
	function __construct($id, &$sentence, $token_index_from, $token_index_to, $type, $text){
		$this->id = $id;
		$this->sentence = $sentence;
		$this->first = $token_index_from;
		$this->last = $token_index_to;
		$this->type = $type;
		$this->text = $text;
	}
	
	function getGlobalId(){
		return $this->sentence->id . "." . $this->id;
	}
	
	function getFirstToken(){
		return $this->sentence->tokens[$this->first];
	}

	function getLastToken(){
		return $this->sentence->tokens[$this->last];
	}
	
	function getSentence(){
		return $this->sentence;
	}
	
	function getType(){
		return $this->type;
	}
}

/**
 * Klasa reprezentuje pojedynczą relację między anotacjami.
 */
class AnnotatedDocumentRelation{
	
	/** Unikalny identyfikator relacji w obrębie dokumentu */
	var $id = null;
	
	/** Referencja na anotację źródłową */
	var $source = null;
	
	/** Referencja na anotację docelową */
	var $target = null;
	
	/** Nazwa relacji */
	var $type = null;
	
	function __construct($id, &$annotation_source, &$annotation_target, $type){
		$this->id = $id;
		$this->source = $annotation_source;
		$this->target = $annotation_target;
		$this->type = $type;
	}
	
	function getSource(){
		return $this->source;
	}
	
	function getTarget(){
		return $this->target;
	}
	
	function getType(){
		return $this->type;
	}
}

?>