<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-05-12
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

class HtmlStr{
	function __construct($content){
		$this->content = $content;
		$this->n = 0; // Numer pozycji w tekście
		$this->m = 0; // Numer znaku z pominięciem tagów html
	}

	/**
	 * 
	 */	
	function insert($pos, $text, $begin=true){
		$this->moveTo($pos);
		if (!$begin)
			while ($this->skipTag()) {};
		$this->content = mb_substr($this->content, 0, $this->n) . $text . mb_substr($this->content, $this->n);	
	}
	
	/**
	 * Wstawia początek i koniec znacznika tak, aby znaczniki były na tym samym poziomie zagnieżdżenia.
	 */
	function insertTag($posBegin, $textBegin, $posEnd, $textEnd){
		$this->moveTo($posBegin);
		$begin_n = $this->n;
		$tag_stack = array();
		while ( $posEnd > $this->m ){
			while ($tag = $this->skipTag()) {
				if ($tag[strlen($tag)-1] == "/"){
					// pomiń tagi bez zamknięcia					
				}elseif ($tag[0] == "/"){
					// tag kończący, usuń ze stosu
					$pop = array_pop($tag_stack);
					if ($pop != trim($tag, "/")){
						ob_start();
						print_r($tag_stack);
						$stack = ob_get_clean();
						throw new Exception("Tag missmatch in insertTag() pop='$pop', tag='$tag', {$stack}");
					}					
				}else{
					$tag_stack[] = $tag;
				}
			};
			
			$this->n++;
			$this->m++;
		}

		// Dla tagów pozostałych na strosie zmodyfikuj wskaźnik na początek wstawiania		
		$end_n = $this->n;
		$this->n = $begin_n;
		foreach ($tag_stack as $tag){
			$this->skipTag();
		}
		$begin_n = $this->n;
				
		$this->content = mb_substr($this->content, 0, $end_n) . $textEnd . mb_substr($this->content, $end_n);			 
		$this->content = mb_substr($this->content, 0, $begin_n) . $textBegin . mb_substr($this->content, $begin_n);
		$this->n = $end_n + mb_strlen($textBegin) + mb_strlen($textEnd);	
	}
	
	/**
	 * Przesuń wskaźnik na wskazaną pozycję, na początek wszystkich anotacji.
	 */
	function moveTo($pos){
		if ($pos > $this->m){
			// Przesunięcie do przodu
			while ($pos > $this->m){
				while ( $this->skipTag() !== null ){}
				$this->m++;
				$this->n++;
				if ($this->m>mb_strlen($this->content))
					throw new Exception("Index m out of content");
			}
		}else{
			// Cofnięcie
			while ($pos < $this->m){
				while ( $this->skipTagBackward()) {};				
				$this->m--;
				$this->n--;
			}	
			while ( $this->skipTagBackward()) {}; 
		}
	}

	/**
	 * Jeżeli wskaźnik znajduje się na początku znacznika, to przeskakuje na pozycję za znacznikiem i zwraca jego nazwę.
	 * Operacja nie zmienia indeksu m.
	 * @return nazwa znacznika lub null 
	 */
	function skipTag(){
		if (mb_substr($this->content, $this->n, 1)=="<"){
			$this->n++;
			
			$tag_begin_pos = $this->n;
			$c = null;
			
			// Wczytaj nazwę tagu
			do{
				$this->n++;
				$c = mb_substr($this->content, $this->n, 1); 
			}while ( $c != ">" && $c != " " && $c != "#" );
			$tag_name = mb_substr($this->content, $tag_begin_pos, $this->n - $tag_begin_pos);

			// Wczytaj pozostałe atrybuty tagu
			while ( $c != ">" ){
				$this->n++;
				$c = mb_substr($this->content, $this->n, 1); 
			}
			$this->n++;
			return $tag_name;
		}else
			// na bieżącej pozycji nie ma znacznika
			return null;			
	}
	
	/**
	 * Cofnij tag do tyłu.
	 */
	function skipTagBackward(){
		if (mb_substr($this->content, $this->n-1, 1)==">"){
			do{
				$this->n--;
				$c = mb_substr($this->content, $this->n, 1); 
			}while ( $c != "<" );
			return true;			
		}
		return false;
	}
	
	/**
	 * Idź do wskazanej pozycji
	 */
	
	function getContent(){
		return $this->content;
	}
}

?>

