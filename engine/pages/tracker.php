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
		global $mdb2, $corpus;
		
		$sql = "SELECT count(*) FROM reports_annotations WHERE user_id=5";
		$annotation_count = $mdb2->query($sql)->fetchOne();
		$this->set("annotation_count", $annotation_count);

		$sql = "SELECT * FROM reports_annotations WHERE user_id=5 ORDER BY creation_time DESC";
		$annotations = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
		
		$tracker = array();
		
		$prev = null;
		$first = null;
		$count = 0;
		$sum == 0;
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
		
		
//		$sql = "SELECT count(*)" .
//				" FROM reports r JOIN reports_annotations a ON (r.id = a.report_id)" .
//				" WHERE status=2 AND corpora={$corpus['id']}";
//		$annotation_count = $mdb2->query($sql)->fetchOne();
//		$annotation_count = db_fetch_one($sql);
//		
//		$sql = "SELECT a.type, COUNT(*) AS count, COUNT(DISTINCT(a.text)) AS `unique`" .
//				" FROM reports_annotations a" .
//				" JOIN reports r ON (r.id = a.report_id)" .
//				" WHERE r.corpora={$corpus['id']}" .
//				" GROUP BY a.type" .
//				" ORDER BY a.type;";
////		if (PEAR::isError($r = $mdb2->query($sql)))
////			die("<pre>{$r->getUserInfo()}</pre>");
////		$annotations_count = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
//		$annotations_count = db_fetch_rows($sql); 
//
//		$sql = "SELECT a.type, a.text, COUNT(*) AS count, COUNT(DISTINCT(text)) AS `unique`" .
//				" FROM reports_annotations a" .
//				" JOIN reports r ON (r.id = a.report_id)" .
//				" WHERE r.corpora={$corpus['id']}" .
//				" GROUP BY a.type, a.text" .
//				" ORDER BY a.type, count DESC";
//		if (PEAR::isError($r = $mdb2->query($sql)))
//			die("<pre>{$r->getUserInfo()}</pre>");
//		$annotations = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
//		$annotation_map = array();
//		$annotation_type = "";
//		$annotation_list = array();
//
//		foreach ($annotations as $an){
//			$annotation_map[$an['type']][] = $an;			
//		}
//
//		// scal listę anotacji z listą szczegółową anotacji
//		foreach ($annotations_count as $k=>$an){
//			$annotations_count[$k]['details'] = $annotation_map[$an['type']];
//		}
//		$this->set('annotation_count', number_format($annotation_count, 0, "", "."));
//		$this->set('tags', $annotations_count);				
	}
}


?>
