<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Abstract class defines a corpus perspective.
 * 
 */
abstract class CCorpusPerspective {
	
	var $page = null;
	
	function __construct(CPage &$page)
	{
		$this->page = $page;
        $this->page->includeJs("js/c_autoresize.js");

    }
	
	abstract function execute();

    // this method is used also by PerspectivePerspectives
    function set_users_roles(){
        global $corpus;
        $roles = $this->page->getDb()->fetch_rows("SELECT *" .
                " FROM users_corpus_roles us " .
                " RIGHT JOIN users u ON (us.user_id=u.user_id AND us.corpus_id={$corpus['id']})" .
                " WHERE u.user_id != {$corpus['user_id']}" .
                " ORDER BY u.screename");

        $users_roles = array();
        foreach ($roles as $role){
            $users_roles[$role['user_id']]['role'][] = $role['role'];
            $users_roles[$role['user_id']]['screename'] = $role['screename'];
            $users_roles[$role['user_id']]['user_id'] = $role['user_id'];
        }
        foreach($users_roles as $key => $u_roles){
            if(!in_array(CORPUS_ROLE_READ,$u_roles['role']))
                unset($users_roles[$key]);
        }
        $this->page->set('users_roles', $users_roles);
    }
}
?>
