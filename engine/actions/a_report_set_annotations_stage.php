<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Action_report_set_annotations_stage extends CAction{
		
	var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $corpus, $user, $mdb2;
	  	$report_id = $_GET['id'];

	  	$annSub = $_POST['annSub'];
	  	$annChange = $_POST['annChange'];
	  	
	  	$accepted = array();
	  	$discarded = array();
	  	$modify = array();

	  	foreach ($annSub as $ann_id=>$ann_stage){
	  		if ($ann_stage=="accept") 
	  			array_push($accepted, $ann_id);	  		
	  		elseif ($ann_stage=="discard")
	  			array_push($discarded, $ann_id);
	  		elseif ( $ann_stage == "change" )
	  			array_push($modify, array($ann_id, $annChange[$ann_id]));
	  	} 
	  	
	  	/** Zapisz zaakceptowane anotacje */
	  	if (count($accepted)>0){
			$sql = "UPDATE reports_annotations_optimized " .
					"SET stage=\"final\" " .
					"WHERE id " .
					"IN (" . implode(",",$accepted) . ")";
			db_execute($sql);
	  	}
	  	
	  	/** Zapisz odrzucone anotacje */
	  	if (count($discarded)>0){
			$sql = "UPDATE reports_annotations_optimized " .
					"SET stage=\"discarded\" " .
					"WHERE id " .
					"IN (" . implode(",",$discarded) . ")";
			db_execute($sql);
	  	}
	  	
	  	/** Skopiuj zmionione anotacje */
	  	if ( count($modify) > 0 ){
	  		$sqlSelect = "SELECT * FROM reports_annotations WHERE id = ?";
	  		$sqlDublet = "SELECT COUNT(*) FROM reports_annotations" .
	  				" WHERE `from` = ? AND `to` = ? AND type = ? AND stage = 'final'";
	  		$sqlUpdate = "UPDATE reports_annotations_optimized SET stage='discarded' WHERE id = ?";
	  		$sqlInsert = "INSERT INTO reports_annotations_optimized (`from`,`to`,`type_id`,`text`,`report_id`,`stage`,`source`,`user_id`)" .
	  				" VALUES(?, ?, ?, ?, ?, 'final', 'user', ?)";
	  		
	  		foreach ($modify as $pair){
	  			list($id, $type) = $pair;
	  			$a = db_fetch($sqlSelect, array($id));
	  			if ( db_fetch_one($sqlDublet, array($a['from'], $a['to'], $type)) == 0 ){
	  				db_execute($sqlInsert, array($a['from'], $a['to'], $type, $a['text'], $a['report_id'], $user['user_id']));
	  			}	  				  				  				
	  			db_execute($sqlUpdate, array($id));
	  		}
	  	}
			  	
		return null;
	}
	
} 

?>
