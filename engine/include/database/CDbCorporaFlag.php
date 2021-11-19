<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbCorporaFlag{
	
	/**
	 * Return list of corpora_flags ids
	 * 
	 * index_flags: array, values: corpora_flags.corpora_flag_id or corpora_flags.short
	 */
	static function getCorporaFlagIds($index_flags){
		global $db;

		$names = array(-1);
		$ids = array(-1);
		foreach ($index_flags as $item){
			if (is_numeric($item))
				$ids[] = $item;
			else $names[] = $item;
		}

		$sql = "SELECT corpora_flag_id, short " .
				"FROM corpora_flags " .
				"WHERE corpora_flag_id " .
				"IN ('" . implode("','",$ids) . "') " .
				"OR short " .
				"IN ('" . implode("','",$names) . "') ";
		return $db->fetch_rows($sql);
	}

	/**
	 * Return a list of flag values.
     * TODO: dupliacte function from DbFlag
	 */
	static function getFlags(){
		global $db;
		$sql = "SELECT * FROM flags ORDER BY flag_id";
		return $db->fetch_rows($sql);
	}
	
	/**
	 * Return a list of flags defined for given corpus.
	 * @param int $corpus_id
	 */
	static function getCorpusFlags($corpus_id){
		global $db;
		$sql = "SELECT f.* FROM corpora_flags f WHERE f.corpora_id = ? ORDER BY f.name";
		return $db->fetch_rows($sql, array($corpus_id));
	}

    static function getCorpusFlagHistory($corpus_id, $user, $flag){
        global $db;

        $params = array($corpus_id);

        if ($user != null) {
            $params[] = $user;
        }

        if ($flag != null) {
            $params[] = $flag;
        }


        $sql = "SELECT cf.name AS 'flag', f1.flag_id AS new_status_id, f1.name AS 'new_status', f2.name AS 'old_status', 
                f2.flag_id AS old_status_id, u.screename, DATE_FORMAT(fsh.date , '%H:%i, %D %M %Y') AS 'date',
                fsh.report_id, r.title AS 'report_name', cf.corpora_id AS 'corpus_id'
                FROM flag_status_history fsh 
                JOIN corpora_flags cf ON cf.corpora_flag_id = fsh.flag_id
                JOIN flags f1 ON f1.flag_id = fsh.new_status
                JOIN reports r ON r.id = fsh.report_id
                LEFT JOIN flags f2 ON f2.flag_id = fsh.old_status
                JOIN users u ON u.user_id = fsh.user_id
                WHERE (cf.corpora_id = ? " .
            ($user != null ? " AND u.user_id = ? ": "").
            ($flag != null ? " AND cf.corpora_flag_id = ? ": "").
            ") ORDER BY fsh.date DESC";


        return $db->fetch_rows($sql, $params);
    }

    static function getCorpusFlagChangeUsers($corpus_id){
        global $db;

        $sql = "SELECT u.user_id, u.screename FROM flag_status_history fsh 
                JOIN users u ON u.user_id = fsh.user_id
                JOIN reports r ON r.id = fsh.report_id
                WHERE r.corpora = ?
                GROUP BY u.user_id
                ORDER BY u.screename DESC";
        return $db->fetch_rows($sql, array($corpus_id));
    }
	
}

?>