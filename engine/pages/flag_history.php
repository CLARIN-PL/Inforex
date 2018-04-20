<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_flag_history extends CPage{

    var $isSecure = true;

    function checkPermission(){
        return hasCorpusRole(CORPUS_ROLE_FLAG_HISTORY);
    }

    function execute(){
        global $corpus;

        $corpus_id = $corpus['id'];

        $selected_user = $_COOKIE['corpus_flag_history_user'];
        $selected_flag = $_COOKIE['corpus_flag_history_flag'];

        $flag_history = DbCorporaFlag::getCorpusFlagHistory($corpus_id, $selected_user, $selected_flag);
        $users = DbCorporaFlag::getCorpusFlagChangeUsers($corpus_id);
        $flags = DbCorporaFlag::getCorpusFlags($corpus_id);

        $this->set('flag_history', $flag_history);
        $this->set('users', $users);
        $this->set('flags', $flags);
    }

}
