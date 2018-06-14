<?php
class Ajax_export_get_errors extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
    }

    function execute(){
        $export_id = $_POST['export_id'];
        return DbExport::getExportErrors($export_id);
    }
}