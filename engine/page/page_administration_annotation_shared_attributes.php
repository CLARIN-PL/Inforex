<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_annotation_shared_attributes extends CPageAdministration {

	function execute(){
		$sql = "SELECT id, name, type, description FROM shared_attributes";
		$sharedAttributes = db_fetch_rows($sql);
		$this->set("sharedAttributes", $sharedAttributes);
	}
}


?>