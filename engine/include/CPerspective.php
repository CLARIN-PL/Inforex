<?php
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
