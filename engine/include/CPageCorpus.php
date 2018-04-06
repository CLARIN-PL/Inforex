<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Class CPageCorpus represents any page which presents corpus data. By default can be access by corpus owner and manager.
 */
class CPageCorpus extends CPage {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole = array(CORPUS_ROLE_MANAGER, CORPUS_ROLE_OWNER);
    }

}