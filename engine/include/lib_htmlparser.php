<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-05-12
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

class HtmlParser{
	function __construct($content){
		$this->content = $content;
		$this->n = 0; // Wskaźnik indeksu znaku w dokumencie
	}

	/**
	 * Zwraca aktualny znak. 
	 */
	function c(){
		return $this->content[$this->n];
	}
	
	/**
	 * Przesuwa wskaźnik do danego znacznika.
	 * @param unknown_type $text
	 */
	function moveToNode($type, $id){
		
	}
	
	/**
	 * Sprawdza, czy wskaźnik jest ustawiony na jakiś tag.
	 * Enter description here ...
	 */
	function isTag(){
		if ($this->c() == '<'){			
			$closing = $this->content[$this->n+1] == '/';
			$n = $closing ? $thin->n+2 : $thin->n+1; 
			while ('z' >= $this->content[$n] && $this->content[$n] >= 'a') $n++;
			if ( $this->content[$n] == '>'){
				// Tag otwierający lub kończący
			}else if ( $this->content[$n] == '/' && $this->content[$n+1] == '>' ){ 
				//Tag samozamykający
			}else if ( $this->content[$n] == " " ){
				// Tag z atrybutami			
			}else
				return false;
		}
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
	 * @param $opening -- czy pominąć tag otwierający
	 * @param $closing -- czy pominąć tag zamykający
	 * @return nazwa znacznika lub null 
	 */
	function skipTag($opening=true, $closing=true){
		if ( ($opening && mb_substr($this->content, $this->n, 1)=="<")
			 || ($closing && mb_substr($this->content, $this->n, 2)=="</") ) {
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
		if ($this->n>0 && mb_substr($this->content, $this->n-1, 1)==">"){
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

