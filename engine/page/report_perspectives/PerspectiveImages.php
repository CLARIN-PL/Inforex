<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveImages extends CPerspective {
	
	function execute(){
		$row = $this->page->get("row");
		$report_id = $row['id'];
		$images = array_chunk(DbImage::getReportImages($report_id), 3);
		$this->page->set("images", $images);
	}

}

?>
