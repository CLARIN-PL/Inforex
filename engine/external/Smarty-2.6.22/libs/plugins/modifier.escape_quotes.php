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
function smarty_modifier_escape_quotes($string) {
   $string = preg_replace('/"/', '\"', $string);
   return preg_replace("/'/", "\\\'", $string);
}

?>