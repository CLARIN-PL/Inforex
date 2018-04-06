<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_lps_metric extends CPage{
	
	var $isSecure = true;
	
	function checkPermission(){
		return hasCorpusRole(CORPUS_ROLE_READ);
	}
	
	function execute(){
		global $corpus;
		
		$corpus_id = $corpus['id'];
		$metric = strval($_GET['metric']);
		$class = strval($_GET['class']);		
		$class1 = strval($_GET['class1']);		
		$class2 = strval($_GET['class2']);		
		$stats = null;
		$bucket_size = 10;
		$unit = "";
		
		$poses = array();
		$poses['czasownik'] = array("fin", "bedzie", "aglt", "praet", "impt", 
									"imps", "inf", "pcon", "pant", "ger", 
									"pact", "ppas", "winien");
		$poses['liczebnik'] = array("num", "numcol");
		$poses['przymiotnik'] = array("adj", "adja", "adjp", "adjc");
		$poses['rzeczownik'] = array("subst","depr");
		$poses['spójnik'] = array("conj", "comp");
		$poses['zaimek'] = array("ppron12", "ppron3", "siebie");
		
		$class1_name = $class1;
		if ( isset($poses[$class1]) )
			$class1 = $poses[$class1];

		$class2_name = $class2;
		if ( isset($poses[$class2]) )
			$class2 = $poses[$class2];
							
		if ( !in_array($metric, array("tokens", "class", "ratio")))
			$metric = "tokens";
		
		if ( $metric == "tokens" )
			$stats = DbCorpusStats::getDocumentLengthsInSubcorpora($corpus_id);
		elseif ( $metric == "class" ){
			$stats = DbCorpusStats::getDocumentClassCountsNormInSubcorpora($class, $corpus_id);
			$unit = "%";
		}
		elseif ( $metric == "ratio" ){
			$stats = DbCorpusStats::getDocumentClassCountsRatioInSubcorpora($class1, $class2, $corpus_id);
			$bucket_size = number_format($this->getGroupMaxValue($stats)/15, 1, ".", "");
		}
		
		$this->set('stats', $this->groupIntoBuckets($stats, $bucket_size));
		$this->set('classes', Tagset::getSgjpClasses());
		$this->set('poses', $poses);
		$this->set('metric', $metric);
		$this->set('class', $class);
		$this->set('class1', $class1_name);
		$this->set('class2', $class2_name);
		$this->set('unit', $unit);
	}
	
	/**
	 * Transforms
	 *   [group_id] => array(counts) 
	 * into
	 *   [bucket] = array( [group_id] => count )
	 */
	function groupIntoBuckets($groups, $bucket_size=10){
		$max = 0;
		$bucket_size = $bucket_size == 0 ? 1 : $bucket_size;
		foreach ($groups as $name=>$count)
			foreach ($count as $c)
				$max = max((int)$c['count'], $max);
		
		$buckets = ceil($max/$bucket_size);
		
		$stats = array();
		
		foreach ($groups as $name=>$count)
			$stats[-1][] = $name;
		
		for ($i=0; $i<=$buckets; $i++){
			$stats["" . $i*$bucket_size] = array();
			foreach ($groups as $name=>$count)
				$stats["" . $i*$bucket_size][] = 0;
		}

		$i=0;
		foreach ($groups as $name=>$count){				
			foreach ($count as $c){
				if ($c['count']==0)
					$stats[0][$i]++;
				else{
					$index = ceil($c['count']/$bucket_size) * $bucket_size;
					$stats["" . $index][$i]++;
				}
			}
			$i++;
		}
		return $stats;				
	}
	
	function getGroupMaxValue($groups){
		$max = 0;
		foreach ($groups as $name=>$count)
			foreach ($count as $c)
				$max = max((int)$c['count'], $max);
		return $max;		
	}
}
?>


