<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus extends CPage{

	var $isSecure = true;
	var $subpages = array(
			"information" => "Basic information", 
			"users" => "Users", 
			"users_roles" => "Users roles", 
			"subcorpora" => "Subcorpora",
			"perspectives" => "Perspectives", 
			"flags" => "Flags", 
			"annotation_sets" => "Annotation sets", 
			"event_groups" => "Event groups",
			"corpus_metadata" => "Metadata");

	function checkPermission(){
		global $corpus;
		return hasCorpusRole(CORPUS_ROLE_MANAGER) || isCorpusOwner();
	}
	
	function execute(){		
		
		$subpage = array_key_exists('subpage', $_GET) ? $_GET['subpage'] : "information";
		
		$perspective_class_name = "Perspective".ucfirst($subpage);
		if (class_exists($perspective_class_name)){
			$perspective = new $perspective_class_name($this);
			$perspective->execute();
		}else{
			$this->set("error", "Perspective $subpage does not exist");
		}
		
		$this->set('subpage', $subpage);
		$this->set('subpage_file', "inc_corpus_{$subpage}.tpl");
		$this->set('subpages', $this->subpages);
	}
}

?>
