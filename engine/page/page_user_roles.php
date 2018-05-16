<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_user_roles extends CPage{

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = ROLE_SYSTEM_USER_LOGGEDIN;
        $this->includeJs("libs/bootstrap-sortable/moment.min.js"); // required by bootstrap-sortable.js
        $this->includeJs("libs/bootstrap-sortable/bootstrap-sortable.js");
        $this->includeCss("libs/bootstrap-sortable/bootstrap-sortable.css");
    }

    function execute(){
        global $user;
        $corpus_roles = DbCorpus::getUserCorporaAndRoles($user['user_id']);
        $this->set('corpus_roles', $corpus_roles);
	}
}