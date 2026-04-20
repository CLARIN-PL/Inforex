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
			if (is_numeric($item)) {
				$ids[] = $item;
			} else {
				$names[] = $item;
			}
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
     * TODO: duplicate function from DbFlag
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

    private static function buildCorpusFlagHistoryWhere($corpus_id, $user, $flag, $search){
        $params = array($corpus_id);
        $where = array("cf.corpora_id = ?");

        if ($user !== null && $user !== '' && $user !== '-') {
            $where[] = "u.user_id = ?";
            $params[] = $user;
        }

        if ($flag !== null && $flag !== '' && $flag !== '-') {
            $where[] = "cf.corpora_flag_id = ?";
            $params[] = $flag;
        }

        $search = trim((string)$search);
        if ($search !== '') {
            $where[] = "(r.title LIKE ? OR cf.name LIKE ? OR u.screename LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        return array(
            'where' => implode(' AND ', $where),
            'params' => $params
        );
    }

    static function countCorpusFlagHistory($corpus_id, $user, $flag, $search=''){
        global $db;

        $query = self::buildCorpusFlagHistoryWhere($corpus_id, $user, $flag, $search);
        $sql = "SELECT COUNT(*) AS total
                FROM flag_status_history fsh
                JOIN corpora_flags cf ON cf.corpora_flag_id = fsh.flag_id
                JOIN reports r ON r.id = fsh.report_id
                JOIN users u ON u.user_id = fsh.user_id
                WHERE " . $query['where'];

        return (int)$db->fetch_one($sql, $query['params']);
    }

    static function getCorpusFlagHistory($corpus_id, $user, $flag, $search='', $limit=null, $offset=0){
        global $db;

        $query = self::buildCorpusFlagHistoryWhere($corpus_id, $user, $flag, $search);
        $params = $query['params'];

        $sql = "SELECT cf.name AS flag,
                       f1.flag_id AS new_status_id,
                       f1.name AS new_status,
                       f2.name AS old_status,
                       f2.flag_id AS old_status_id,
                       u.screename,
                       fsh.date AS date,
                       fsh.report_id,
                       r.title AS report_name,
                       cf.corpora_id AS corpus_id
                FROM flag_status_history fsh
                JOIN corpora_flags cf ON cf.corpora_flag_id = fsh.flag_id
                JOIN flags f1 ON f1.flag_id = fsh.new_status
                JOIN reports r ON r.id = fsh.report_id
                LEFT JOIN flags f2 ON f2.flag_id = fsh.old_status
                JOIN users u ON u.user_id = fsh.user_id
                WHERE " . $query['where'] . "
                ORDER BY fsh.date DESC";

        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }

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
