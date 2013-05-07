<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Get array value with isset check. If value is not set 
 * then return the $default value. In other case return the value as string.
 * @param $array ­— an array
 * @param $index — element key in the array
 * @param $default — default value if the not found
 * @return value of the key of default value
 */
function array_get_str(&$array, $key, $default){
	return isset($array[$key]) ? strval($array[$key]) : $default;
}

function array_get_int(&$array, $key, $default){
	return isset($array[$key]) ? intval($array[$key]) : $default;
}

?>