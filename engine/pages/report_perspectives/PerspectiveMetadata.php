<?php

class PerspectiveMetadata extends CPerspective {
	
	function execute()
	{		
		$row = $this->page->get("row");

		$ext = DbReport::getReportExtById($row['id']);
		
		/* Jeżeli nie ma rozszrzonego wiersza atrybutów, to utwórz pusty */
		if ( !$ext ){
			DbReport::insertEmptyReportExt($row['id']);
			$ext = DbReport::getReportExtById($row['id']);
		}
		
		$features = array();
		foreach ($ext as $k=>$v){
			if ($k != "id")
				$features[] = array( "name"=>$k, "value"=>$v, "title"=>$k);
		}	
		
		$this->page->set("features", $features);
	}

}

?>
