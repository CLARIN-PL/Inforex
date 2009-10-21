<?php
function smarty_modifier_format_annotations($string)
{
	$string = str_replace("<an:$k>", "<span class='$k'>[?]", $string);
	$string = preg_replace('/<an#(\d+):([a-z_]+)>/', "<small title='an#$1:$2'>[#$1]</small><span id='an$1' class='$2' title='an#$1:$2'>", $string);
	$string = str_replace("</an>", "</span>", $string);
	return $string;
}
?>
