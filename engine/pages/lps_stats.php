<?php

class Page_lps_stats extends CPage{
	
	var $isSecure = true;
	
	function checkPermission(){
		return "Brak dostępu";
	}
	
	function execute(){
		global $corpus;
		
		$count_by = isset($_GET['count_by']) ? strval($_GET['count_by']) : ""; 
		
		if ($corpus['id'] != 3)
			$this->redirect("index.php?page=browse&id=" . $corpus['id']);
		
		if ( $count_by == "author" )
			$perspective = "(SELECT e.*" .
				"			 FROM reports_ext_3 e" .
				"			 JOIN reports r USING (id)" .
				"			 GROUP BY SUBSTRING(r.title, 1, 4)) AS a";
		else 
			$perspective = "reports_ext_3 a JOIN reports r USING (id)";
		
		$gender = db_fetch_rows("SELECT a.deceased_gender, count(*) as count FROM $perspective GROUP BY a.deceased_gender");
		$maritial = db_fetch_rows("SELECT a.deceased_maritial, count(*) as count FROM $perspective GROUP BY a.deceased_maritial");
		$age = db_fetch_rows("SELECT FLOOR(a.deceased_age/10)*10 as span_from, FLOOR(a.deceased_age/10)*10+9 as span_to, count(*) as count FROM $perspective GROUP BY FLOOR(a.deceased_age/10) ORDER BY FLOOR(a.deceased_age/10) ASC;");

		$age_gender_t = db_fetch_rows("SELECT FLOOR(a.deceased_age/10)*10 as span_from, FLOOR(a.deceased_age/10)*10+9 as span_to, a.deceased_gender, count(*) as count FROM $perspective WHERE a.deceased_gender IS NOT NULL GROUP BY FLOOR(a.deceased_age/10), a.deceased_gender ORDER BY FLOOR(a.deceased_age/10) ASC;");
		$age_gender = array();
		foreach ($age_gender_t as $r){
			$age_gender[$r['span_from']][$r['deceased_gender']] = $r;
		}

		$age_maritial_t = db_fetch_rows("SELECT FLOOR(a.deceased_age/10)*10 as span_from, FLOOR(a.deceased_age/10)*10+9 as span_to, a.deceased_maritial, count(*) as count FROM $perspective WHERE a.deceased_gender IS NOT NULL GROUP BY FLOOR(a.deceased_age/10), a.deceased_maritial ORDER BY FLOOR(a.deceased_age/10) ASC;");
		$age_maritial = array();
		foreach ($age_maritial_t as $r){
			$age_maritial[$r['span_from']][$r['deceased_maritial']] = $r;
			$age_maritial[$r['span_from']]['span_from'] = $r['span_from']; 
			$age_maritial[$r['span_from']]['span_to'] = $r['span_to']; 
		}

		$maritial_gender_t = db_fetch_rows("SELECT a.deceased_maritial, a.deceased_gender, count(*) as count FROM $perspective WHERE a.deceased_gender IS NOT NULL AND a.deceased_maritial IS NOT NULL GROUP BY a.deceased_gender, a.deceased_maritial;");
		$maritial_gender = array("single"=>array("male"=>null, "female"=>null), "cohabitant"=>array("male"=>null, "female"=>null));
		foreach ($maritial_gender_t as $r){
			$maritial_gender[$r['deceased_maritial']][$r['deceased_gender']] = $r;
		}

		$source = db_fetch_rows("SELECT source, count(*) as count FROM reports_ext_3 r GROUP BY source;");
		
		$this->set('tags', $this->get_tags_count());
		$this->set('error_types', $this->get_error_types());			
		$this->set('gender', $gender);
		$this->set('maritial', $maritial);
		$this->set('age', $age);
		$this->set('age_gender', $age_gender);
		$this->set('age_maritial', $age_maritial);
		$this->set('maritial_gender', $maritial_gender);
		$this->set('source', $source);
		$this->set('count_by', $count_by);
	}


	/**
	 * Zlicza liczbę znaczników w korpusie.
	 */
	function get_tags_count(){
		$rows = db_fetch_rows("SELECT content FROM reports WHERE corpora = 3");
		
		$tags = array();
			
		foreach ($rows as $row){
			
			$content = html_entity_decode($row['content']);
			
			if (preg_match_all("/<([a-zA-Z]+)( [^>]*|\/)?>/", $content, $matches)){
				foreach ($matches[1] as $tag){
					
					if ( !isset($tags[$tag]) )
						$tags[$tag] = 0;
						
					$tags[$tag]++;
				}
			}
						
		}
		
		arsort($tags);
		
		return $tags;		
	}

	/**
	 * Policz statystyki błędów
	 */
	function get_error_types(){
		$rows = db_fetch_rows("SELECT content FROM reports WHERE corpora = 3");
		
		$errors = array();
			
		foreach ($rows as $row){			
			$content = html_entity_decode($row['content']);
			if (preg_match_all('/<corr [^>]*type="([^"]+)"/m', $content, $matches)){
				foreach ($matches[1] as $types){
					foreach (explode(",", $types) as $type){
						$type = trim($type);
							if ( !isset($errors[$type]) )
								$errors[$type] = 1;
							else						
								$errors[$type]++;
					}	
				}
			}
						
		}
		
		arsort($errors);
		return $errors; 
	}	
}

?>


