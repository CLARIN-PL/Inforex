<?php

class PerspectiveMetadata extends CPerspective {
	
	function execute()
	{	
		global $corpus;	
		$row = $this->page->get("row");

		$ext = DbReport::getReportExtById($row['id']);
		$features = DbCorpus::getCorpusExtColumns($corpus['ext']);
		$subcorpora = DbCorpus::getCorpusSubcorpora($corpus['id']);
		$statuses = DbStatus::getAll();
				
		/* Jeżeli nie ma rozszrzonego wiersza atrybutów, to utwórz pusty */
		if ( !$ext ){
			DbReport::insertEmptyReportExt($row['id']);
			$ext = DbReport::getReportExtById($row['id']);
		}
		
		$features_index = array();
		foreach ($features as &$f){
			$features_index[$f['field']] = &$f;
		}
		
		foreach ($ext as $k=>$v){
			if ($k != "id")
				$features_index[$k]['value'] = $v;
		}	
		
		fb($features_index);

		$this->page->set("content", Reformat::xmlToHtml($row['content']));		
		$this->page->set("features", $features);
		$this->page->set("subcorpora", $subcorpora);
		$this->page->set("statuses", $statuses);	}

}

?>