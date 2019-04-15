<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {math} function plugin
 *
 * Type:     function<br>
 * Name:     math<br>
 * Purpose:  handle math computations in template<br>
 * @link http://smarty.php.net/manual/en/language.function.math.php {math}
 *          (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_perspectivecolwidth($params, &$smarty)
{
    if (empty($params['base'])) {
        $smarty->trigger_error("missing base parameter");
        return;
    }

    $width = $params['base'];

    $vars = &$smarty->get_template_vars();

    if ( !$vars['config_active'] ){
        $width += $params['config'];
    }

    if ( !$vars['flags_active'] ){
        $width += 1;
    }

    return $width;
}

/* vim: set expandtab: */

?>
