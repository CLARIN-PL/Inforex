<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class HtmlStr2{
	
	var $ignore_whitespaces = false;
	var $content = null;
	/** Tablica z widocznymi znakami */
	var $chars = array();
	/** Tablica z niewidocznymi znakami (tagi, białe znaki) */
	var $tags = array();
	
	function __construct($content, $recognize_tags=true){
		$this->content = str_replace("\xc2\xa0", " ", $content);
		// Remove invisible control characters and unused code points			
		$this->content = preg_replace('/[\p{Cf}\p{Co}\p{Cs}\p{Cn}\x00-\x09\x11-\x1f]/u','',$this->content);

		// ToDo: Dla długich tekstów klasa HtmlStr2 zużywa strasznie dużo pamięci, nawet ponad 500MB.
		// Dopóki nie zostanie rozwiązany problem zużycia pamięci zostało wprowadzone ograniczenie na wielkość
		// obsługiwanych tekstów, tj. do 50k znaków.
		if ( strlen($this->content) > 50000 ){
			throw new Exception("Text too long to display (over 50k characters)");
		}		
		
		$h = new HtmlParser2($this->content);
		$os = $h->getObjects($recognize_tags);
		
		$chars = array();
		$tags = array();
		$stack = array();
		$hay = array();
		
		foreach ($os as $o){
			if ($o instanceof HtmlChar){
				$zn = $o->toString();
				if ( strlen( trim($zn)) > 0 ){
					$chars[] = $o;
					$tags[] = $stack;
					$stack = array();
				}
				else{
					$stack[] = $o;
				}
			}
			elseif( $o instanceof HtmlTag){
				$t = new XmlTagPointer($o);
				$stack[] =  $t;
				
				if ( $o->getType() == HTML_TAG_OPEN )
					$hay[] = array($t, count($chars));
				elseif ( $o->getType() == HTML_TAG_CLOSE ){
					list($tl, $index) = array_pop($hay);
					if ( $tl->getTag()->getName() != $t->getTag()->getName() )
						throw new Exception('Different tag names.');
						
					$tl->setIndex(count($chars));
					$t->setIndex($index);
				}
			}
		}
		$tags[] = $stack;
		
		$this->chars = $chars;
		$this->tags = $tags;
	}
	
	/**
	 * Get the position of opening and closing tags for given placements.
	 * The positions is a offset in the stack of elements.
	 */
	function _getInsertTagPositions($from, $to){
		
		/** Opening tag */
		$i = count($this->tags[$from]);
		if ( count($this->tags[$from]) > 0 ){ 
			while ($i > 0
					&& 
					   ( $this->tags[$from][$i-1] instanceof HtmlChar 
					     || $this->tags[$from][$i-1]->getTag()->getType() == HTML_TAG_SELF_CLOSE
					     || ( $this->tags[$from][$i-1]->getTag()->getType() == HTML_TAG_OPEN
					     		&& $this->tags[$from][$i-1]->getIndex() < $to )					     
					     || $this->tags[$from][$i-1]->getIndex() == $from
					   )
					){
				$i--;
			}
	
			while ($i < count($this->tags[$from])
					&& 
					   ( $this->tags[$from][$i] instanceof HtmlChar 
					     || $this->tags[$from][$i]->getTag()->getType() == HTML_TAG_SELF_CLOSE
					     || $this->tags[$from][$i]->getIndex() == $from
					   )
					){
				$i++;
			}
			
			if ( $i > count($this->tags[$from]) ){
				throw new Exception("Cannot insert the opening tag");
			}
		}

		/** Closing tag */
		$j = 0;
		if ( count($this->tags[$to]) > 0 ) {
			while ($j < count($this->tags[$to])
					&& 
					   ( $this->tags[$to][$j] instanceof HtmlChar 
					     || $this->tags[$to][$j]->getTag()->getType() == HTML_TAG_SELF_CLOSE
					     || ( $this->tags[$to][$j]->getTag()->getType() == HTML_TAG_CLOSE 
					     		&& $this->tags[$to][$j]->getIndex() > $from)
					     || $this->tags[$to][$j]->getIndex() == $to
					   )
					){
				$j++;
			}
		
			while ($j >0
					&& 
					   ( $this->tags[$to][$j-1] instanceof HtmlChar 
					     || $this->tags[$to][$j-1]->getTag()->getType() == HTML_TAG_SELF_CLOSE
					     || $this->tags[$to][$j-1]->getIndex() == $to
					   )
					){
				$j--;
			}

			if ( $j > count($this->tags[$to]) ){
				throw new Exception("Cannot insert the closing tag");
			}
		}

		return array($i, $j);		
	}
	
	/**
	 * Verify tags consistency between given positions.
	 * Include: pairs of opening/closing tags.
	 */
	function _verifyConsistency($from, $fi, $to, $ti){
		$tags = array_slice($this->tags[$from], $fi);
		for ($i=$from+1; $i<$to; $i++)
			$tags = array_merge($tags, $this->tags[$i]);
		$tags = array_merge($tags, array_slice($this->tags[$to], 0, $ti));
		foreach ($tags as $e){
			if ( $e instanceof XmlTagPointer
					&& $e->getTag()->getType() != HTML_TAG_SELF_CLOSE 
					&& ( $e->getIndex() > $to || $e->getIndex() < $from )
					)
				return $e;
		}
		return true;
	}
	
	/**
	 * Insert pair of opening and closing tags into XML document.
	 */
	function insertTag($from, $tag_begin, $to, $tag_end, $force_insert=FALSE){
		if ( $from < 0 || $from > count($this->chars))
			throw new Exception("Starting index out of char array.\n\nfrom=$from;\ncount(chars)=".count($this->chars));
		if ( $to < 0 || $to > count($this->chars))
			throw new Exception("Starting index out of char array.\n\nfrom=$from;\ncount(chars)=".count($this->chars));
		
		list($i, $j) = $this->_getInsertTagPositions($from, $to);
		
		if ( !$force_insert && $this->_verifyConsistency($from, $i, $to, $j) !== true ){
			throw new Exception(sprintf("Annotation %s is crossing existing annotation", $tag_begin));
		}
		
		$xot = new XmlTagPointer(new HtmlTag("x", HTML_TAG_OPEN, $tag_begin));
		$xot->setIndex($to);
		$xct = new XmlTagPointer(new HtmlTag("x", HTML_TAG_CLOSE, $tag_end));
		$xct->setIndex($from);
		
		array_splice($this->tags[$from], $i, 0, array($xot));
		array_splice($this->tags[$to], $j, 0, array($xct));
	}

	function getContent(){
		$strs = array();
		for ($i=0; $i<count($this->chars); $i++){
			for ($j=0; $j<count($this->tags[$i]); $j++)
				$strs[] = $this->tags[$i][$j]->toString();
			$strs[] = $this->chars[$i]->toString();
		}
		for ($j=0; $j<count($this->tags[$i]); $j++)
			$strs[] = $this->tags[$i][$j]->toString();
		return implode($strs);
	}
	
	function getText($from, $to){
		$text = "";
		for ($i=$from; $i<=$to; $i++){
            if ($i > $from) {
                if (is_array($this->tags[$i])) {
                    foreach ($this->tags[$i] as $t) {
                        if ($t instanceof HtmlChar) {
                            $text .= $t->toString();
                        }
                    }
                }
            }
            if (is_object($this->chars[$i])) {
                $text .= $this->chars[$i]->toString();
            }
		}
		return $text;
	}
	
	/**
	 * Return text for given range of visible characters.
	 * @param $from Index of first visible character.
	 * @param $to Index of last visible character.
	 * @param $align_left Align from left to a continous sequence of characters.
	 * @param $align_right Align from right to a continous sequence of characters.
	 * @param $keep_tags Include xml tags.
	 */
	function getTextAlign($from, $to, $align_left, $align_right, $keep_tags=false){
		$text = "";
		while ( $align_left && $from > 0 && count($this->tags[$from]) == 0){
			$from--;
		}
		while ( $align_right && $to+1 < count($this->tags) && count($this->tags[$to+1]) == 0){
			$to++;
		}
		for ($i=$from; $i<=$to; $i++){
            if ($i > $from) {
                if (is_array($this->tags[$i])) {
                    foreach ($this->tags[$i] as $t) {
                        if ($t instanceof HtmlChar || $keep_tags) {
                            $text .= $t->toString();
                        }
                    }
                }
            }
            if (is_object($this->chars[$i])) {
                $text .= $this->chars[$i]->toString();
            }
		}
		return $text;
	}	
	
	function getSentencesPositions(){
		$positions = array();
		
		$i = 0; // current position
		while($i <= count($this->chars)){
			if (is_array($this->tags[$i])) {
				foreach ($this->tags[$i] as $t) {
					if ( $t instanceof XmlTagPointer && $t->tag instanceof HtmlTag	&& $t->tag->name === 'sentence' && $t->tag->type == HTML_TAG_OPEN) {
						$positions[] = $i;
					}
				}
			}
			$i++;
		}
		
		
		return $positions;
	}
	
	function getSentencePos($pos_in_sentence){
        $sentence_begin = -1;
        $i=$pos_in_sentence;
        while ($i >= 0 && $sentence_begin === -1) {
            if (is_array($this->tags[$i])) {
                foreach ($this->tags[$i] as $t) {
                    if ( $t instanceof XmlTagPointer && $t->tag instanceof HtmlTag 
                            && $t->tag->name === 'sentence' && $t->tag->type == 1) {
                        $sentence_begin = $i;
                    }
                }
            }
            $i--;
        }
        
        $sentence_end = -1;
        $i=$pos_in_sentence+1;
        while ($i <= count($this->chars) && $sentence_end === -1) {
            if (is_array($this->tags[$i])) {
                foreach ($this->tags[$i] as $t) {
                    if ( $t instanceof XmlTagPointer && $t->tag instanceof HtmlTag 
                            && $t->tag->name === 'sentence' && $t->tag->type == 2) {
                        $sentence_end = $i;
                    }
                }
            }
            $i++;
        }
		if ($sentence_begin !== -1 && $sentence_end !== -1) {
                    $return = array($sentence_begin, $sentence_end-1);
                } else {
                    $return = array(-1, -1);
                }
		return $return;
	}
	
	function getCharNumberBetweenPositions($pos1, $pos2){
		return mb_strlen($this->getText($pos1, $pos2));
	}
	
	function getSentence($pos_in_sentence){
        list($sentence_begin, $sentence_end) = $this->getSentencePos($pos_in_sentence);
        $text = '';
		if ($sentence_begin !== -1 && $sentence_end !== -1) {
                    $text = $this->getText($sentence_begin, $sentence_end);
                }
		return $text;
	}
	
	function isSpaceAfter($pos){
		if ( $pos + 1 < count($this->tags) )
			foreach ($this->tags[$pos+1] as $tag)
				if ( $tag instanceof HtmlChar)
					return true;
		return false;
	}
}

