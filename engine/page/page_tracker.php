<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_tracker extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $corpus;
		
		$sql = "SELECT count(*) FROM reports_annotations WHERE user_id=5";
		$annotation_count = $this->getDb()->fetch_one($sql);
		$this->set("annotation_count", $annotation_count);

		$sql = "SELECT * FROM reports_annotations WHERE user_id=5 ORDER BY creation_time DESC";
		$annotations = $this->getDb()->fetch_rows($sql);
		
		$tracker = array();
		
		$prev = null;
		$first = null;
		$count = 0;
		$sum = 0;
		foreach($annotations as $an){
			if ($prev == null){ 
				$prev = $an;
				$first = $an;
				$count = 1;
			}else{
				if (strtotime($prev['creation_time'])-strtotime($an['creation_time'])>30*60){
					$time = strtotime($first['creation_time'])-strtotime($prev['creation_time']);
					$sum += $time;
					
					$track = array();
					$track['from'] = $prev['creation_time'];
					$track['to'] = $first['creation_time'];
					$track['time'] = round($time/60);
					$track['count'] = $count;
					$track['sum'] = number_format($sum/3600, 2, ",", "");
					$tracker[] = $track;
					
					$prev = $an;
					$first = $an;
					$count = 1;
				}else{
					$prev = $an;
					$count++;
				}
			}
		}
		
		$this->set('tracker', $tracker);
		//print_r($annotations);
		
		
	}
}


?>
