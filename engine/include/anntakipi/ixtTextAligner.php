<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class TextAligner{
	
	// Public variables
	var $is_begin = false;
	var $is_end = false;
	var $is_inside = false;
	// List of closed annotation after invoking align
	var $annotation_ended = array();
	// List of opened annotation after invoking align	
	var $annotation_started = array();
	// Stack of currenly opened annotations
	var $annotation_stack = array();
	
	// Private variables
	var $_text = "";
	var $_text_length = "";
	var $_index = 0;	
	var $_cutoff = "";
	
	var $_c194 = null;
	var $_c160 = null;
	
	function __construct($text){
		$this->_text = $text;
		$this->_text_length = strlen($text);
		$this->_c194 = chr(194);
		$this->_c160 = chr(160);
	}
	
	function at($index){
		return mb_substr($this->_text, $index, 1);
	}
	
	function getNext($size){
		return mb_substr($this->_text, $this->_index, $size);
	}
	
	/**
	 * Dopasowuje dany fragment tekstu. Jeżeli tekst został dopasowany zwraca true, wpp false.
	 * Po udanym dopasowaniu ustawiane są następujące zmienne:
	 * $is_begin = true -- napotkano początek adnotacji
	 * $is_end   = true -- napotkano koniec adnotacji
	 * $is_border_inside = true -- początek lub koniec adnotacji wystąpił wewnątrz dopasowanego tekstu
	 * $annotation_name -- nazwa ostatnio napotkanej adnotacji
	 */
	function align($text_fragment){
		// Jeżeli po ostatnim wywołaniu osiągnięto koniec adnotacji, to usuń teraz jej nazwę.
		$this->is_begin = false;
		$this->is_end = false;
		$this->is_inside = false;
		$this->annotation_started = array();
		$this->annotation_ended = array();
		
		// Sprawdz, czy jest początek adnotacji
		$backup_index = $this->_index; 

		$cutnum = mb_strlen(trim($text_fragment)); // Number of characters to pass
		$cutoff = "";
		while ($cutnum>0){
			$this->pass_whitespaces();
			if ($this->is_next_tag_begin()){
				if ($cutoff!="") {
					// The annotation begin was found inside the character sequence.
					$this->is_inside = true;
				}; 
				$ann_name = $this->consume_tag_begin();
				array_push($this->annotation_stack, $ann_name);
				$this->annotation_started[] = $ann_name;
				$this->is_begin = true;
			}
			elseif ($this->is_next_tag_end()){
				$this->is_inside = true;
				$this->is_end = true;
				$this->_index += 5;
				$this->annotation_ended[] = array_pop($this->annotation_stack);
			}else{
				$cutnum--;
				$cutoff .= mb_substr($this->_text, $this->_index, 1);
				$this->_index++;
			}
		}
		$this->_index += $cutnum;
		
		// Fix na unicodowe znaki zamieniane na ?
				
		if ( $cutoff == $text_fragment ){
			while ($this->is_next_tag_end()){
				$this->_index += 5;
				$this->is_end = true;
				$this->pass_whitespaces();
				$this->annotation_ended[] = array_pop($this->annotation_stack);
			}							
			return true;
		}
		else{
			$this->_index = $backup_index;
			$this->_cutoff = $cutoff;
			
			return false;
		}
	}

	/**
	 * Odcina kolejny znak.
	 */
	function nextChar(){
		// Jeżeli po ostatnim wywołaniu osiągnięto koniec adnotacji, to usuń teraz jej nazwę.
		$this->is_begin = false;
		$this->is_end = false;
		$this->is_inside = false;
		$this->annotation_started = array();
		$this->annotation_ended = array();
		
		// Sprawdz, czy jest początek adnotacji
		$backup_index = $this->_index; 

		$cutnum = 1; // Number of characters to pass
		$cutoff = "";
		while ($cutnum>0){
			$this->pass_whitespaces();
			if ($this->is_next_tag_begin()){
				if ($cutoff!="") {
					// The annotation begin was found inside the character sequence.
					$this->is_inside = true;
				}; 
				$ann_name = $this->consume_tag_begin();
				array_push($this->annotation_stack, $ann_name);
				$this->annotation_started[] = $ann_name;
				$this->is_begin = true;
			}
			elseif ($this->is_next_tag_end()){
				$this->is_inside = true;
				$this->is_end = true;
				$this->_index += 5;
				$this->annotation_ended[] = array_pop($this->annotation_stack);
			}else{
				$char = mb_substr($this->_text, $this->_index++, 1);
				$cutoff .= $char;
				if ( $char <> $this->_c194 )
					$cutnum--;				
			}
		}
		$this->_index += $cutnum;
		while ($this->is_next_tag_end()){
			$this->_index += 5;
			$this->is_end = true;
			$this->pass_whitespaces();
			$this->annotation_ended[] = array_pop($this->annotation_stack);
		}							
		
		return $cutoff;
	}

	/**
	 * Przewiń do pierwszego znaku nie będącego białym znakiem.
	 */	
	function pass_whitespaces(){
		$zn = mb_substr($this->_text, $this->_index, 1);
		while ( $this->_index < $this->_text_length && ( trim($zn) =="" || $zn == " " ) ) {
			$this->_index++;
			$zn = mb_substr($this->_text, $this->_index, 1);
		}
	}
	
	function is_next_tag_end($index = null){
		if ($index == null)
			$index = $this->_index;
		return mb_substr($this->_text, $index, 5) == '</an>';
	}
	
	function is_next_tag_begin($index = null){
		if ($index == null)
			$index = $this->_index;
		return $this->at($this->_index) == "<" && mb_substr($this->_text, $this->_index, 4) == "<an#";
	}
	
	/**
	 * Return a name of consumed annotation.
	 */
	function consume_tag_begin(){
		$i = $this->_index;
		$moveto = mb_strpos($this->_text, ":", $i) + 1;
		$i = $moveto;
		
		$i_end = mb_strpos($this->_text, ">", $i);
		$ann_name = mb_substr($this->_text, $i, $i_end-$i);
		assert('$ann_name /* Annotation type is an empty string */');
		// Ustaw wskaźnik na pierwszy znak po adnotacji.
		$this->_index = $i_end + 1;
		$this->pass_whitespaces();
		return $ann_name;		
	}
		
}
 
?>
