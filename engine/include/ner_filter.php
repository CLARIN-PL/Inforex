<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-02-23
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
function ner_filter($annotation_text){
	$uc = "([A-Z]|Ą|Ż|Ś|Ź|Ę|Ć|Ń|Ó|Ł)([a-z]|ą|ż|ś|ź|ę|ć|ń|ó|ł)*";
	return preg_match("/^$uc( $uc)*(( - |-)$uc)?( \($uc\))?$/", $annotation_text)>0;
	
}
?>
