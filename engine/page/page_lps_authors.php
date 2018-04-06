<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_lps_authors extends CPage{
	
	var $isSecure = true;
	
	function checkPermission(){
		return hasCorpusRole(CORPUS_ROLE_READ);
	}
	
	function execute(){
		global $corpus;
		
		$count_by = array_get_str($_GET, "filter_count_by", "author"); 
		$subcorpus = array_get_str($_GET, "subcorpus", null);
		$corpus_id = array_get_int($corpus, "id", 0);
					
		$filters = array();
		$filters[] = array(
						"name"     => "count_by", 
						"values"   => array("author"=>"autorzy", "letter"=>"listy"),
						"selected" => $count_by,
						"all"	   => false	
					);
		fb($filters);
					
		if ($corpus['id'] != 3)
			$this->redirect("index.php?page=browse&id=" . $corpus['id']);
		
		$this->set_authors_stats($count_by, $subcorpus);
		$this->set("filters", $filters);
		$this->set("count_by", $count_by);
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));
		$this->set("subcorpus", $subcorpus);				
	}

	/**
	 * 
	 */
	function set_authors_stats($count_by, $subcorpus){
		if ( $count_by == "author" )
			$perspective = "(SELECT e.*, r.subcorpus_id, r.corpora" .
				"			 FROM reports_ext_3 e" .
				"			 JOIN reports r USING (id)" .
				"			 GROUP BY SUBSTRING(r.title, 1, 4)) AS a";
		else 
			$perspective = "reports r LEFT JOIN reports_ext_3 a USING (id)";
		
		$gender = db_fetch_rows("SELECT a.deceased_gender, count(DISTINCT id) as count" .
						" FROM $perspective" .
						" WHERE corpora = 3" .
						( $subcorpus ? " AND subcorpus_id = $subcorpus" : "") .
						" GROUP BY IF(a.deceased_gender IS NULL,'',TRIM(a.deceased_gender))");
		$maritial = db_fetch_rows("SELECT a.deceased_maritial, count(*) as count" .
						" FROM $perspective" .
						( $subcorpus ? " WHERE subcorpus_id = $subcorpus" : "") .
						" GROUP BY IF(a.deceased_maritial IS NULL,'',a.deceased_maritial)");						
		$age = db_fetch_rows("SELECT start as span_from, end as span_to, count(*) as count" .
						" FROM pcsn_age_ranges " .
						" LEFT JOIN $perspective ON (a.deceased_age>=start AND a.deceased_age<=end)" .
						( $subcorpus ? " WHERE subcorpus_id = $subcorpus" : "") .
						" GROUP BY start" .
						" ORDER BY start ASC;");
		$age_gender_t = db_fetch_rows("SELECT start as span_from, end as span_to, a.deceased_gender, count(*) as count" .
						" FROM pcsn_age_ranges " .
						" LEFT JOIN $perspective ON (a.deceased_age>=start AND a.deceased_age<=end)" .
						" WHERE a.deceased_gender IS NOT NULL" .
						( $subcorpus ? " AND subcorpus_id = $subcorpus" : "") .
						" GROUP BY start, a.deceased_gender" .
						" ORDER BY start ASC;");
		$age_gender = array();
		foreach ($age_gender_t as $r){
			$age_gender[$r['span_from']][$r['deceased_gender']] = $r;
		}

		$age_maritial_t = db_fetch_rows("SELECT start as span_from, end as span_to, a.deceased_maritial, count(*) as count" .
						" FROM pcsn_age_ranges " .
						" LEFT JOIN $perspective ON (a.deceased_age>=start AND a.deceased_age<=end)" .
						" WHERE a.deceased_gender IS NOT NULL" .
						( $subcorpus ? " AND subcorpus_id = $subcorpus" : "") .
						" GROUP BY start, a.deceased_maritial" .
						" ORDER BY start ASC;");
		$age_maritial = array();
		foreach ($age_maritial_t as $r){
			$age_maritial[$r['span_from']][array_get_str($r, "deceased_maritial", "none")] = $r;
			$age_maritial[$r['span_from']]['span_from'] = $r['span_from']; 
			$age_maritial[$r['span_from']]['span_to'] = $r['span_to']; 
		}

		$maritial_gender_t = db_fetch_rows("SELECT a.deceased_maritial, a.deceased_gender, count(*) as count " .
				" FROM $perspective " .
				" WHERE a.deceased_gender IS NOT NULL AND a.deceased_maritial IS NOT NULL " .
				( $subcorpus ? " AND subcorpus_id = $subcorpus" : "") .
				" GROUP BY a.deceased_gender, a.deceased_maritial;");
		$maritial_gender = array("single"=>array("male"=>null, "female"=>null), "cohabitant"=>array("male"=>null, "female"=>null));
		foreach ($maritial_gender_t as $r){
			$maritial_gender[$r['deceased_maritial']][$r['deceased_gender']] = $r;
		}

		$source = db_fetch_rows("SELECT source, count(*) as count FROM reports_ext_3 r GROUP BY IF(source IS NULL,'',source);");
		
		$this->set('gender', $gender);
		$this->set('maritial', $maritial);
		$this->set('age', $age);
		$this->set('age_gender', $age_gender);
		$this->set('age_maritial', $age_maritial);
		$this->set('maritial_gender', $maritial_gender);
		$this->set('source', $source);
	}

}

?>


