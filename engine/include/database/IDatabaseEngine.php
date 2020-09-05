<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Abstract Database Engine interface.
 */


interface IDatabaseEngine {

	function __construct($dsn);
	function disconnect();
	function exec($query);
	function query($query); 
	function prepare($query);
	function lastInsertID(); 
	function quote($value, $type = null, $quote = true, $escape_wildcards = false);
 	function errorInfo($error = null);

}

?>
