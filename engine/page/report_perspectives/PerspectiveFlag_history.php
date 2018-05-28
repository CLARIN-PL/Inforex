<?php
class PerspectiveFlag_history extends CPerspective
{
    function execute(){
        global $corpus;

        $row = $this->page->get("row");
        $report_id = $row['id'];

        $selected_user = $_COOKIE['flag_history_user'];
        $selected_flag = $_COOKIE['flag_history_flag'];

        $flag_history = DbReportFlag::getReportFlagHistory($report_id, $selected_user, $selected_flag);
        $users = DbReportFlag::getReportFlagChangeUsers($report_id);
        $flags = DbCorporaFlag::getCorpusFlags($corpus['id']);
        ChromePhp::log($flags);

        $this->page->set('flag_history', $flag_history);
        $this->page->set('users', $users);
        $this->page->set('flags', $flags);

    }
}