<?php
/**
 * @package MyCMS
 * @subpackage LCMS
 * @author Michał Marcińczuk <marcinczuk@gmail.com>
 **/
 
 class CCorpus extends ATable{
 	
 	var $_meta_table = "corpora";
 	var $_meta_key = "id";
 	var $_meta_stmt = null;
 	
 	var $id = null;
 	var $name = null;
 	var $description = null;
 	var $public = null;
 	var $user_id = null;
 	var $ext = null;
}
 
 ?>
