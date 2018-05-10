<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectivePerspectives extends CCorpusPerspective {
	
	function execute()
	{
		$this->set_corpus_perspectives();
		$this->set_users_perspectives();
		PerspectiveUsers_roles::set_users_roles();
	}
	
	function set_corpus_perspectives(){
		global $corpus, $db;
		$sql = "SELECT *" .
				" FROM corpus_and_report_perspectives carp" .
				" LEFT JOIN report_perspectives rp ON (rp.id = carp.perspective_id)" .
				" WHERE carp.corpus_id = ? ORDER BY `order`";

		$rows = $db->fetch_rows($sql, array($corpus['id']));
		$corpus_perspectivs = array();
		
		foreach ($rows as $row){
			$corpus_perspectivs[$row['id']]['title'] = $row['title'];
			$corpus_perspectivs[$row['id']]['access'] = $row['access'];				 
		}
		
		$this->page->set('corpus_perspectivs', $corpus_perspectivs);
	}
	
	function set_users_perspectives(){
		global $corpus, $db;
		$sql = "SELECT *" .
				" FROM corpus_perspective_roles cpr " .
				" WHERE cpr.corpus_id = ?";

		$rows = $db->fetch_rows($sql, array($corpus['id']));
		$users_perspectives = array();
		
		foreach ($rows as $row){
			$users_perspectives[$row['user_id']][] = $row['report_perspective_id'];
		}
		
		$this->page->set('users_perspectives', $users_perspectives);
	}
}
?>
