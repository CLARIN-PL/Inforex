<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveMetadata extends CPerspective {
	
	function execute()
	{	
		global $corpus;	
		$row = $this->page->get("row");

		$ext_table = DbReport::getReportExtById($row['id']);
		$ext = array();
		
		$features = DbCorpus::getCorpusExtColumns($corpus['ext']);
		$subcorpora = DbCorpus::getCorpusSubcorpora($corpus['id']);
		$statuses = DbStatus::getAll();
		$formats = DbReport::getFormats();

		/* Jeżeli nie ma rozszrzonego wiersza atrybutów, to utwórz pusty */
		if ( $ext_table == null ){
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
		$this->page->set("statuses", $statuses);	
		$this->page->set("formats", $formats);
	}
}

?>