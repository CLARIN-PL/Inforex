<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveUsers_roles extends CCorpusPerspective {
	
	function execute()
	{
		$this->set_users_roles();
		$this->set_corpus_roles();
	}
	
	function set_corpus_roles(){
		global $db;
		$corpus_roles = $db->fetch_rows("SELECT * FROM corpus_roles WHERE role != 'read'");
		$this->page->set('corpus_roles', $corpus_roles);
	}
}
?>