class HtmlParser2{

	var $chars = array();
	var $n = 0;
		
	function __construct(&$content){
/*// For older version of PHP < 5.3
		$len = mb_strlen($content);
		$chars = array();
		for ($i=0; $i<$len; $i++){
			$ch = mb_substr($content, $i, 1, "UTF-8");
			$chars[] = $ch;
		}
		$this->chars = $chars;
*/
		// The solution below is faster but it does not work under PHP 5.2.6
        // due a bug which was fixed in 5.3		
		$this->chars = preg_split('//u', $content, -1); 	
		$this->len = count($this->chars);
		$this->n = 0;	
	}
	
	function len(){
		return $this->len;
	}
	
	function getChar(){
		$c = $this->chars[$this->n++];
		
		if ( $c == '&'){
			$cseq = $c; 
			$zn = '';
			$n = $this->n;
			if ($n < $this->len())
				do{
					$zn = $this->chars[$n++];
					$cseq .= $zn;
				}while ($n<$this->len() && (  ($zn >= 'a' && $zn <= 'z') 
										|| ($zn >= 'A' && $zn <= 'Z') 
										|| ($zn >= '0' && $zn <= '9')
										|| $zn == '#' ) );			
			// Zakończenie encji HTML
			if ($zn == ';') {
				$c = $cseq;
				$this->n = $n;
			}						
		}
		
		return $c;	
	}
	
