<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_get_details extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do pobierania danych.";
	}
	
	function execute(){
		global $db;

		$corpusId = $_POST['corpus_id'];
		$element_name = $_POST['element_name'];
		
		if ($element_name == 'corpus')
			$sql = "SELECT name, description, public, ext, screename FROM corpora c LEFT JOIN users u ON (c.user_id=u.user_id) WHERE c.id=?";
		elseif ($element_name == 'subcorpus')
			$sql = "SELECT subcorpus_id AS id, name, description FROM corpus_subcorpora WHERE corpus_id=?";
		else
			$sql = "SELECT corpora_flag_id AS id, name, short, sort FROM corpora_flags WHERE corpora_id=?";
		return $db->fetch_rows($sql,array($corpusId));
	}
	
}
?>
