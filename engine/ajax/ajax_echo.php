<?php

class Ajax_echo extends CPage{

	function execute(){
		$param = $_POST['param'];
		return "SERVER SAYS: ".$param;
	}
}