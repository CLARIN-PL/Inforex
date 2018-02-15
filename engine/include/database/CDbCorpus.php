<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class DbCorpus{

	static function getCorpora($public = 1){
		global $db;
		$sql = "SELECT c.*, COUNT(r.id) AS `reports`" .
				" FROM corpora c" .
				" LEFT JOIN reports r ON (c.id = r.corpora)" .
				" WHERE c.public = ? ".
				" GROUP BY c.id" .
				" ORDER BY c.name";
		$corpora = $db->fetch_rows($sql, array($public));
		return $corpora;
	}
	
	static function getPrivateCorporaForUser($user_id, $is_admin){
		global $db;
		$sql = "SELECT c.*, COUNT(r.id) AS `reports`, u.screename" .
				" FROM corpora c" .
                " LEFT JOIN users u ON (u.user_id = c.user_id)" .
				" LEFT JOIN reports r ON (c.id = r.corpora)" .
				" LEFT JOIN users_corpus_roles cr ON (c.id=cr.corpus_id AND cr.user_id=? AND role='". CORPUS_ROLE_READ ."')" .
				" WHERE (c.user_id = ?" .
				"    OR cr.user_id = ?" .
				"    OR 1=?)" .
				"	 AND c.public = 0" .
				" GROUP BY c.id" .
				" ORDER BY c.name";
		
		$corpora = $db->fetch_rows($sql,array($user_id, $user_id, $user_id, $is_admin));
		return $corpora;	
	}
	
	static function getCorpusById($corpus_id){
		global $db;
		$sql = "SELECT * FROM corpora WHERE id = ?";
		return $db->fetch($sql, array($corpus_id));
	}

	/**
	 * Return subcorpus data for given id.
	 * @param unknown $subcorpus_id
	 * @return {Array}
	 */
	static function getSubcorpusById($subcorpus_id){
		global $db;
		$sql = "SELECT * FROM corpus_subcorpora WHERE subcorpus_id = ?";
		return $db->fetch($sql, array($subcorpus_id));
	}
	
	/**
	 * Return list of subcorpus. 
	 */
	static function getCorpusSubcorpora($corpus_id){
		global $db;
		
		$sql = "SELECT *" .
				" FROM corpus_subcorpora" .
				" WHERE corpus_id = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return list of corpus flags. 
	 */
	static function getCorpusFlags($corpus_id){
		global $db;
		
		$sql = "SELECT short, corpora_flag_id " .
				"FROM corpora_flags " .
				"WHERE corpora_id = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return list of corpus reports ids. 
	 */
	static function getCorpusReports($corpus_id){
		global $db;
		
		$sql = "SELECT id " .
				"FROM reports " .
				"WHERE corpora = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return corpus id by report id. 
	 */
	static function getCorpusByReportId($report_id){
		global $db;
		
		$sql = "SELECT corpora " .
				"FROM reports " .
				"WHERE id = ?";
		return $db->fetch_one($sql, array($report_id));
	}
	
	/**
	 * Return name of a table with additional document fields.
	 */
	static function getCorpusExtTable($corpus_id){
		global $db;
		$sql = "SELECT ext FROM corpora WHERE id = ?";
		return $db->fetch_one($sql, array($corpus_id));
	}
	
	/**
	 * Return array of table columns with their description.
	 */
	static function getCorpusExtColumns($table_name){
		global $db;
		if (!$table_name){
			return array();
		}
		else{
			$sql = "SHOW FULL COLUMNS FROM $table_name WHERE `key` <> 'PRI'";
			$rows = $db->fetch_rows($sql);

			$fields = array();
			foreach ($rows as &$row){
				$field = array();
				if (!isset($row['Field'])){
					throw new Exception("Attribute called Field not found");
				}
				$field['field'] = $row['Field'];
				$field['comment'] = $row['Comment'];
                if ($row['Null'] == 'YES') {
                    $field['null'] = "Yes";
                } else{
                    $field['null'] = "No";
                }

				if (preg_match('/^enum\((.*)\)$/', $row['Type'], $match)){
					$field['type'] = 'enum';
					$values = array();
					foreach ( split(",", $match[1]) as $v )
						$values[] = trim($v, "'");
					$field['field_values'] = $values; 
				}
				else
					$field['type'] = 'text';
				$fields[] = $field;		
			}
			return $fields;
		}
	}
	
	/**
	 * Zwraca listę wszystkich podkorpusów.
	 */
	static function getSubcorpora(){
		global $db;
		$sql = "SELECT * FROM corpus_subcorpora";
		return $db->fetch_rows($sql);
	}
	
	/**
	 * 
	 * @param unknown $corpus_id
	 * @param unknown $name
	 * @param unknown $description
	 * @return subcorpus id
	 */
	static function createSubcopus($corpus_id, $name, $description){
		global $db;
		$sql = "INSERT INTO corpus_subcorpora (corpus_id, name, description) VALUES (?, ?, ?) ";		
		$db->execute($sql, array($corpus_id, $name, $description));
		return $db->last_id();
	}


    static function getSubcorporaByIds($subcorpora_ids, $fields=null){
        global $db;
        $sql = "SELECT ".
            ($fields ? $fields : " * " ).
            " FROM corpus_subcorpora " .
            "WHERE subcorpus_id IN('" . implode("','",$subcorpora_ids) . "') ORDER BY subcorpus_id";
        return $db->fetch_rows($sql);
    }
}

?>