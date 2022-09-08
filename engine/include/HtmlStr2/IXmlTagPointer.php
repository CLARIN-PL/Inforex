<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

interface IXmlTagPointer {
	
	public function __construct($tag);
	public function getTag();
	public function setIndex($index);
	public function getIndex();
	public function toString();

}

?>
