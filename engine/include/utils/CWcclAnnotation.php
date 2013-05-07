<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class WcclAnnotation{
	
	var $from;
	var $to;
	var $text;
	var $type;
	
	function __construct($from, $to, $type, $text){
		$this->from = $from;
		$this->to = $to;
		$this->type = $type;
		$this->text = $text;
	}
	
}

?>