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
	 */
	static function getFlags(){
		global $db;
		$sql = "SELECT * FROM flags ORDER BY flag_id";
		return $db->fetch_rows($sql);
	}
	
	/**
	 * Return a list of flags defined for given corpus.
	 * @param unknown $corpus_id
	 */
	static function getCorpusFlags($corpus_id){
		global $db;
		$sql = "SELECT f.* FROM corpora_flags f WHERE f.corpora_id = ? ORDER BY f.sort";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
}

?>