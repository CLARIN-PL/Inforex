<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Abstract class defines a report perspective.
 * @author Michał Marcińczuk
 *
 */
abstract class CPerspective {
	
	var $page = null;
	var $document = array();
	
	function __construct(CPage &$page, &$document)
	{
		$this->page = $page;
		$this->document = $document;
	}
	
	abstract function execute();
	
}
?>
