<?php
class Ajax_user_login{
	
	function execute(){
		global $auth;
		if ($auth->checkAuth()){
			echo json_encode(array("success"=>1));
		}else{
			echo json_encode(array("error"=>$auth->getStatus()));			
		}		
	}
	
}
?>
