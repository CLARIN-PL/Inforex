<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_subcorpus_add extends CPageCorpus {

	function execute(){
		global $corpus;

		$sql = "INSERT INTO corpus_subcorpora (corpus_id, name, description) VALUES (?, ?, ?) ";
		$corpus_id = $corpus['id'];
		$corpus_name = strval($_POST['name_str']);
		$corpus_desc = strval($_POST['desc_str']);
				
		ob_start();
		$this->getDb()->execute($sql, array($corpus_id, $corpus_name, $corpus_desc));
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
