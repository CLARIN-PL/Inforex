<?php
class ajax_metadata_get_reports extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);
        $this->anySystemRole[] = ROLE_SYSTEM_USER_LOGGEDIN;
    }

    function execute(){
        $search = $_POST['search'];
        $corpus_id = $_POST['corpus_id'];
        $page = $_POST['page'];

        $results = DbReport::getReportsByFilter($search, $corpus_id, $page);

        header('Content-Type: application/json');
        echo json_encode($results);
        die();
    }
}