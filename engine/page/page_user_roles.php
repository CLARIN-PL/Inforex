<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_user_roles extends CPage{

	var $isSecure = true;
	var $roles = array("loggedin");
	
	function execute(){		
	}
}
?>