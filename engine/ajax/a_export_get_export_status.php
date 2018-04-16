<?php
class Ajax_export_get_export_status extends CPage{
    function checkPermission(){
        return hasRole('admin') || hasCorpusRole('export');
    }

    function execute(){
        $corpus_id = $_POST['corpus_id'];
        $ongoing_exports = $_POST['current_exports'];
        return DbExport::getExportsProgress($corpus_id, $ongoing_exports);
    }
}