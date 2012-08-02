<?php
/*
 * Created on Jul 23, 2012
 */
class Ajax_semquel_get_sql extends CPage {
	var $isSecure = false;

	function execute(){
	
		global $config;
		
		$sql = $_POST['semquel'];
		$db2 = new Database($config->relation_marks_db);
		
		echo json_encode(array("success" => 1, "output" => $db2->fetch_rows($sql)));
	}	
}
?>
