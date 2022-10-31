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

    function __construct($name=null,$description=null){
        parent::__construct($name,$description);
        $this->anyCorpusRole = array(CORPUS_ROLE_MANAGER, CORPUS_ROLE_OWNER);
    }

    /**
     * Return current corpus data. If the corpus data is not set, than redirect to the home page.
     * @return mixed
     */
    function getCorpus(){
        global $corpus;
        if (!$corpus){
            $this->redirect("index.php?page=home");
        } else {
            return $corpus;
        }
    }

    function getCorpusId(){
        $corpus = $this->getCorpus();
        return $corpus[DB_COLUMN_CORPORA__CORPUS_ID];
    }

    function debugLog($name,$value) {

        global $logId;
        $valStr = is_array($value) ? json_encode($value) : $value;
        DebugLogger::logVariableASJSON($name,$valStr);

    } // debugLog()

}
