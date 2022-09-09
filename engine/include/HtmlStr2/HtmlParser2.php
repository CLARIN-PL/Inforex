<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class HtmlParser2 implements IHtmlParser2 {

	private $chars = array();
	private $n = 0;
    private $len;
		
	public function __construct($content){
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
	
	private function getLen(){
		return $this->len;
	}
	
	private function getChar(){
		$c = $this->chars[$this->n++];
		
		if ( $c == '&'){
			$cseq = $c; 
			$zn = '';
			$n = $this->n;
			if ($n < $this->getLen())
				do{
					$zn = $this->chars[$n++];
					$cseq .= $zn;
				}while ($n<$this->getLen() && (  ($zn >= 'a' && $zn <= 'z') 
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
	
	private function getTag(){
		if ($this->n > $this->getLen()){
			throw new Exception("Index out of array bound (this->n={$this->n})");
		}
		
		if ( $this->chars[$this->n] == "<" ) {
			$tag = "<";
			$type = IHtmlTag::HTML_TAG_OPEN;
			$tag_name = null;
			$n_revert = $this->n;			
			
			if ( $this->chars[$this->n+1] == "/" ){
				$type = IHtmlTag::HTML_TAG_CLOSE;
				$tag .= "/";
				$this->n++;
			}
			
			/* Wczytaj nazwę tagu */
			do{
				$this->n++;				
				$c =$this->chars[$this->n];
				if ( $c != ">" && $c != " " && $c != "#" && $c != "/" )
					$tag_name .= $c;				 
			}while ( $this->n < $this->getLen() && $c != ">" && $c != " " && $c != "#" && $c != "/" );
			$tag .= $tag_name . $c;
			
			/* Wczytaj pozostałe atrybuty tagu */
			$lc = null;
			while ( $this->n < $this->getLen() && $c != ">" ){
				$this->n++;
				$lc = $c;
				$c = $this->chars[$this->n];
				$tag .= $c; 
			}
			if ($lc == "/")
				$type = IHtmlTag::HTML_TAG_SELF_CLOSE;
			$this->n++;

			return new HtmlTag($tag_name, $type, $tag);			
		}
		else
			return null;			
	}

	public function getObjects($recognize_tags){		
		$elements = array();
		$this->n = 0;
		
		if ( $recognize_tags){
			while ($this->n < $this->getLen()){
				$o = $this->getTag();
				if ( $o == null ){
					$o = new HtmlChar($this->getChar());
				}
				$elements[] = $o;
			}			
		}
		else{
			while ($this->n < $this->getLen()){
				$elements[] = new HtmlChar($this->getChar());
			}						
		}
		return $elements;
	}	
	
}

?>
