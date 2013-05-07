<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
if (!function_exists('mb_sprintf')) {
	function mb_sprintf($format) {
	  $args = func_get_args();
	
	  for ($i = 1; $i < count($args); $i++) {
	    $args [$i] = iconv('UTF-8', 'ISO-8859-2', $args [$i]);
	  }
	 
	  return iconv('ISO-8859-2', 'UTF-8', call_user_func_array('sprintf', $args));
 	}
}
?>
