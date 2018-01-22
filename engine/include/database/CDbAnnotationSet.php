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

	static function getCorporaOfAnnotationSet($annotation_set_id){
	    global $db;
	    $sql = "SELECT c.name, c.public, c.description FROM annotation_sets_corpora ansc 
                JOIN corpora c ON c.id = ansc.corpus_id
                WHERE ansc.annotation_set_id = ?
                ORDER BY c.name";
	    $annotation_sets = $db->fetch_rows($sql, array($annotation_set_id));

	    return $annotation_sets;
    }

    static function getCorporaAnnotationSetStats($annotation_set_id){
        global $db;

        $sql = "SELECT c.id, c.name, c.public, c.description, count(ra.id) AS 'count_uses' FROM annotation_types at
                JOIN annotation_sets_corpora ascr ON at.group_id = ascr.annotation_set_id
                JOIN corpora c ON c.id = ascr.corpus_id
                LEFT JOIN reports_annotations ra ON 
                (at.name = ra.type AND report_id IN 
                  (SELECT id FROM reports r WHERE r.corpora = c.id)
                )
                WHERE (at.group_id = ? AND ascr.annotation_set_id = ? AND c.public = 1)
                GROUP BY c.id
                ORDER BY count_uses";
        $corpora = $db->fetch_rows($sql, array($annotation_set_id, $annotation_set_id));

        return $corpora;
    }


}

?>