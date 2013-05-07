<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
 class CImage extends ATable{
 	
 	var $_meta_table = "images";
 	var $_meta_key = "id";
 	var $_meta_stmt = null;
 	
 	var $id = null;
 	var $corpus_id = null;
 	var $original_name = null;
 	var $hash_name = null;
 	
 	function setKey($id) { $this->id = $id; }
 	function setCorpusId($corpus_id) { $this->corpus_id = $corpus_id; }
 	function setOriginalName($name) { $this->original_name = $name; }
 	function setHashName($name) { $this->hash_name = $name; }
 
 	function getKey() { return $this->id; }
 	function getCorpusId() { return $this->corpus_id; }
 	function getOriginalName() { return $this->original_name; }
 	function getHashName() { return $this->hash_name; }
 	
 	function getServerFileName() { return  $this->id . "_" . $this->hash_name ; }
}
 
 ?>
