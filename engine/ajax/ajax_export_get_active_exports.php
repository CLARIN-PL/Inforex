<?php
class Ajax_export_get_active_exports extends CPage{
    function checkPermission(){
        return hasRole('admin') || hasCorpusRole('export');
    }

	function execute(){
        $corpus_id = $_POST['corpus_id'];
        $active_exports = DbExport::getActiveExports($corpus_id);
        return $active_exports;
    }
}