<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class HtmlStr{
	
	var $ignore_whitespaces = false;
	
	function __construct($content, $ignore_whitespaces = true){
		$this->content = str_replace("\xc2\xa0", " ", $content);
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

	function insertTagMulti($posBegin, $textBegin, $posEnd, $textEnd){

		$begin_n = null;	//
		$tag_stack = array();	// stos na nazwy napotkanych tagów

		/* Przejdź do początku anotacji */
		$this->moveTo($posBegin);
		
		/* Upewnij się, że wskaźnik jest przed wszystkimi tagami */
		while ( ($tag = $this->skipTagBackward(true, true, true, true)) !== null ) {};
		
		/* Pomiń tagi zamykające i samozamykające */
		while ( ($tag = $this->skipTag(false, true, true, true)) !== null ) {};
		
		/* Odczytaj wszystkie tagi znajdujące się przed wskazaną pozycją */
		while ( ($tag = $this->skipTag(true, true, true, true)) !== null ) {
			if ($tag[strlen($tag)-1] == "/"){
				echo "pomiń samozamykający\n";
			}
			else if ($tag[0] == "/"){
				array_pop($tag_stack);
			}
			else{
				array_push($tag_stack, array($tag, $this->m));
			}
		};
		
		print_r($tag_stack);
		
		while ( $posEnd > $this->m ){
			while ($tag = $this->skipTag(true, true, true, true)){
				echo "{$tag}\n";
			 
				if ($tag[strlen($tag)-1] == "/"){
					echo "pomiń samozamykający\n";
				}
				else if ($tag[0] == "/"){
					if ( count($tag_stack) == 0)
						echo "break\n";
					else
						array_pop($tag_stack);
				}
				else{
					array_push($tag_stack, array($tag, $this->m));
				}
			}
			
			$this->consumeCharacter();
		}

		print_r($tag_stack);
		
	}
	
	/**
	 * Wstawia początek i koniec znacznika tak, aby znaczniki były na tym samym poziomie zagnieżdżenia.
	 */
	function insertTag($posBegin, $textBegin, $posEnd, $textEnd, $skipOpenning=false){

		$tag_stack = array();	// stos na nazwy napotkanych tagów

		$this->moveTo($posBegin);
		$this->skipTagBackward(true, true, true, true);
		$this->skipWhitespaces();
		$this->skipTag(false, true, true, true);
		$this->skipWhitespaces();
		
		$stack = array(array("BOM", $this->n));
		while ( ($tag = $this->skipTag(true, true, true, true)) != null ){
			$this->skipWhitespaces();
			if ( $tag[mb_strlen($tag)-1] != "/" ){
				$popped = false;
				if ( count($stack) > 0 && false){
					$last = $stack[count($stack)-1];
					if ( "/" . $last[0] == $tag){
						$popped = true;
						array_pop($stack);
					}
				}				
				if (!$popped)
					$stack[] = array($tag, $this->n);
			}
		}
		
		while ( $posEnd > $this->m ){
			while ($tag = $this->skipTag()) {
				if ($tag[strlen($tag)-1] == "/"){							
					// pomiń tagi bez zamknięcia					
				}else if ($tag[0] == "/"){
					
					/* Jeżeli są tagi wewnątrzne */
					if ( count($tag_stack) > 0 ){
						$pop = array_pop($tag_stack);
						if ($pop != mb_trim($tag, "/")){
							throw new Exception("Tag missmatch in insertTag()" .
								" pop='$pop', " .
								" tag='$tag'," .
								" posBegin='$posBegin'," .
								" textBegin='". htmlentities($textBegin)."'," .
								" posEnd='$posEnd'," .
								" textEnd=". htmlentities($textEnd).", " .
								" m='{$this->m}', {$stack}");
						}						
					}
					/* Trzeba cofnąć początek tagu */
					else{
						$pop = array_pop($stack);
						$pop = $pop[0];
						if ( $pop != mb_trim($tag, "/") )
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

		$begin_tag = array_pop($stack);
		$begin_n = $begin_tag[1];
		$end_n = $this->n;
		
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
				$zn = $this->consumeCharacter();
				if ($zn == "")
					throw new Exception("Position out of content");
				if ( $this->m > mb_strlen($this->content) )
					throw new Exception("Index m out of content");
			}
		}
		else if ( $pos < $this->m ){
			while ($pos < $this->m ){
				while ( $this->skipTagBackward() != null ) {}
				$zn = $this->consumeCharacterBackward();
				if ($zn == "")
					throw new Exception("Position out of content");
				if ( $this->m < 0 )
					throw new Exception("Index m out of content");
			}
		}
	}

	/**
	 * Jeżeli wskaźnik znajduje się na początku znacznika, to przeskakuje na pozycję 
	 * za znacznikiem i zwraca jego nazwę. Operacja nie zmienia indeksu m.
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
			/* Jeżeli jest to tag samozamykający się, to doklej / to nazwy */
			if ($c == "/") $tag_name .= "/";

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
	
	/**
	 * Jeżeli wskaźnik znajduje się na końcu znacznika, to przeskakuje na pozycję
	 * przed znacznikiem i zwraca jego nazwę. Operacja nie zmienia indeksu m.
	 * @return nazwa znacznika lub null 
	 */
	function skipTagBackward($opening=true, $closing=true, $selfclosing=true, $whitespace=true){

		/* Wskaźnik znaku jest na początku, więc nie ma gdzie się cofać. */
		if ($this->n == 0 ) return null;

		/* Wskaźnik znaku jest przed początkową pozycją, co jest niedozwolone. */
		if ($this->n < 0 ) throw new Exception("Out of content");		
		
		if ($whitespace)
			$this->skipWhitespacesBackward();
		
		if ( mb_substr($this->content, $this->n-1, 1) == ">" ) {

			/* Przewiń do potencjalnego początku tagu */
			$n_backup = $this->n;
			while ( $this->n >= 0 && mb_substr($this->content, $this->n-1, 1) != '<' ){
				$this->n--;
			}
			/* Jeżeli indeks znaków wyszedł poza zakreś, to nie było znacznika. */
			if ($this->n < 0){
				$this->n = $n_backup;
				return null;
			}
									
			$tag_name = null;
			$n_tag_start = $this->n; // Pozycja znaku przed tagiem
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
				$this->n = $n_tag_start-1;
				return $tag_name;			
			}
			else{
				$this->n = $n_backup;
				return null;
			}
			
			$this->n = $n_tag_start;
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
		while ($this->n < $len && mb_trim(mb_substr($this->content, $this->n, 1))=='')
			$this->n++;		
	}

	/**
	 * Pomiń białe znaki od tyłu
	 */	
	function skipWhitespacesBackward(){
		while ($this->n >= 0 && mb_trim(mb_substr($this->content, $this->n-1, 1))=='')
			$this->n--;		
	}
	
	/**
	 * Zwraca tekst między wskazanymi indeksami znaków
	 */
	function getText($from, $to){
		$this->moveTo($from);
		$this->skipWhitespaces();		
		$text = "";
		if ($to!=null){
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
		return mb_trim($text);
	}
	
	/**
	 * Pobiera aktualny znak i przechodzi do następnego. Encje html traktowane są jako pojedyncze znaki,
	 * ale zwracane są w oryginalnej postaci, tj. encji, np. &amp; Jeżeli w tekście wystąpi znak & nie zakodowany
	 * jako encja, to w dokładnie takiej samej postaci zostanie zwrócony (&, a nie jako encja).
	 * 
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
				return 	mb_substr($this->content, $start, $this->n-$start);
			}else{
				$n = $this->n;
			}						
		}
		
		$zn = mb_substr($this->content, $n, 1); 
		if (!$this->ignore_whitespaces || mb_trim($zn)!=''){
			$this->m++;
		}
		$this->n++;
		return $zn;
	}
	
		/**
	 * Pobiera aktualny znak i przechodzi do następnego. Encje html traktowane są jako pojedyncze znaki.
	 */
	function consumeCharacterBackward(){

		if ($this->n == 0)
			return null;
		
		$n = $this->n;
		$c = mb_substr($this->content, $n-1, 1);
		
		/* Jeżeli jest średnik, to możemy mieć do czynienia z encją HTML */
		if ( $c == ';'){
			$zn = '';
			$n--;
			if ($n > 0)
				do{
					$zn = mb_substr($this->content, $n-1, 1);
					$n--;
				}while ( $n>=0 && (  ($zn >= 'a' && $zn <= 'z') 
										|| ($zn >= 'A' && $zn <= 'Z') 
										|| ($zn >= '0' && $zn <= '9')
										|| $zn == '#' ) );
			
			/* Rozpoznano encję */
			if ($zn == '&') {
				$end = $this->n;
				$this->m--;
				$this->n = $n;
				//return html_entity_decode(mb_substr($this->content, $this->n, $end-$this->n));				
				return custom_html_entity_decode(mb_substr($this->content, $this->n, $end-$this->n));		
			}else{
				/* Nie jest to encja, więc zresetuj pozycję $n */
				$n = $this->n;
			}						
		}
		
		/* Jak jesteśmy w tym miejscu, to mamy do czynienia ze znakiem */
		if (!$this->ignore_whitespaces || mb_trim($c)!=''){
			$this->m--;
		}
		$this->n--;
		return $c;
	}
	
	function isNoSpace(){
		$left_content = mb_substr($this->content, $this->n);
		$left_content = strip_tags($left_content);
		return mb_trim($left_content[0]) == "";
	}
}

function mb_trim( $string )
{
    //$string = preg_replace( "/(^\s+)|(\s+$)/us", "", $string );   
    return trim($string);
} 

?>

