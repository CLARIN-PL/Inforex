<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

interface IHtmlParser2 {

	public function __construct(&$content);
	public function getObjects($recognize_tags);
	
}

?>
