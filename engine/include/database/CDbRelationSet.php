<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbRelationSet{

	/**
	 * Returns a list of relation sets assigned to a corpus with $corpusId id.
	 * @param int $corpuId
	 * @return An array of annotation schemas.
	 */
	static function getRelationSetsAssignedToCorpus($corpusId){
		global $db;		
		$sql = "SELECT * FROM relation_sets";
		return $db->fetch_rows($sql);
	}
	
	
}

?>