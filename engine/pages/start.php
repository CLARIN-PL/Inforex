<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_start extends CPage{
	
	var $isSecure = false;

	function checkPermission(){
		global $corpus;
		return hasCorpusRole(CORPUS_ROLE_READ) || $corpus['public'];
	}
		
	function execute(){
		global $db, $user, $corpus;
		
		$corpus_id = $corpus['id'];
		
		$subcorpora = $db->fetch_rows("SELECT IFNULL(s.name, '[unassigned]') AS name, count(r.id) AS count, IFNULL(s.subcorpus_id,0) AS subcorpus_id" .
				" FROM reports r " .
				" LEFT JOIN corpus_subcorpora s USING (subcorpus_id)" .
				" WHERE r.corpora = ?" .
				" GROUP BY subcorpus_id ORDER BY s.name ASC",
				array($corpus_id));

		$reports_count = $db->fetch_one("SELECT COUNT(*) FROM reports WHERE corpora = ?",
				array($corpus_id));
		
		$flags = $db->fetch_rows("SELECT *," .
				" SUM(IF(rf.flag_id=1,1,0)) AS f1," .
				" SUM(IF(rf.flag_id=2,1,0)) AS f2," .
				" SUM(IF(rf.flag_id=3,1,0)) AS f3," .
				" SUM(IF(rf.flag_id=4,1,0)) AS f4," .
				" SUM(IF(rf.flag_id=5,1,0)) AS f5" .
				" FROM corpora_flags f LEFT JOIN reports_flags rf USING (corpora_flag_id)" .
				" WHERE f.corpora_id = ? GROUP BY corpora_flag_id ORDER BY f.sort, f.name",
				array($corpus_id)); 
		
		foreach ($flags as $k=>$v){
			$flags[$k]['f0'] = $reports_count - $v['f1'] - $v['f2'] - $v['f3'] - $v['f4'] - $v['f5'];
		}
		
		$this->set("subcorpora", $subcorpora);
		$this->set("flags", $flags);
	}
}


?>
