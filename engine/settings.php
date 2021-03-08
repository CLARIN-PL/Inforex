<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Server configuration
 */  
date_default_timezone_set("Europe/Warsaw");
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set("display_errors", 1);
ini_set("output_buffering", 0);
ini_set("short_open_tag",1);
setlocale(LC_CTYPE, "en_US.UTF-8");		

error_reporting(E_ALL);

function inforexCentralErrorHandler($level, $message, $file = ’’, $line = 0) {
	print("[".$level."] ".$message." in ".$file.":".$line."<br/>\n");
	//throw new ErrorException($message, 0, $level, $file, $line);
}

set_error_handler(inforexCentralErrorHandler);

?>
