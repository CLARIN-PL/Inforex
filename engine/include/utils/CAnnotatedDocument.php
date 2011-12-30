<?

/**
 * Uproszczona postać dokumentu. Dokument reprezentowany jest jako tablica
 * zdań, tablica anotacji i tablica relacji.  
 */
class AnnotatedDocument{

	var $name = null;	
	var $sentences = array();
	var $relations = array();

	function __construct($name, $sentences, $relations){
		$this->name = $name;
		$this->sentences = $sentences;
		$this->relations = $relations;
	}

	/**
	 * Wypisuje postać dokumentu na konsolę.
	 */
	function dump(){
		echo "DOCUMENT\n";
		echo "# META\n";
		echo "# SENTENCES\n";
		foreach ($this->sentences as $s){
			echo sprintf("# * [%3s] ", $s->id);
			foreach ($s->tokens as $t)
				echo ($t->ns ? "" : " ") . $t->orth;
			echo "\n#\n";
			if (count($s->annotations) != 0) { 
				foreach ($s->annotations as $a){
					echo sprintf("#   [%s] (%3d, %3d, %15s, %s)\n", 
							$a->getGlobalId(), $a->token_index_from, $a->token_index_to, $a->type, $a->text);
				}
			}
			echo "#\n";
		}
		
		echo "# RELATIONS";
		if (count($this->relations) == 0)
			echo " NONE\n";
		else{
			echo "\n";
			foreach ($this->relations as $r){
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

class AnnotatedDocumentSentence{
	
	/** Unikalny identyfikator zdania w obrębie dokumentu */
	var $id = null;
	
	var $tokens = null;
	
	var $annotations = array();
	
	function __construct($id, $tokens, $annotations){
		$this->id = $id;
		$this->tokens = $tokens;
		$this->annotations = $annotations;
	}
	
}

class AnnotatedDocumentToken{
	
	/** Unikalny identyfikator tokenu w obrębie zdania */
	var $id = null;
	
	var $orth = null;
	
	var $ns = null;
	
	function __construct($id, $orth, $ns){
		$this->id = $id;
		$this->orth = $orth;
		$this->ns = $ns;
	} 
	
}


class AnnotatedDocumentAnnotation{

	/** Unikalny identyfikator anotacji w obrębie zdania */
	var $id = null;
	
	/** Referencja na zdanie, w którym jest anotacja. */
	var $sentence = null;
	var $token_index_from = null;
	var $token_index_to = null;
	var $type = null;
	var $text = null;
	
	function __construct($id, &$sentence, $token_index_from, $token_index_to, $type, $text){
		$this->id = $id;
		$this->sentence = $sentence;
		$this->token_index_from = $token_index_from;
		$this->token_index_to = $token_index_to;
		$this->type = $type;
		$this->text = $text;
	}
	
	function getGlobalId(){
		return $this->sentence->id . "." . $this->id;
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
}

?>