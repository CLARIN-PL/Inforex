<?php

class Page_lps_stats extends CPage{
	
	var $isSecure = true;
	
	function checkPermission(){
		return hasCorpusRole(CORPUS_ROLE_READ);
	}
	
	function execute(){
		global $corpus;
		
		$count_by = isset($_GET['count_by']) ? strval($_GET['count_by']) : ""; 
		$corpus_id = $corpus['id'];
		$subcorpus = $_GET['subcorpus'];
				
		if ($corpus['id'] != 3)
			$this->redirect("index.php?page=browse&id=" . $corpus['id']);
		
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
			$age_maritial[$r['span_from']][$r['deceased_maritial']] = $r;
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
		
		$this->set('tags', $this->get_tags_count($subcorpus));
		$this->set('error_types', $this->get_error_types($subcorpus));			
		$this->set('error_type_tags', $this->get_error_type_tags('capital', $subcorpus));			
		$this->set('gender', $gender);
		$this->set('maritial', $maritial);
		$this->set('age', $age);
		$this->set('age_gender', $age_gender);
		$this->set('age_maritial', $age_maritial);
		$this->set('maritial_gender', $maritial_gender);
		$this->set('source', $source);
		$this->set('count_by', $count_by);
		$this->set('subcorpus', $subcorpus);
		$this->set('subcorpora', DbCorpus::getCorpusSubcorpora($corpus_id));		
		
		$this->set_errors_correlation_matrix($subcorpus);
		$this->set_interpuntion_stats($subcorpus);
	}


	/**
	 * Zlicza liczbę znaczników w korpusie.
	 */
	function get_tags_count($subcorpus=null){
		global $config;
		
		$rows = DbReport::getReports($subcorpus?null:array(3), $subcorpus?array($subcorpus):null);
		$docs = DbReport::getReportsCount(3, $subcorpus);		
		
		$counter = new ElementCounter();
		//$tags = array();

		$contents = array();
		
		foreach ($rows as $row){			
			$content = custom_html_entity_decode($row['content']);
			$contents[] = $content;
						
			if (preg_match_all("/<([a-zA-Z]+)( [^>]*|\/)?>/", $content, $matches)){				
				for ($i=0; $i<count($matches[0]); $i++){
					$tag = $matches[1][$i];
					$att = $matches[2][$i];
				
					$counter->add($tag, $row['id']);
				
					/* Zlicz podtypy dla p */
					if ($tag == "p"){
						$tag = strpos($att, "place=")	=== false ? "p [rend]" : "p [place]";
						$counter->add($tag, $row['id']);
					}							
					/* Zlicz podtypy dla p */
					else if ($tag == "ornament"){
						//$tag = strpos($att, "type=") === false ? "p [rend]" : "p [place]";
						$type = substr($att, 7, strlen($att)-9);
						$counter->add("ornament [$type]", $row['id']);
					}							
				}
			}							
		}
		
		$tags = $counter->getDict();		
		foreach ($tags as &$tag){
			$tag['docper'] = count($tag['docset'])/$docs*100;
		}
		
		ksort($tags);
		
		return $tags;		
	}

	/**
	 * Policz statystyki błędów
	 */
	function get_error_types($subcorpus){
		$sql = "SELECT content, id, title FROM reports WHERE corpora = 3";
		if ( $subcorpus )
			$sql .= " AND subcorpus_id = $subcorpus";
		$rows = db_fetch_rows($sql);
		
		$errors = array();
			
		foreach ($rows as $row){			
			//$content = html_entity_decode($row['content']);
			$content = custom_html_entity_decode($row['content']);
			list($author, $x) = explode(".", $row['title']);
			
			$matches = array(array(), array());
			
			if (preg_match_all('/<corr [^>]*type="([^"]+)"/m', $content, $m)){
				$matches[0] = array_merge($matches[0], $m[0]);
				$matches[1] = array_merge($matches[1], $m[1]);
			}
			if (preg_match_all('/<corr [^>]*resp="(author)"/m', $content, $m)){
				$matches[0] = array_merge($matches[0], $m[0]);
				$matches[1] = array_merge($matches[1], $m[1]);
			}
			
			for ($i=0; $i<count($matches[0]); $i++){
				$tag = $matches[0][$i];
				$type = $matches[1][$i];

				foreach (explode(",", $type) as $type){
					$type = trim($type);
					if ( !isset($errors[$type]) ){
						$errors[$type]['count'] = 1;
					}
					else{
						$errors[$type]['count']++;
					}
					$errors[$type]['docs'][$row['id']] = 1;								
					$errors[$type]['authors'][$author] = 1;								
				}	
			}
						
		}
		
		foreach ($errors as $k=>$v){
			$errors[$k]['count_docs'] = count(array_keys($v['docs']));
			$errors[$k]['count_authors'] = count(array_keys($v['authors']));
		}
		
		arsort($errors);
		return $errors; 
	}	
	
