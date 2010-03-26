<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-03-25
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
class HtmlStr{
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
			//echo mb_substr($this->content, $this->n, 1);
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
		return $this->content;
	}
}

?>
