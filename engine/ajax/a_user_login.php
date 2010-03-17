<?php
class Ajax_user_login{
	
	function execute(){
		global $auth;
		if ($auth->checkAuth()){
			echo json_encode(array("success"=>1));
		}			
	}
	
}
?>
