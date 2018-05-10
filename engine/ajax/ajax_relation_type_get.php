<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_relation_type_get extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = ROLE_SYSTEM_EDITOR_SCHEMA_RELATIONS;
    }
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}
		$parent_id = intval($_POST['parent_id']);
		$parent_type = $_POST['parent_type'];
		
		if ($parent_type=="relation_set"){
			$sql = "SELECT id, name, description FROM relation_types WHERE relation_set_id={$parent_id}";
		}
				
		$result = db_fetch_rows($sql);
		return $result;
	}
	
}
