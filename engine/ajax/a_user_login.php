<?php
class Ajax_user_login extends CPage {
	
	function execute(){
		global $auth;
		if ($auth->checkAuth()){
			echo json_encode(array("success"=>1));
			$user = $auth->getUserData();
			UserActivity::login($user['user_id']);
		}else{
			echo json_encode(array("error"=>$auth->getStatus()));			
		}		
	}
	
}
?>
