<?php
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
