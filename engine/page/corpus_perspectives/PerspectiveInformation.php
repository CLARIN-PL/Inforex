<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveInformation extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;
		$owner = $db->fetch("SELECT * FROM users WHERE user_id = {$corpus['user_id']}");
		$this->page->set('owner', $owner);
	}
}
?>
