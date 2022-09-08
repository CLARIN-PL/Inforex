<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

interface IHtmlTag {
	
    const HTML_TAG_OPEN = 1;
    const HTML_TAG_CLOSE = 2;
    const HTML_TAG_SELF_CLOSE = 3;

	public function __construct($name, $type, $str);
	public function toString();
	public function getName();
	public function getType();

}

?>
