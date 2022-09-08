<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class HtmlTag implements IHtmlTag {
	
	private $name = null;
	private $type = null;
	private $str = null;
	
	public function __construct($name, $type, $str){
		$this->name = $name;
		$this->type = $type;
		$this->str = $str;	
	}

	public function toString(){
		return $this->str;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getType(){
		return $this->type;
	}
}

?>
