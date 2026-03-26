<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class CclLexeme{
	var $disamb = null;
	var $base = null;
	var $ctag = null;	
	
	function setDisamb($disamb){
		$this->disamb = $disamb;
	}
	
	function setBase($base){
		$this->base = $base;
	}
	
	function setCtag($ctag){
		$this->ctag = $ctag;
	}
	
	function getDisamb(){
		return $this->disamb;
	}
	
	function getBase(){
		return $this->base;
	}
	
	function getCtag(){
		return $this->ctag;
	}
	
} // CclLexeme class

?>
