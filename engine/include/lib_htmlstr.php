<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-05-12
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

class HtmlStr{
	
	var $ignore_whitespaces = false;
	
	function __construct($content, $ignore_whitespaces = true){
		$this->content = $content;
		$this->n = 0; // Numer pozycji w tekście
		$this->m = 0; // Numer znaku z pominięciem tagów html i białych znaków
		$this->ignore_whitespaces = $ignore_whitespaces;
	}

	/**
	 * 
	 */	
	function insert($pos, $text, $begin=true, $end=true, $whitespace=true){
		$this->moveTo($pos);
		if (!$begin)
			while ($this->skipTag($begin, $end)) {};
		
		if (!$whitespace){
			
			/** Rewind trailing white spaces */	
			while ($this->n>0 && mb_substr($this->content, $this->n-1, 1) == " ") { $this->n--; }
		}
		$this->content = mb_substr($this->content, 0, $this->n) . $text . mb_substr($this->content, $this->n);	
	}
	
	/**
	 * Wstawia początek i koniec znacznika tak, aby znaczniki były na tym samym poziomie zagnieżdżenia.
	 */
	function insertTag($posBegin, $textBegin, $posEnd, $textEnd, $skipOpenning=false){

		$begin_n = null;	//
		$tag_stack = array();	// stos na nazwy napotkanych tagów

		$this->moveTo($posBegin);
		
		/* Omiń wszystkie znaczniki zamykające i samozamykające */		
		while ( $this->skipTag($skipOpenning, true, true) !== null );

		$begin_n = $this->n;

		while ( $posEnd > $this->m ){
			while ($tag = $this->skipTag()) {
				if ($tag[strlen($tag)-1] == "/"){
					
					// pomiń tagi bez zamknięcia					
				}else if ($tag[0] == "/"){
					
					// tag kończący, usuń ze stosu
					$pop = array_pop($tag_stack);
					if ($pop != trim($tag, "/")){
						ob_start();
						print_r($tag_stack);
						echo "\n";
						//echo "<pre style='white-spaces: wrap'>";
						echo  $this->content;
						//echo "</pre>";
						$stack = ob_get_clean();
						throw new Exception("Tag missmatch in insertTag()" .
								" pop='$pop', " .
								" tag='$tag'," .
								" posBegin='$posBegin'," .
								" textBegin='". htmlentities($textBegin)."'," .
								" posEnd='$posEnd'," .
								" textEnd=". htmlentities($textEnd).", " .
								" m='{$this->m}', {$stack}");
					}					
				}else{
					$tag_stack[] = $tag;
				}
			};
			// Licz tylko widoczne znaki (bez białych znaków)
			$this->consumeCharacter();
		}

		// Dla tagów pozostałych na strosie zmodyfikuj wskaźnik na początek wstawiania		
		$end_n = $this->n;
		$this->n = $begin_n;
		foreach ($tag_stack as $tag){
			if ($this->ignore_whitespaces)
				$this->skipWhitespaces();
			$this->skipTag();
		}

		if ($this->ignore_whitespaces)
			$this->skipWhitespaces();
					
		$begin_n = $this->n;
				
		$this->content = mb_substr($this->content, 0, $end_n) . $textEnd . mb_substr($this->content, $end_n);			 
		$this->content = mb_substr($this->content, 0, $begin_n) . $textBegin . mb_substr($this->content, $begin_n);
		$this->n = $end_n + mb_strlen($textBegin) + mb_strlen($textEnd);
		
	}
	
	/**
	 * Przesuń wskaźnik na wskazaną pozycję, na początek wszystkich anotacji.
	 */
	function moveTo($pos){
		if ( $pos < $this->m ){
			// Zresetuje i szukaj od początku
			$this->n = 0;
			$this->m = 0;
		}
		if ($pos > $this->m){
			// Przesunięcie do przodu
			while ($pos > $this->m){
				while ( $this->skipTag() !== null ){}
				$zn = $this->consumeCharacter();
				if ($zn == "")
					throw new Exception("Position out of content");
				if ( $this->m > mb_strlen($this->content) )
					throw new Exception("Index m out of content");
			}
		}
	}

