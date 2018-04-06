<?php
class Ajax_export_get_stats extends CPage{
    function checkPermission(){
        return hasRole('admin') || hasCorpusRole('export');
    }

    function execute(){
        $export_id = $_POST['export_id'];
        $stats = DbExport::getExportStats($export_id);
        ChromePhp::log($stats);
        return $stats;
    }
}