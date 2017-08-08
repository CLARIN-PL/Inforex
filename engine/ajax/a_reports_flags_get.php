<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_reports_flags_get extends CPage {

    /**
     * Zwraca tablice JSON z dostępnymi rolami.
     */
    function execute(){
        global $db;

        $status = $_POST['selected_action'];
        $flag = $_POST['selected_flag'];
        $mode = $_POST['mode'];

        if($mode == "count"){
            $sql = "SELECT count(*) as num_of_documents FROM reports_flags  WHERE (corpora_flag_id = ? AND flag_id = ?)";
            $reports = $db->fetch_one($sql, array($flag, $status));

        } else{
            $sql = "SELECT r.* FROM reports_flags rf JOIN reports r ON r.id = rf.report_id WHERE (rf.corpora_flag_id = ? AND rf.flag_id = ?) ";
            $reports = $db->fetch_rows($sql, array($flag, $status));
        }


        return $reports;
    }
}
?>
