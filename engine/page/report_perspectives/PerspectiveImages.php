<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveImages extends CPerspective {
	
	function execute(){
		$row = $this->page->report;
		$reportId = $row['id'];
		$images = array_chunk(DbImage::getReportImages($reportId), 3);
		$this->page->set("images", $images);
	}
}
?>
