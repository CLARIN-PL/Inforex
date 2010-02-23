<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-02-11
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

// List of unicode characters that should be replaced by a question mark.
$takipihelper_question_mark = array(chr(239).chr(130).chr(183), chr(239).chr(128).chr(173), chr(239).chr(131).chr(152), chr(194).chr(178));
// List of unicode characters that should be replaced by other characters.
$takipihelper_replacements = array("½"=>"1/2");


class TakipiHelper{
	
	// Replace a set of characters that is replaced by takipi while tagging.
	static function replace($text){
		global $takipihelper_question_mark;
		global $takipihelper_replacements;
		$text = str_replace($takipihelper_question_mark, "?", $text);
		$text = str_replace(array_keys($takipihelper_replacements), array_values($takipihelper_replacements), $text);
		return $text;
	}
	
}
?>
