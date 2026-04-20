<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveUser_activity extends CCorpusPerspective {

    function __construct(CPage $page)
    {
        parent::__construct($page);
        $this->page->includeJs('js/corpus_user_activity.js');
    }

    function execute()
    {
        $this->page->set("activities", array());
    }

}
?>
