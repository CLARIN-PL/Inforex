<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-03-25
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

mb_internal_encoding("UTF-8");
 
class HtmlStr{
	
	var $index = array();
	var $buffor = array();
	var $index_rebuild_required = true;
	var $flush_required = true;
	
	function __construct($content){
		$this->content = $content;
		$this->n = 0; // Numer pozycji w tekście
		$this->m = 0; // Numer znaku z pominięciem tagów html
	}
	
	function insert($pos, $text, $begin=true){
		if ($this->m>$pos){
			$this->n = 0;
			$this->m = 0;
		}		
		$hold_count = false;
		while ($this->m<$pos && $this->n<mb_strlen($this->content)){
			if (mb_substr($this->content, $this->n, 1)=="<") $hold_count = true;
			else if (mb_substr($this->content, $this->n, 1)==">") $hold_count = false;
			else if (!$hold_count) $this->m++;
			$this->n++;
		}
		// Jeżeli wstawiamy początek, to pomijamy tagi, jak koniec, to nie pomijamy.
		if ($begin && mb_substr($this->content, $this->n, 1)=="<"){
			do{
				$this->n++;
			}while (mb_substr($this->content, $this->n, 1)!=">");
			$this->n++;
		}
		$this->content = mb_substr($this->content, 0, $this->n) . $text . mb_substr($this->content, $this->n);	
	}
	
	function getContent(){
		if ($this->flush_required)
			$this->flush();
		return $this->content;
	}
	
	/**
	 * Index characters outside the XML tags.
	 */
	function makeIndex(){
		$this->index = array();
		$hold_count = false;
		$chars = $this->split_preg($this->content);
		$content_length = count($chars);		
		
		for ($n=1; $n < $content_length; $n++ ) {
			$c = $chars[$n];
			if ( $c == "<" ) $hold_count = true;
			else if ( $c == ">" ) $hold_count = false;
			else if (!$hold_count) {
				$this->index[] = $n-1;	
			}
		}
		$this->index_rebuild_required = false;
	}
	
	function split_preg($str){
		return preg_split('//u', $str, -1);		
	}

	/**
	 * Add insertion to the queue.
	 */
	function insertBuffered($pos, $text, $begin=true){
		if ($this->index_rebuild_required)
			$this->makeIndex();
		
		$n = $this->index[$pos];
		
		if ($begin){
			$current = isset($this->buffor[$n]) ? $this->buffor[$n] : array();
			$this->buffor[$n] = array_merge(array($text), $current);		
		}else
			$this->buffor[$n][] = $text;
	}
	
	/**
	 * Apply all insertions.
	 */
	function flush(){
		$str = "";
		$prev = 0;
		ksort($this->buffor);
		foreach ($this->buffor as $index=>$texts){
			$str .= mb_substr($this->content, $prev, $index-$prev);
			$str .= implode($texts);
			$prev = $index;
		}
		$str .= mb_substr($this->content, $prev); 
		$this->content = $str;
		
		$this->flush_required = false;
		$this->index_rebuild_required = true;
	}
}

?>
