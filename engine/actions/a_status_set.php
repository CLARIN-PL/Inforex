<?php

class Action_status_set extends CAction{
	
	function execute(){
		
		$status = intval($_GET['status']);
		$page = strval($_GET['page']);
		
		if (!in_array($status, array(0, 1, 2, 3, 5)))
			$status = 2;
		
		HTTP_Session2::set('status', $status);
		
		return $page;
	}
	
} 

?>
