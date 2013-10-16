<?php


class Ajax_echo{

	function execute(){
		$param = $_POST['param'];
		return "SERVER SAYS: ".$param;
	}
}


?>