	function getTag(){
		if ($this->n > $this->len()){
			throw new Exception("Index out of array bound (this->n={$this->n})");
		}
		
		if ( $this->chars[$this->n] == "<" ) {
			$tag = "<";
			$type = HTML_TAG_OPEN;
			$tag_name = null;
			$n_revert = $this->n;			
			
			if ( $this->chars[$this->n+1] == "/" ){
				$type = HTML_TAG_CLOSE;
				$tag .= "/";
				$this->n++;
			}
			
			/* Wczytaj nazwę tagu */
			do{
				$this->n++;				
				$c =$this->chars[$this->n];
				if ( $c != ">" && $c != " " && $c != "#" && $c != "/" )
					$tag_name .= $c;				 
			}while ( $this->n < $this->len() && $c != ">" && $c != " " && $c != "#" && $c != "/" );
			$tag .= $tag_name . $c;
			
			/* Wczytaj pozostałe atrybuty tagu */
			$lc = null;
			while ( $this->n < $this->len() && $c != ">" ){
				$this->n++;
				$lc = $c;
				$c = $this->chars[$this->n];
				$tag .= $c; 
			}
			if ($lc == "/")
				$type = HTML_TAG_SELF_CLOSE;
			$this->n++;

			return new HtmlTag($tag_name, $type, $tag);			
		}
		else
			return null;			
	}

