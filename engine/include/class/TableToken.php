<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
 class TableToken extends ATable{
 	
 	var $_meta_table = "tokens";
 	var $_meta_key = "token_id";
 	var $_meta_stmt = null;
 	
 	var $token_id = null;
 	var $report_id = null;
 	var $from = null;
 	var $to = null;
 	var $eos = null;

 	function getTokenId(){
 		return $this->token_id;
	}

 	function setReportId($report_id){
		$this->report_id = $report_id;
	}

	function getReportId(){
 		return $this->report_id;
	}
	
	function setFrom($from){
		$this->from = $from;
	}

	function getFrom(){
 		return $this->from;
	}
	
	function setTo($to){
		$this->to = $to;
	}

	function getTo(){
 		return $this->to;
	}
	
	function setEos($eos){
		$this->eos = $eos;
	}

     function getEos(){
         return $this->eos;
     }

}