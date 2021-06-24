<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_roles_get extends CPage {

	/**
	 * Zwraca tablice JSON z dostępnymi rolami.
	 */
	function execute(){
        return DbUserRoles::getAllRoles();
	}	
}