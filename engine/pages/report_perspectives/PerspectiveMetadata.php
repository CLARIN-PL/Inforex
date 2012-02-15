<?php

class PerspectiveMetadata extends CPerspective {
	
	function execute()
	{		
		$row = $this->page->get("row");

		$ext = DbReport::getReportExtById($row['id']);
		
		$features = array();
		foreach ($ext as $k=>$v){
			if ($k != "id")
				$features[] = array( "title"=>$k, "value"=>$v);
		}	
		
		$this->page->set("features", $features);
	}

}

?>
