<?
/**
 * 
 */
class DbCorpus{

	static function getCorpusById($corpus_id){
		global $db;
		$sql = "SELECT * FROM corpora WHERE id = ?";
		return $db->fetch($sql, array($corpus_id));
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
		
		$sql = "SELECT short " .
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
			foreach ($rows as &$row){
				if (preg_match('/^enum\((.*)\)$/', $row['type'], $match)){
					$row['field_type'] = 'enum';
					$values = array();
					if ($row['null']=='YES')
						$values[] = '(NULL)';
					foreach ( split(",", $match[1]) as $v )
						$values[] = trim($v, "'");
					$row['field_values'] = $values; 
				}
				else
					$row['filed_type'] = 'enum';			
			}
			return $rows;
		}
	}
	
}

?>