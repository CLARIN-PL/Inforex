<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class WcclRelation{
	
	var $type = null;
	var $source_sentence_id = null;
	var $source_channal_name = null;
	var $source_id = null;
	var $target_sentence_id = null;
	var $target_channal_name = null;
	var $target_id = null;
		
	function __construct($type, 
			$source_sentence_id, $source_channal_name, $source_id, 
			$target_sentence_id, $target_channal_name, $target_id){
		$this->type = $type;
		$this->source_sentence_id = $source_sentence_id;
		$this->source_channal_name = $source_channal_name;
		$this->source_id = $source_id;
		$this->target_sentence_id = $target_sentence_id;
		$this->target_channal_name = $target_channal_name;
		$this->target_id = $target_id;				
	}
}

?>