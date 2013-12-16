<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_get_report_perspectives extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $user, $corpus;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$sql = "SELECT rp.id, " .
				"rp.title, " .
				"rp.description, " .
				"carp.access, " .
				"carp.corpus_id AS cid " .
				"FROM report_perspectives rp " .
				"LEFT JOIN corpus_and_report_perspectives carp " .
					"ON rp.id = carp.perspective_id " .
					"AND carp.corpus_id = ?" .
				" ORDER BY rp.title, `order`";	
		return $db->fetch_rows($sql, array($corpus['id']));
	}	
}
?>
