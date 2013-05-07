<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Przekształca tekst z postaci wizualnej zapisanej w postaci XML-a, 
 * do postaci czystego tekstu. Transformacja uwzględnia:
 * - łączenie podzielonych słów,
 * - usunięcie wykreślonych fragmentu tekstu.
 * Transformacja tworzy mapowanie indeksów znaków z postaci "oczyszczonej"
 * do postaci bazowej.
 * 
 * Klasa służy do wycągnięcia ciągłego tekstu, który może zostać poddanny
 * analizie morfologicznej oraz zapamiętanie pominiętych elementów w celu
 * ustalenia granic tokenów w rzeczywistym tekście.
 */
class LpsTextTransformer{
	
	/**
	 *  Tablica przesunięć indeksów.
	 *  a => b oznacza, że dla znaków o indeksie >=a należy dodać wartość b
	 */
	var $offset = array();
	
	var $cleanText = null;
	var $originalText = null;
	
	/* Number of ignored characters */
	var $cutoffLength = 0;
	
	/**
	 * @param $text -- tekst bazowy
	 */
	function __construct($text){
		$this->originalText = $text;
		$this->cleanText = $this->_parseText($this->originalText); 
	}
	
	function _parseText($text){
		$reader = new XMLReader();
		$reader->xml($text);
		$cleanText = "";
		$space = "";
		$indexOriginal = 0;
		$indexClean = 0;
		do {
			$read = $reader->read();
			
			if ($reader->nodeType == XMLReader::ELEMENT 
					&& in_array($reader->localName, array("hyph", "del") ) ){

				if ( $reader->localName == "hyph" )
					$space = ""; // następny fragment tekstu będzie dodany bez spacji ze względu na przeniesienie

				$skipped = $reader->readString();
				$skippedCount = count_characters($skipped);
				$reader->next();
			
				$this->cutoffLength += $skippedCount; 					
				$this->offset[$indexClean] += $skippedCount;  
			}
									
			if ($reader->nodeType == XMLReader::TEXT){
				$string = $reader->readString();
				$cleanText .= $space . trim($string);
				$space = " ";
				
				$indexClean += count_characters($string);
			}
						
		}
		while ( $read );
			
		return trim($cleanText);
	}

	/**
	 * Zwraca oczyszczony tekst.
	 * @return tekst oczyszczony
	 */	
	function getCleanText(){
		return $this->cleanText;
	}
	
	/**
	 * Zwraca pierwotną postać tekstu.
	 * @return tekst pierwotny
	 */
	function getBaseText(){
		return $this->baseText;
	}
	
	/**
	 * Mapuje indeks z tekstu oczyszczonego do indeksu tekstu bazowego.
	 * @param $cleanText -- indeks znaku w tekście oczyszczonym,
	 * @return indeks znaku w tekście bazowym
	 */
	function mapToBaseIndes($cleanIndex){
		$offset = 0;
		foreach ($this->offset as $k=>$v)
			if ($cleanIndex >= $k)
				$offset += $v;
		return $cleanIndex + $offset;
	}
}
?>