<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_set_filter extends CPage{
	
	function execute(){
		$statues = db_reports_get_statuses();
		$types = db_reports_get_types();
		
		$this->set('statuses', $statues);
		$this->set('types', $types);
	}
}


?>
