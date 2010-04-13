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
 	var $link = null;
 	var $html_downloaded = null;
 	var $number = null;
	var $company = null; 	
	var $content = null; 	
	var $type = null; 	
	var $status = null; 	
	var $formated = null;
	var $user_id = null; 	
}
 
 ?>
