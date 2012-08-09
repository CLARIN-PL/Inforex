<?php

function exec_shell_asserted($cmd){
	$r = shell_exec($cmd);
	if ( $r == null ){
		throw new Exception("Shell command not executed properly: \n$cmd");
	}
	else
		return $r;	
}

?>