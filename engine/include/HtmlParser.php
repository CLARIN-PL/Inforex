<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class HtmlParser{
	function __construct($content){
		$this->content = $content;
		$this->n = 0; // Wskaźnik indeksu znaku w dokumencie liczona po znakach ASCII
	}

	/**
	 * Zwraca aktualny znak. 
	 */
	function c(){
		return $this->content[$this->n];
	}
	
	/**
	 * Odczytuje tekst do pierwszego znacznika.
	 */
	function readText(){
		$start = $this->n;
		while (!$this->isEnd() && !$this->isTag()) $this->n++;
		return substr($this->content, $start, $this->n-$start);
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
 		return !$this->isEnd() && $this->c() == '<';			
	}
	
	/**
	 * Sprawdza, czy wskaźnik doszedł do końca dokumentu.
	 */
	function isEnd(){
		return $this->n >= strlen($this->content);
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
				if ($this->m > mb_strlen($this->content))
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
	function skipTag(){
		if ($this->c() == "<")
		{
			while ($this->c() != ">")
				$this->n++;
			$this->n++;	
		}
	}

	function readTag(){
		$start = $this->n;
		if ($this->c() == "<")
		{
			while (!$this->isEnd() && $this->c() != ">")
				$this->n++;
			$this->n++;	
		}
		return substr($this->content, $start, $this->n-$start);
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


	/**
	 * Odczytuje anotacje inline z podanego tekstu html.
	 * Zwraca tablice id => array(from, to, type, id, text)
	 */
	static function readInlineAnnotations($content){
		$p = new HtmlParser($content);		
		$stack = array();
		$n = 0;		
		$annotations = array();
		while(!$p->isEnd()){
			if ($p->isTag()){
				$tag = $p->readTag();
				if (preg_match("<an#([0-9]+):([a-z_]+)>", $tag, $match))
				{
					array_push($stack, array($match, "", $n));
				}
				elseif ( $tag == "</an>")
				{
					$ann = array_pop($stack);
					$ann[] = $n-1;
					$annotations[$ann[0][1]] = array($ann[2], $ann[3], $ann[0][2], $ann[0][1], $ann[1]);
				}
			}else{
				$text = $p->readText();
				foreach ($stack as $k=>$v)
					$stack[$k][1] .= $text;
				//$text = html_entity_decode($text, ENT_COMPAT, "UTF-8");					
				$text = custom_html_entity_decode($text);				
				$text = preg_replace("/\s/", "", $text);
				$n += mb_strlen($text);
			}
		}
		return $annotations;
	}

	/**
	 * Odczytuje anotacje w formacie pary tagów <anb id="" type=""/> i <ane id=""/>
	 * Zwraca tablice id => array(from, to, type, id, text)
	 */
	static function readInlineAnnotationsWithOverlapping($content){
		$p = new HtmlParser($content);
		$h = new HtmlStr2($content);
		$starts = array();
		$ends = array();
		$n = 0;		
		$annotations = array();
		$wrong_annotations = array();
		
		while(!$p->isEnd()){
			if ($p->isTag()){
				$tag = $p->readTag();
				if (preg_match("/<anb id=\"([0-9]+)\" type=\"([\\p{Ll}_0-9]+)\"\/>/u", $tag, $match))
				{
					$starts[$match[1]] = array("from"=>$n, "type"=>$match[2], "id"=>$match[1]);
				}
				elseif (preg_match("<ane id=\"([0-9]+)\"\/>", $tag, $match))
				{
					$ends[$match[1]] = array("to"=>$n-1);
				}
			}else{
				$text = $p->readText();
				$text = custom_html_entity_decode($text);				
				$text = preg_replace("/\s/", "", $text);
				$n += mb_strlen($text);
			}
		}
		
		foreach ($starts as $id=>$s){
			if ( isset($ends[$id]) ){
				$e = $ends[$id];
				unset($ends[$id]);
				$text = $p->getContent();				
				$annotations[$id] = array( $s['from'], $e['to'], $s['type'], $id, $h->getText($s['from'], $e['to']));
			}
			else{
				$wrong_annotations[$id] = array("details" => htmlspecialchars("Missing tag <ane>"), "id" => $id);
			}
		}
		foreach ($ends as $id=>$e){
			$wrong_annotations[$id] = array("details" => htmlspecialchars("Missing tag <anb>"), "id" => $id);
		}
				
		return array ($annotations, $wrong_annotations);
	}


	/**
	 * Sprawdza poprawność dokumentu
	 */
	
	static function parseXml($content){
		$c = new MyDOMDocument();
		$c->loadXML($content);
		return $c->getErrors();
	}
	
	static function validateXmlWithXsd($contentXml, $xsdPath){
		$d = new MyDOMDocument();
		@$d->loadXML($contentXml);
		$errors = $d->getErrors();
		if($errors){
			return $errors;
		}
		@$d->schemaValidate($xsdPath);
		return $d->getErrors();
	}
}
?>

