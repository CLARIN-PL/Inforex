<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty cat modifier plugin
 *
 * Type:     modifier<br>
 * Name:     has_role<br>
 * Date:     June 1, 2010
 * Purpose:  check whether logged in user has given role
 * Input:    corpus role name
 * Example:  {"read"|has_corpus_role}
 * @author   Michał Marcińczuk <marcinczuk at gmail dot com>
 * @version 1.0
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_has_role($string)
{
    return hasRole($string);
}

/* vim: set expandtab: */

?>
