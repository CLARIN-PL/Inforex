<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class XmlTagPointer implements IXmlTagPointer {
	
	private $tag = null;
	/** Indeks znaku przed którym występuje powiązany tag. */
	private $index = null;
	
	public function __construct(IHtmlTag $tag){
		$this->tag = $tag;
	}
	
	public function getTag(){
		return $this->tag;
	}
	
	public function setIndex($index){
		$this->index = $index;
	}
	
	public function getIndex(){
		return $this->index;
	}
	
	public function toString(){
		return $this->tag->toString();
	}
}

?>
