<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-01-13
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
class TakipiAnnotation{
	var $begin = null;
	var $end = null;
	var $name = null;
	
	function __construct($begin, $end, $name){
		$this->begin = $begin;
		$this->end = $end;
		$this->name = $name;
	}
} 
 
class TakipiAnndoc{
	var $annotations = array();
	
	function add($begin, $end, $name){
		$this->annotations[] = new TakipiAnnotation($begin, $end, $name);
	}
}

?>
