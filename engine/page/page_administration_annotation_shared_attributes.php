<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_annotation_shared_attributes extends CPageAdministration {

	function execute(){
		$this->set("sharedAttributes", CDbAnnotationSharedAttribute::getAll());
	}
}
