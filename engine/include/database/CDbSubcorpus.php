<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class DbSuborpus{

	static function deleteSubcorpus($subcorpusId){
	    global $db;
        $db->execute("DELETE FROM corpus_subcorpora WHERE subcorpus_id = ?", array($subcorpusId));
    }

}

?>