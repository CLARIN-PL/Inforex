<?php
class Ajax_export_get_stats extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
    }

    function execute(){
        $export_id = $_POST['export_id'];
        $stats = DbExport::getExportStats($export_id);
        ChromePhp::log($stats);
        return $stats;
    }
}