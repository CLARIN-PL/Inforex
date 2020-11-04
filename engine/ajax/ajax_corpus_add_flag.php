<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_add_flag extends CPageCorpus {

	
	function execute(){
		global $corpus;

		$corpusId = intval($corpus['id']);
		$flagName = strval($_POST['name_str']);
		$flagShort = strval($_POST['short_str']);
		$flagDesc = strval($_POST['desc_str']);
		$flagSort = intval($_POST['element_sort']);

		$sql = "INSERT INTO corpora_flags (corpora_id, name, short, description, sort) VALUES (?, ?, ?, ?, ?)";
		ob_start();
		$this->getDb()->execute($sql, array($corpusId, $flagName, $flagShort, $flagDesc, $flagSort));
		
		$error_buffer_content = ob_get_contents();
		ob_clean();
		if(strlen($error_buffer_content))
			throw new Exception("Error: ". $error_buffer_content);
		else{
			$last_id = $this->getDb()->last_id();
			return array("last_id"=>$last_id);
		}
	}	
}
