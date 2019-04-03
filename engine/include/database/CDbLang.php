<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbLang{
	
	static function getLangUsedInCorpus($corpusId){
		global $db;
		$sql = "SELECT l.code, l.language FROM reports r JOIN lang l ON (r.lang=l.code) WHERE corpora = ? GROUP BY l.code, l.language ORDER BY l.language";
		return $db->fetch_rows($sql, array($corpusId));
	}

}