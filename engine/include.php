<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
ob_start();

/* Add 'external/pear' path to the include path to override the default path */
$include_paths = array();
$include_paths[] = __DIR__ . '/external/pear';
$include_paths[] = get_include_path();
set_include_path( implode(PATH_SEPARATOR, $include_paths) );

require_once __DIR__ . '/include/vendor/autoload.php';
