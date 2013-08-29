<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_user_add extends CAction{
		
	function checkPermission(){
		return hasRole(USER_ROLE_ADMIN);
	}
	
	function execute(){
		global $db, $mdb2;
		
		$sql = "INSERT INTO users ( login, screename, email, password ) VALUES ('{$_POST['login']}', '{$_POST['name']}', '{$_POST['email']}', MD5('{$_POST['password']}'))";
		$db->execute($sql);
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			$this->set("action_error", "Error: (". $error[1] . ") -> ".$error[2]);
		
		//$corpus_roles = array();
		//$private_corpora = DbCorpus::getCorpora(0);
		//foreach($private_corpora as $corpus){
		//	$corpus_roles[$corpus['id']] = array(CORPUS_ROLE_READ);
		//}
		try{
			//DbUser::addCorpusRoles($mdb2->lastInsertID(), $corpus_roles);
			$this->set("action_performed", "Added user: \"". $_POST['name'] . "\", id: ".$mdb2->lastInsertID());
		} catch(Exception $e){
			$this->set("action_error", "Error: ".$e->getMessage());
		}
			
		return null;
	}	
} 

?>
