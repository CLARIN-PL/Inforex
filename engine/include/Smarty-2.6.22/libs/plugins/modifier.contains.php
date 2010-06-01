<?php

/**
 * Smarty shared plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Function: smarty_contains
 * Purpose:  Used to find a string in a string
 * Example: contains( 'Jason was here', 'here' ) returns true
 * Example2: contains( 'Jason was here', 'ason' ) returns false
 * Usage string: {$foo|contains:"bar"}
 * Usage array: {$foo|@contains:"bar"}
 * @author Jason Strese <Jason dot Strese at gmail dot com>
 * @param string
 * @return string
 */
function smarty_modifier_contains($string, $find, $cases = false)
{
   $count = 0;
   if( is_string($string) && !empty($string) )
   {
      if($cases) $count = substr_count($string, $find);
      else $count = substr_count(strtolower($string), strtolower($find) );
   }
   elseif( is_array($string) && count($string) )
   {
      if($cases)
      {
         foreach($string as $str) {
            if($str == $find) $count++;
         }
      } else {
         foreach($string as $str) {
            if(strtolower($str) == strtolower($find)) $count++;
         }
      }
   }
   return $count;
}

/* vim: set expandtab: */ 
?>