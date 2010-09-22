<?php
class Page_user_roles extends CPage{

	var $isSecure = true;
	var $roles = array("loggedin");
	
	function execute(){		
		global $mdb2, $corpus, $user;		
	}
}


?>
