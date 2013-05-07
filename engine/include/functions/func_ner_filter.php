<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
function ner_filter($annotation_text){
	$uc = "([A-Z]|Ą|Ż|Ś|Ź|Ę|Ć|Ń|Ó|Ł)([a-z]|ą|ż|ś|ź|ę|ć|ń|ó|ł)*";
	return preg_match("/^$uc( $uc)*(( - |-)$uc)?( \($uc\))?$/", $annotation_text)>0;
	
}
?>
