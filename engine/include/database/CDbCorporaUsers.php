<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbCorporaUsers{
		
	/**
	 * Zwraca listę użytkowników przypisanych do danego korpusu.
	 * @param unknown $corpus_id
	 */
	static function getCorpusUsers($corpus_id){
		global $db;
		$sql = "SELECT u.*" .
					" FROM users_corpus_roles us " .
					" JOIN users u ON (us.user_id=u.user_id AND us.corpus_id=?) ".
					" GROUP BY u.user_id ".
					" ORDER BY u.screename";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
}

?>