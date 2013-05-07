<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Abstract class defines a corpus perspective.
 * 
 */
abstract class CCorpusPerspective {
	
	var $page = null;
	
	function __construct(CPage &$page)
	{
		$this->page = $page;
	}
	
	abstract function execute();
	
}
?>
