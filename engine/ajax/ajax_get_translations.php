<?php
class ajax_get_translations extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }

    function execute(){
        $report_id = $_POST['report_id'];
        $translations = DbReport::getReportTranslations($report_id);

        $translations_sorted = array();
        foreach($translations as $translation){
            $translations_sorted[$translation['code']][] = $translation;
        }

        return $translations_sorted;
    }

}