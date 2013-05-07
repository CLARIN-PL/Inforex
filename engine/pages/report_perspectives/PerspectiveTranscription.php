<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveTranscription extends CPerspective {
	
	function execute()
	{
		$orientation = isset($_GET['orientation']) ? $_GET['orientation'] : $_COOKIE['orientation'];
				
		$_COOKIE['orientation'] = $orientation; 
						
		$images = db_fetch_rows("SELECT * FROM reports_and_images ri JOIN images i ON (ri.image_id=i.id) WHERE ri.report_id = ? ORDER BY `position`", array($this->document['id']));
		$this->page->set('images', $images);
		$this->page->set('orientation', $orientation);
	}
	
}

?>
