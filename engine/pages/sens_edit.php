<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_sens_edit extends CPage{

	function execute(){		
		$sens = DbSens::getSensList();
		foreach($sens as $key => $value){
			$sens[$key]['annotation_type'] = substr($sens[$key]['annotation_type'], 4); // obcinanie "wsd_" 
		}
		$this->set("sensList", $sens);
	}
}

?>