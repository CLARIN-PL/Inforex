<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_corpus_flag_history extends CPage{

    var $isSecure = true;

    function checkPermission(){
        return hasCorpusRole(CORPUS_ROLE_FLAG_HISTORY);
    }

    function execute(){
        global $corpus;

        $corpus_id = $corpus['id'];
        $page_size = 20;
        $current_page = max(1, intval(isset($_GET['history_page']) ? $_GET['history_page'] : 1));
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $selected_user = isset($_COOKIE['corpus_flag_history_user']) ? $_COOKIE['corpus_flag_history_user'] : null;
        $selected_flag = isset($_COOKIE['corpus_flag_history_flag']) ? $_COOKIE['corpus_flag_history_flag'] : null;

        $total_results = DbCorporaFlag::countCorpusFlagHistory($corpus_id, $selected_user, $selected_flag, $search);
        $total_pages = max(1, (int)ceil($total_results / $page_size));
        $current_page = min($current_page, $total_pages);
        $offset = ($current_page - 1) * $page_size;
        $first_page = max(1, $current_page - 2);
        $last_page = min($total_pages, $first_page + 4);
        $first_page = max(1, $last_page - 4);
        $page_numbers = range($first_page, $last_page);

        $flag_history = DbCorporaFlag::getCorpusFlagHistory($corpus_id, $selected_user, $selected_flag, $search, $page_size, $offset);
        $users = DbCorporaFlag::getCorpusFlagChangeUsers($corpus_id);
        $flags = DbCorporaFlag::getCorpusFlags($corpus_id);

        $this->set('flag_history', $flag_history);
        $this->set('users', $users);
        $this->set('flags', $flags);
        $this->set('search', $search);
        $this->set('page_size', $page_size);
        $this->set('current_page', $current_page);
        $this->set('total_pages', $total_pages);
        $this->set('total_results', $total_results);
        $this->set('page_start', $total_results > 0 ? ($offset + 1) : 0);
        $this->set('page_end', min($offset + $page_size, $total_results));
        $this->set('page_numbers', $page_numbers);
    }

}