	/**
	 * Jeżeli wskaźnik znajduje się na początku znacznika, to przeskakuje na pozycję za znacznikiem i zwraca jego nazwę.
	 * Operacja nie zmienia indeksu m.
	 * @param $opening -- czy pominąć tag otwierający
	 * @param $closing -- czy pominąć tag zamykający
	 * @return nazwa znacznika lub null 
	 */
	function skipTag($opening=true, $closing=true, $selfclosing=true, $whitespace=true){
		if ($this->n > mb_strlen($this->content)){
			throw new Exception("Out of content");
		}
		
		if ($whitespace)
			$this->skipWhitespaces();
		
		if ( mb_substr($this->content, $this->n, 1) == "<" ) {
			
			$tag_name = null;
			$n_backup = $this->n++;			
			$tag_begin_pos = $this->n;
			$c = null;
			
			/* Wczytaj nazwę tagu */
			do{
				$this->n++;
				$c = mb_substr($this->content, $this->n, 1); 
			}while ( $c != ">" && $c != " " && $c != "#" && $c != "/" );
			$tag_name = mb_substr($this->content, $tag_begin_pos, $this->n - $tag_begin_pos);

			/* Wczytaj pozostałe atrybuty tagu */
			$cp = null;
			while ( $c != ">" ){
				$cp = $c;
				$this->n++;
				$c = mb_substr($this->content, $this->n, 1); 
			}
			
			if ( ($opening && $tag_name[0] != "/" && $cp != "/") 
					|| ($closing && $tag_name[0] == "/" ) 
					|| ($selfclosing && $cp == "/") ){
				$this->n++;
				return $tag_name;			
			}
			else{
				$this->n = $n_backup;
				return null;
			}
			
			$this->n++;
			return $tag_name;
		}else{

			/* na bieżącej pozycji nie ma tagu */
			return null;
		}			
	}
	
	function getContent(){
		return $this->content;
	}

	/**
	 * Pomiń białe znaki
	 */	
	function skipWhitespaces(){
		$len = mb_strlen($this->content);
		while ($this->n < $len && trim(mb_substr($this->content, $this->n, 1))=='')
			$this->n++;		
	}
	
	/**
	 * Zwraca tekst między wskazanymi indeksami znaków
	 */
	function getText($from, $to){
		$this->moveTo($from);
		$this->skipWhitespaces();		
		$text = "";
		if ($to){
			while ($this->m <= $to){
				while ($this->skipTag(true, true, true, false) != null ) {}
				$text .= $this->consumeCharacter();
			}
		}
		else {
			while ($this->n<mb_strlen($this->content)-1 ) {
				while ($this->skipTag(true, true, true, false) != null ) {}
				$text .= $this->consumeCharacter();
			}
		}
		return trim($text);
	}
	
	/**
	 * Pobiera aktualny znak i przechodzi do następnego. Encje html traktowane są jako pojedyncze znaki.
	 */
	function consumeCharacter(){
		$n = $this->n;
		$len = mb_strlen($this->content);
		
		if (mb_substr($this->content, $n, 1) == '&'){
			$zn = '';
			$n++;
			if ($n < $len)
				do{
					$zn = mb_substr($this->content, $n, 1);
					$n++;
				}while ($n<$len && (  ($zn >= 'a' && $zn <= 'z') 
										|| ($zn >= 'A' && $zn <= 'Z') 
										|| ($zn >= '0' && $zn <= '9')
										|| $zn == '#' ) );
			
			// Zakończenie encji HTML
			if ($zn == ';') {
				$start = $this->n;
				$this->m++;
				$this->n = $n;
				return html_entity_decode(mb_substr($this->content, $start, $this->n-$start));		
			}else{
				$n = $this->n;
			}						
		}
		
		$zn = mb_substr($this->content, $n, 1); 
		if (!$this->ignore_whitespaces || trim($zn)!=''){
			$this->m++;
		}
		$this->n++;
		return $zn;
	}
	
}

?>

