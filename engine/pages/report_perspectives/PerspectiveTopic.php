<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveTopic extends CPerspective {
	
	function execute()
	{
				
		$topics = db_fetch_rows("SELECT * FROM reports_types ORDER BY `name`");
		$this->page->set('topics', $topics);
	}
	
}

?>
