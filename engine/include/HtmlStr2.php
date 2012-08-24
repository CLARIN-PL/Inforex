<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-05-12
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

class HtmlStr2{
	
	var $ignore_whitespaces = false;
	var $content = null;
	/** Tablica z widocznymi znakami */
	var $chars = array();
	/** Tablica z niewidocznymi znakami (tagi, białe znaki) */
	var $tags = array();
	
	function __construct($content){
		$this->content = str_replace("\xc2\xa0", " ", $content);

		$h = new HtmlParser2($content);
		$os = $h->getObjects();
		
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
				$t = &new XmlTagPointer($o);
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

			if ( $j > count($this->tags[$from]) ){
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
	function insertTag($from, $tag_begin, $to, $tag_end){
		if ( $from < 0 || $from > count($this->chars))
			throw new Exception("Starting index out of char array.\n\nfrom=$from;\ncount(chars)=".count($this->chars));
		if ( $to < 0 || $to > count($this->chars))
			throw new Exception("Starting index out of char array.\n\nfrom=$from;\ncount(chars)=".count($this->chars));
		
		list($i, $j) = $this->_getInsertTagPositions($from, $to);
		
		if ( $this->_verifyConsistency($from, $i, $to, $j) !== true ){
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
			if ($i>$from)
				foreach ($this->tags[$i] as $t)
					if ( $t instanceof HtmlChar)
						$text .= $t->toString();
			$text .= $this->chars[$i]->toString();
		}
		return $text;
	}
}

class HtmlParser2{

	var $chars = array();
	var $n = 0;
		
	function __construct($content){

		$len = mb_strlen($content);
		$chars = array();
		for ($i=0; $i<$len; $i++){
			$ch = mb_substr($content, $i, 1, "UTF-8");
			$chars[] = $ch;
		}
		$this->chars = $chars;	
		$this->n = 0;		
	}
	
	function getChar(){
		$c = $this->chars[$this->n++];
		
		if ( $c == '&'){
			$cseq = $c; 
			$zn = '';
			$n = $this->n;
			if ($n < count($this->chars))
				do{
					$zn = $this->chars[$n++];
					$cseq .= $zn;
				}while ($n<count($this->chars) && (  ($zn >= 'a' && $zn <= 'z') 
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
		if ($this->n > count($this->chars)){
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
			}while ( $c != ">" && $c != " " && $c != "#" && $c != "/" );
			$tag .= $tag_name . $c;
			
			/* Wczytaj pozostałe atrybuty tagu */
			$lc = null;
			while ( $c != ">" ){
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

	function getObjects(){
		$elements = array();
		$this->n = 0;
		while ($this->n < count($this->chars)){
			$o = $this->getTag();
			if ( $o == null ){
				$o = new HtmlChar($this->getChar());
			}
			$elements[] = $o;
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

