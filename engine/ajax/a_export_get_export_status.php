<?php
class Ajax_export_get_export_status extends CPage{
    function checkPermission(){
        return hasRole('admin') || hasCorpusRole('export');
    }

    function execute(){
        $current_exports = $_POST['current_exports'];
        return DbExport::getExportsProgress($current_exports);
    }
}