	/**
	 * 
	 */
	function get_error_type_tags($error, $subcorpus_id){
		$tags = array();
		$rows = $subcorpus_id ? DbReport::getReports(null, $subcorpus_id) : DbReport::getReports(3);
		
		if ($error == "author") 
			$pattern = '/(<corr [^>]*resp="author"[^>]*>)(.*?)<\/corr>/m';
		else
			$pattern = '/(<corr [^>]*type="([^"]+,)*'.$error.'(,[^"]+)*"[^>]*>)(.*?)<\/corr>/m';

		foreach ($rows as $row){			
			//$content = html_entity_decode($row['content']);
			$content = custom_html_entity_decode($row['content']);
			$id = $row['id'];
			if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)){
				foreach ($matches as $m){	
					if ( isset($tags[$m[0]]) ){
						$tags[$m[0]][count]++;
						$tags[$m[0]]['docs'][$id]['count']++;
						$tags[$m[0]]['docs'][$id]['name'] = $row['title'];
					}
					else{
						preg_match('/sic="([^"]*)"/', $m[0], $sic);
						preg_match('/type="([^"]*)"/', $m[0], $type);
						$tags[$m[0]] = array('count'=>1, 
							'type'=>$error == "author" ? "" : $type[1], 
							'sic'=>$error == "author" ? "" : $sic[1], 
							'content'=>strip_tags($m[0]),
							'tag'=>htmlentities($m[0], ENT_COMPAT, 'UTF-8'),
							'docs'=>array($id=> array('count'=>1, 'name'=>$row['title'])));	
					}						
				}
			}
		}
		
		/* Zamień listę dokumentów, w których wystąpiły znaczniki, na liczbę dokumentów */
		foreach ($tags as $k=>$v){
			$tags[$k]['count_docs'] = count(array_keys($tags[$k]['docs']));
			arsort($tags[$k]['docs']);			
		}
		
		usort($tags, "lps_sort_errors");
						
		return $tags;
	}

	/**
	 * Zwraca listę dokumentów zawierających określony znacznik
	 */
	function get_error_tag_docs($tag, $subcorpus_id){
		$docs = array();
		$tag = stripslashes($tag);
		$rows = $subcorpus_id ? DbReport::getReports(null, $subcorpus_id) : DbReport::getReports(3);

		foreach ($rows as $row){			
			$content = custom_html_entity_decode($row['content']);
			$id = $row['id'];
			$count = substr_count($content, $tag);
			if ($count > 0){
				$docs[$id] = array("title"=>$row['title'], "count"=>$count);			
			}
		}
		asort($docs);
								
		return $docs;
	}
		
	/**
	 * Tworzy macierz współwystępowania błędów.
	 */
	function set_errors_matrix($subcorpus_id){
		$rows = $subcorpus_id ? DbReport::getReports(null, $subcorpus_id) : DbReport::getReports(3);
		$errors = array();

		foreach ($rows as $row){			
			$content = custom_html_entity_decode($row['content']);
			list($author, $x) = explode(".", $row['title']);

			if (preg_match_all('/<corr [^>]*type="([^"]+)"/m', $content, $matches)){
				foreach ($matches[1] as $types){
					foreach (explode(",", $types) as $type){
						$type = trim($type);
						$errors[$type][$author] = 1;
					}
				}
			}
		}
		
		$matrix = array();
		foreach ( $errors as $x=>$type1){
			foreach ( $errors as $y=>$type2){
				if ($x == $y)
					$matrix[$x][$y] = count(array_keys($type1));
				else{
					$intersect = array_intersect(array_keys($type1), array_keys($type2));
					$matrix[$x][$y] = count($intersect);
					$matrix[$y][$x] = count($intersect);
				}
			}		
		}
		
		$this->set('matrix', $matrix);
		$this->set('matrix_error_types', array_keys($errors));
	}

	/**
	 * 
	 */
	function set_errors_correlation_matrix($subcorpus_id){
		$rows = $subcorpus_id ? DbReport::getReports(null, $subcorpus_id) : DbReport::getReports(3);
		$errors = array();
		$authors = array();
		$avgs = array();
		$devs = array();

		$id=0;
		foreach ($rows as $row){			
			list($author, $x) = explode(".", $row['title']);
			$authors[$author] = $id++;
		}
			
		foreach ($rows as $row){			
			$content = custom_html_entity_decode($row['content']);
			list($author, $x) = explode(".", $row['title']);

			if (preg_match_all('/<corr [^>]*type="([^"]+)"/m', $content, $matches)){
				foreach ($matches[1] as $types){
					foreach (explode(",", $types) as $type){
						$type = trim($type);
						if ( !isset($errors[$type])){
							$errors[$type] = array();
							foreach ($authors as $a) $errors[$type][] = 0; 
						}						
						$author_index = $authors[$author];
						$errors[$type][$author_index]++;
					}
				}
			}
		}
		/* Tablica asocjacyjna typ_błędu => tablica liczby wystąpień dla poszczególnych autorów. */
		ksort($errors);
		
		/* Oblicz macierz korelacji */		
		$matrix = array();
		foreach ( $errors as $x=>$xvalues){
			foreach ( $errors as $y=>$yvalues){
				$matrix[$x][$y] = $this->pearson_correlation($xvalues, $yvalues);
			}		
		}
		
		$this->set('matrix', $matrix);
		$this->set('matrix_error_types', array_keys($errors));
	}
	
	/**
	 * Calculate Pearson correlation for two values distribution.
	 */
	function pearson_correlation($a, $b){
		$avga = array_sum($a)/count($a);
		$avgb = array_sum($b)/count($b);
		$m = 0;
		$l1 = 0;
		$l2 = 0;
		
		for ($i=0; $i<count($a); $i++){
			$va = $a[$i];
			$vb = $b[$i];
			$m += ( ($va-$avga)*($vb-$avgb) );
			$l1 += ($va-$avga)*($va-$avga);
			$l2 += ($vb-$avgb)*($vb-$avgb); 
		}
		
		return $m / sqrt($l1*$l2);
	}
	
		
	/**
	 * 
	 */
	function set_interpuntion_stats(){
		$headers = array("label"=>"Interpunkcja", "count"=>"Wystąpienia");
		
		$rows = db_fetch_rows("SELECT content, id, title, subcorpus_id FROM reports WHERE corpora = 3");
		$subcorpora = db_fetch_rows("SELECT * FROM corpus_subcorpora WHERE corpus_id = 3");
		$seqs = array();
				
		foreach ($subcorpora as $s){
			$headers["sub_".$s['subcorpus_id']] = $s['name'];
		}
				
		foreach ($rows as $row){
			$content = $row['content'];
			$content = strip_tags($content);
			if (preg_match_all('/(\p{P}+)/m', $content, $matches)){
				foreach ($matches[1] as $seq){
					if ( !isset($seqs[$seq]) ){
						$a = array("Interpunkcja"=>$seq, "Wystąpienia"=>0);
						foreach ($subcorpora as $s)
							$a["sub_".$s['subcorpus_id']] = 0;
						$seqs[$seq] = $a; 						
					}
					$seqs[$seq]["count"]++;
					$seqs[$seq]["sub_".$row['subcorpus_id']]++;
				}
			}
		}
		ksort($seqs);
		
		$this->set("interpunction", $seqs);
		$this->set("interpunction_header", $headers);
	}
}

function lps_sort_errors($a, $b){
	if ( $a['count_docs'] < $b['count_docs'] )
		return 1;
	else if ( $a['count_docs'] == $b['count_docs'] )
		return 0;
	else
		return -1;
}

?>


