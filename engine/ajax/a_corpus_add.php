<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_add extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $user, $mdb2;

		$attrs = array();
		$attrs['name'] = strval($_POST['name']);
		$attrs['description'] = strval($_POST['description']);
		$attrs['user_id'] = $user['user_id'];
		$attrs['public'] = $_POST['ispublic'] === "true";
		$attrs['ext'] = "";

		$db->insert("corpora", $attrs); 
		$last_id = $db->last_id();
		
		return array("last_id"=>$last_id);
	}
	
}
?>
