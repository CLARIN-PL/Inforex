<?php
function smarty_modifier_format_annotations($string)
{
	$tab['amount'] = 'GreenYellow';
	$tab['company'] = 'LightSteelBlue';
	$tab['date'] = 'Gold';
	$tab['person'] = 'PeachPuff';
	foreach ($tab as $k=>$v){
		$string = str_replace("<an:$k>", "<span style='background: red'>[?]", $string);
		$string = preg_replace('/<an#(\d+):'.$k.'>/', "<small style='color: grey'>[#$1]</small><span style='background: $v'>", $string);
	}
	$string = str_replace("</an>", "</span>", $string);
	return $string;
}
?>
