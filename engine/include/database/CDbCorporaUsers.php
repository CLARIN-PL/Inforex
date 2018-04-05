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

    /**
     * return arr of users with at least one morpho annotation in agreement stage
     * @param int $corpus_id
     */
	static function getCorpusUsersWithMorphoTaggs($corpus_id){
        global $db;
        $sql = "SELECT distinct u.* FROM `tokens_tags_optimized` tto
                    join tokens t on t.token_id = tto.token_id
                    join reports r on r.id = t.report_id
                    join users u on tto.user_id = u.user_id
                    where tto.stage = 'agreement'
                    and tto.user_id is not null
                    and r.corpora = ?";
        return $db->fetch_rows($sql, array($corpus_id));
    }
}

?>