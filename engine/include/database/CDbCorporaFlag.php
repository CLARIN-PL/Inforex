<?

class DbCorporaFlag{
	
	/*
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

	
	
}

?>