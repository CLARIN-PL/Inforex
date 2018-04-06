<?php
class Ajax_export_get_errors extends CPage{
    function checkPermission(){
        return hasRole('admin') || hasCorpusRole('export');
    }

    function execute(){
        $export_id = $_POST['export_id'];
        return DbExport::getExportErrors($export_id);
    }
}