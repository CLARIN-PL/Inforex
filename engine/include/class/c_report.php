<?php
/**
 * @package MyCMS
 * @subpackage LCMS
 * @author Michał Marcińczuk <marcinczuk@gmail.com>
 **/
 
 class CReport extends ATable{
 	
 	var $_meta_table = "reports";
 	var $_meta_key = "id";
 	var $_meta_stmt = null;
 	
 	var $id = null;
 	var $corpora = null;
 	var $date = null;
 	var $title = null;
 	var $source = null;
	var $author = null; 	
	var $content = null; 	
	var $type = null; 	
	var $status = null; 	
	var $user_id = null; 	
}
 
 ?>
