<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty escape_quotes modifier plugin.
 *
 * Type:     modifier<br />
 * Name:     escape_quotes<br />
 * Purpose:  Escape both double and single quotes.
 * @author bjoshua
 * @link http://www.phpinsider.com/smarty-forum/viewtopic.php?p=22951
 * @param string $string
 * @return string
 * @version $Revision: 1.0.0 $
 */
function smarty_modifier_escape_csv($string) {
	$value = str_replace('"', '""', $string); // First off escape all " and make them ""
	if(preg_match('/,/', $value) or preg_match("/\n/", $value) or preg_match('/"/', $value)) { // Check if I have any commas or new lines
		return '"'.$value.'"'; // If I have new lines or commas escape them
	} else {
		return $value; // If no new lines or commas just return the value
	}
}

?>