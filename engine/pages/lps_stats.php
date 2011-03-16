<?php

class Page_lps_stats extends CPage{
	
	var $isSecure = true;
	
	function execute(){
		global $corpus;
		
		if ($corpus['id'] != 3)
			$this->redirect("index.php?page=browse&id=" . $corpus['id']);
		
		$gender = db_fetch_rows("SELECT a.deceased_gender, count(*) as count FROM (SELECT e.* FROM reports_ext_3 e JOIN reports r USING (id) GROUP BY SUBSTRING(r.title, 1, 4)) AS a GROUP BY a.deceased_gender");
		$maritial = db_fetch_rows("SELECT a.deceased_maritial, count(*) as count FROM (SELECT e.* FROM reports_ext_3 e JOIN reports r USING (id) GROUP BY SUBSTRING(r.title, 1, 4)) AS a GROUP BY a.deceased_maritial");
		$age = db_fetch_rows("SELECT FLOOR(a.deceased_age/10)*10 as span_from, FLOOR(a.deceased_age/10)*10+9 as span_to, count(*) as count FROM (SELECT e.* FROM reports_ext_3 e JOIN reports r USING (id) GROUP BY SUBSTRING(r.title, 1, 4)) AS a GROUP BY FLOOR(a.deceased_age/10) ORDER BY FLOOR(a.deceased_age/10) ASC;");

		$age_gender_t = db_fetch_rows("SELECT FLOOR(a.deceased_age/10)*10 as span_from, FLOOR(a.deceased_age/10)*10+9 as span_to, a.deceased_gender, count(*) as count FROM (SELECT e.* FROM reports_ext_3 e JOIN reports r USING (id) GROUP BY SUBSTRING(r.title, 1, 4)) AS a WHERE a.deceased_gender IS NOT NULL GROUP BY FLOOR(a.deceased_age/10), a.deceased_gender ORDER BY FLOOR(a.deceased_age/10) ASC;");
		$age_gender = array();
		foreach ($age_gender_t as $r){
			$age_gender[$r['span_from']][$r['deceased_gender']] = $r;
		}

		$age_maritial_t = db_fetch_rows("SELECT FLOOR(a.deceased_age/10)*10 as span_from, FLOOR(a.deceased_age/10)*10+9 as span_to, a.deceased_maritial, count(*) as count FROM (SELECT e.* FROM reports_ext_3 e JOIN reports r USING (id) GROUP BY SUBSTRING(r.title, 1, 4)) AS a WHERE a.deceased_gender IS NOT NULL GROUP BY FLOOR(a.deceased_age/10), a.deceased_maritial ORDER BY FLOOR(a.deceased_age/10) ASC;");
		$age_maritial = array();
		foreach ($age_maritial_t as $r){
			$age_maritial[$r['span_from']][$r['deceased_maritial']] = $r;
			$age_maritial[$r['span_from']]['span_from'] = $r['span_from']; 
			$age_maritial[$r['span_from']]['span_to'] = $r['span_to']; 
		}

		$maritial_gender_t = db_fetch_rows("SELECT a.deceased_maritial, a.deceased_gender, count(*) as count FROM (SELECT e.* FROM reports_ext_3 e JOIN reports r USING (id) GROUP BY SUBSTRING(r.title, 1, 4)) AS a WHERE a.deceased_gender IS NOT NULL AND a.deceased_maritial IS NOT NULL GROUP BY a.deceased_gender, a.deceased_maritial;");
		$maritial_gender = array("single"=>array("male"=>null, "female"=>null), "cohabitant"=>array("male"=>null, "female"=>null));
		foreach ($maritial_gender_t as $r){
			$maritial_gender[$r['deceased_maritial']][$r['deceased_gender']] = $r;
		}

		$source = db_fetch_rows("SELECT source, count(*) as count FROM reports_ext_3 r GROUP BY source;");
				
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


