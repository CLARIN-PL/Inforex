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
		global $db, $corpus;

		$sql = "SELECT u.user_id, u.screename, u.login, us.role" .
					" FROM users_corpus_roles us " .
					" RIGHT JOIN users u ON (us.user_id=u.user_id AND us.role = '".CORPUS_ROLE_READ."' AND us.corpus_id=?)" .
					" ORDER BY u.screename";
					
		$this->page->set("users_in_corpus", $db->fetch_rows($sql,array($corpus['id'])));
	}
}
?>
