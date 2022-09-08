<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class HtmlChar implements IHtmlChar {
	
	private $c = null;
	
	public function __construct($c){
		$this->c = $c;
	}	
	
	public function toString(){
//              wgawel: Dekodowanie encji - potrzebne do prawidłowego liczenia
//                      długości ciągów znaków np. przy wyszukiwaniu.
//              czuk:   Użucie html_entity_decode w tym miejscu nie jest uzasadnione,
//                      tym bardziej, że w tej postaci psuje kodowanie znaków.
//		return html_entity_decode($this->c, ENT_XML1 | ENT_QUOTES);
		return $this->c;
	}
}

?>
