<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_get_sens extends CPage {
	var $isSecure = false;
	function execute(){
		
		$result = DbSens::getSensDataById($_POST['sens_id']);
		return $result;
	}	
}
?>