	function getObjects($recognize_tags){		
		$elements = array();
		$this->n = 0;
		
		if ( $recognize_tags){
			while ($this->n < $this->len()){
				$o = $this->getTag();
				if ( $o == null ){
					$o = new HtmlChar($this->getChar());
				}
				$elements[] = $o;
			}			
		}
		else{
			while ($this->n < $this->len()){
				$elements[] = new HtmlChar($this->getChar());
			}						
		}
		return $elements;
	}	
	
}

class HtmlChar{
	
	var $c = null;
	
	function __construct($c){
		$this->c = $c;
	}	
	
	function toString(){
//              wgawel: Dekodowanie encji - potrzebne do prawidłowego liczenia
//                      długości ciągów znaków np. przy wyszukiwaniu.
//              czuk:   Użucie html_entity_decode w tym miejscu nie jest uzasadnione,
//                      tym bardziej, że w tej postaci psuje kodowanie znaków.
//		return html_entity_decode($this->c, ENT_XML1 | ENT_QUOTES);
		return $this->c;
	}
}

define ("HTML_TAG_OPEN", '1');
define ("HTML_TAG_CLOSE", '2');
define ("HTML_TAG_SELF_CLOSE", '3');

class HtmlTag{
	
	var $name = null;
	var $type = null;
	var $str = null;
	
	function __construct($name, $type, $str){
		$this->name = $name;
		$this->type = $type;
		$this->str = $str;	
	}

	function toString(){
		return $this->str;
	}
	
	function getName(){
		return $this->name;
	}
	
	function getType(){
		return $this->type;
	}
}

/**
 * 
 */
class XmlTagPointer{
	
	var $tag = null;
	/** Indeks znaku przed którym występuje powiązany tag. */
	var $index = null;
	
	function __construct($tag){
		$this->tag = $tag;
	}
	
	function getTag(){
		return $this->tag;
	}
	
	function setIndex($index){
		$this->index = $index;
	}
	
	function getIndex(){
		return $this->index;
	}
	
	function toString(){
		return $this->tag->toString();
	}
}

?>

