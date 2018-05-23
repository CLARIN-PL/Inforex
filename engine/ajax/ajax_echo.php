<?php

class Ajax_echo extends CPagePublic {

	function execute(){
		$param = strval($_POST['param']);
		return "SERVER SAYS: $param";
	}
}