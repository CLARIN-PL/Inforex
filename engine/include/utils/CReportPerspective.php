<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class ReportPerspective{
	
	var $id = null;
	var $title = null;
	
	function __construct($id=null, $title=null){
		$this->id = $id;
		$this->title = $title;
	}
}
?>

