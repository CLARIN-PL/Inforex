<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_get_words extends CPage {
	function execute(){
		$sens = DbSens::getSensList();
		foreach($sens as $key => $value){
			$sens[$key]['annotation_name'] = substr($sens[$key]['annotation_name'], 4); // obcinanie "wsd_"
		}
		return $sens;
	}	
}