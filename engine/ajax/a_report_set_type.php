<?php
class Ajax_report_set_type{
	
	function execute(){
		global $mdb2;
		$type = intval($_POST['type']);
		$id = intval($_POST['id']);
		$mdb2->query("UPDATE reports SET type=$type WHERE id=$id");			
		echo json_encode(array("success"=>"1"));
	}
	
}
?>
