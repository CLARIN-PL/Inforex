<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveUsers extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus;

		$users = DbCorporaUsers::getCorpusReadUsersWithLastActivity($corpus['id']);
					
		$this->page->set("users_in_corpus", $users);
	}
}
?>
