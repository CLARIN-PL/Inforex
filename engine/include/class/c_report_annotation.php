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
 	var $user_id = null;
 	var $creation_time = null;
 	var $stage = null;
 	var $source = null;
 	
 	function setReportId($report_id){
		$this->report_id = $report_id;
	}
	
	function setFrom($from){
		$this->from = $from;
	}
	
	function setTo($to){
		$this->to = $to;
	}
	
	function setType($type){
		$this->type = $type;
	}
	
	function setText($text){
		$this->text = $text;
	}
	
	function setUserId($user_id){
		$this->user_id = $user_id;
	}
	
	function setCreationTime($time){
		$this->creation_time = $time;
	}
	
	function setStage($stage){
		$this->stage = $stage;
	}
	
	function setSource($source){
		$this->source = $source;
	}
}
 
 ?>
