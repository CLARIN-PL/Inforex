<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveTei extends CPerspective {
	
	function execute()
	{
		try{
			$this->page->set('tei_header', TeiFormater::report_to_header($this->document));
			$this->page->set('tei_text', TeiFormater::report_to_text($this->document));
		}
		catch(Exception $ex){
			$this->page->set('structure_corrupted', 1);
		}
	}
	
}

?>
