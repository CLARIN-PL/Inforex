<?php
class Ajax_users_get extends CPage {
	
	function execute(){
		global $db;

		$sql = "SELECT user_id, screename FROM users";
		echo json_encode($db->fetch_rows($sql));		
	}	
}
?>
