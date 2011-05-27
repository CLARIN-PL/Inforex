<?php
/*include("../include/anntakipi/ixtTakipiAligner.php"); 
include("../include/anntakipi/ixtTakipiStruct.php"); 
include("../include/anntakipi/ixtTakipiDocument.php"); 
include("../include/anntakipi/ixtTakipiHelper.php"); */


class Action_report_set_annotations_stage extends CAction{
		
	var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $corpus, $user, $mdb2;
	  	$report_id = $_GET['id'];
	  	$annSub = $_POST['annSub'];
	  	$accepted = array();
	  	$discarded = array();
	  	foreach ($annSub as $ann_id=>$ann_stage){
	  		if ($ann_stage=="accept") array_push($accepted, $ann_id);
	  		else array_push($discarded, $ann_id);
	  	} 
	  	if (count($accepted)>0){
			$sql = "UPDATE reports_annotations " .
					"SET stage=\"final\" " .
					"WHERE id " .
					"IN (" . implode(",",$accepted) . ")";
			db_execute($sql);
	  	}
	  	if (count($discarded)>0){
			$sql = "UPDATE reports_annotations " .
					"SET stage=\"discarded\" " .
					"WHERE id " .
					"IN (" . implode(",",$discarded) . ")";
			db_execute($sql);
	  	}
			  	
	  	//$this->set("message","Tokens successfully set");
  		//$this->set("error","Wrong file format");		  		
		  	
		  	
		return null;
	}
	
} 

?>
