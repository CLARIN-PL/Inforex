<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbAnnotationSet{

	/**
	 * Returns a list of annotation schemas assigned to a corpus with $corpus_id id.
	 * @param int $corpu_id 
	 * @return An array of annotation schemas.
	 */
	static function getAnnotationSetsAssignedToCorpus($corpus_id){
		global $db;		
		$sql = "SELECT s.* FROM `annotation_sets` s JOIN `annotation_sets_corpora` sc USING (annotation_set_id) WHERE corpus_id = ? ORDER BY s.description";		
		return $db->fetch_rows($sql, array($corpus_id));		
	}
	
	
}

?>