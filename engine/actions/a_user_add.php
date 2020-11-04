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
		
		$sql = "INSERT INTO users ( login, screename, email, password ) VALUES ('{$_POST['login']}', '{$_POST['name']}', '{$_POST['email']}', MD5('{$_POST['password']}'))";
		$this->getDb()->execute($sql);
		$error = $this->getDb()->errorInfo();
		if(isset($error[0]))
			$this->set("action_error", "Error: (". $error[1] . ") -> ".$error[2]);
		
		//$corpus_roles = array();
		//$private_corpora = DbCorpus::getCorpora(0);
		//foreach($private_corpora as $corpus){
		//	$corpus_roles[$corpus['id']] = array(CORPUS_ROLE_READ);
		//}
		try{
			$this->set("action_performed", "Added user: \"". $_POST['name'] . "\", id: ".$this->getDb()->last_id());
		} catch(Exception $e){
			$this->set("action_error", "Error: ".$e->getMessage());
		}

        unset($_POST);
			
		return null;
	}	
} 

?>
