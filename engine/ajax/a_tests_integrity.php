<?php
class Ajax_tests_integrity extends CPage {
	function execute(){
		global $db;
		
		$new_name = $_POST['newwordname'];
					
		echo json_encode(array("success" => 1));
	}	
}
?>