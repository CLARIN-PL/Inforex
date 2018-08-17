<?php
class ajax_metadata_get_languages extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);
        $this->anySystemRole[] = ROLE_SYSTEM_USER_LOGGEDIN;
    }

    function execute(){
        $search = $_POST['search'];
        $corpus_id = $_POST['corpus_id'];
        $page = $_POST['page'];

        ChromePhp::log("here");
        $results = DbReport::getLanguagesByFilter($search, $page);

        header('Content-Type: application/json');
        echo json_encode($results);
        die();
    }
}