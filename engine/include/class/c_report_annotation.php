<?php
/**
 * @package MyCMS
 * @subpackage LCMS
 * @author Michał Marcińczuk <marcinczuk@gmail.com>
 **/
 
 class CReportAnnotation extends ATable{
 	
 	var $_meta_table = "reports_annotations";
 	var $_meta_key = "id";
 	var $_meta_stmt = null;
 	
 	var $id = null;
 	var $report_id = null;
 	var $from = null;
 	var $to = null;
 	var $type = null;
 	var $text = null;
}
 
 ?>
