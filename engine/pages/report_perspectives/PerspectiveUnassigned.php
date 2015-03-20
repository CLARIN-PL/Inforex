<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveUnassigned extends CPerspective {
	
	function execute()
	{
		global $db;
		$subpage = $this->page->get("unassigned_subpage");
		
		$perspective = $db->fetch("SELECT * FROM report_perspectives WHERE id = ?", array($subpage));
		
		$this->page->set("perspective", $perspective);
	}
}
?>
