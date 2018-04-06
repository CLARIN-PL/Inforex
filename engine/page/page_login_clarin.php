<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_login_clarin extends CPage{
	
	function execute(){
        global $auth;

		$clarinUser = $auth->getClarinUser();

		if($clarinUser){
            $this->set('screenname', $clarinUser['fullname']);
            $this->set('email', $clarinUser['login']);
        } else{
		    $auth->redirectToClarinLogin();
        }
	}
}

?>
