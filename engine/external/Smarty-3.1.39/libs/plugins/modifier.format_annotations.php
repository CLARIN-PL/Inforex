<?php
function smarty_modifier_format_annotations($string)
{
	$string = stripslashes($string);
	$string = preg_replace("/<br>|<\/br>/","<div></div>",$string);
	$string = str_replace('<an:$k>', '<span class=\'$k\'>[?]', $string);
	//$string = preg_replace('/<an#(\d+):([a-z_]+)>/', "<small title='an#$1:$2'>[#$1]</small><span id='an$1' class='$2' title='an#$1:$2'>", $string);
    $string = preg_replace('/<an#(\d+):([^:]+):(\d+):(\d+):\'(.*?)\'>/u', "<span id='an$1' class='ann annotation_set_$3 $2' groupid='$3' subgroupid='$4' lemma='$5' title='an#$1:$2'>", $string);
	$string = preg_replace('/<an#(\d+):([^:]+):(\d+):(\d+)>/u', "<span id='an$1' class='ann annotation_set_$3 $2' groupid='$3' subgroupid='$4' title='an#$1:$2'>", $string);
	$string = preg_replace('/<an#(\d+):([^:]+):(\d+)>/u', "<span id='an$1' class='ann annotation_set_$3 $2' groupid='$3' title='an#$1:$2'>", $string);
	$string = preg_replace('/<an#(\d+):([a-z0-9_]+)>/', "<span id='an$1' class='$2' title='an#$1:$2'>", $string);
    $string = preg_replace('/<an#(\d+):([a-z0-9_]+) eos>/', "<span id='an$1' class='$2 eos' title='an#$1:$2'>", $string);
	$string = str_replace("</an>", "</span>", $string);
    // Browsers ignore content of <title>...</title> tags pair. If content
    // document include this sequence we must convert tag name to entity
    $string = preg_replace("/<title/","<titl&#101;",$string);
    $string = preg_replace("/<\/title/","</titl&#101;",$string);
	return $string;
}
