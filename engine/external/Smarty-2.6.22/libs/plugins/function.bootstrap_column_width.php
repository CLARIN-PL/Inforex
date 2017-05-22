<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {bootstrap_column_width} function plugin
 *
 * Type:     function<br>
 * Name:     bootstrap_column_width<br>
 * Purpose:  print column width on visibility of other columns
 * @author Monte Ohrt <monte at ohrt dot com>
 * @link http://smarty.php.net/manual/en/language.function.counter.php {counter}
 *       (Smarty online manual)
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_bootstrap_column_width($params, &$smarty)
{
    $default_flags_width = 1;
    $default_config_width = 3;

    if ( isset($params['flags_width']) ){
        $default_flags_width = $params['flags_width'];
    }

    if ( isset($params['config_width']) ){
        $default_config_width = $params['config_width'];
    }

    $width = $params['default'];
    if ( !$params['flags'] ){
        $width += $default_flags_width;
    }
    if ( !$params['config'] ){
        $width += $default_config_width;
    }
    return $width;
}

/* vim: set expandtab: */

?>
