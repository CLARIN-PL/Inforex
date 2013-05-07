<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
function exec_shell_asserted($cmd){
	$r = shell_exec($cmd);
	if ( $r == null ){
		throw new Exception("Shell command not executed properly: \n$cmd");
	}
	else
		return $r;	
}

?>