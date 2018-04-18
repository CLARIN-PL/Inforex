<?php
class Ajax_export_get_export_status extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
    }

    function execute(){
        $corpus_id = $_POST['corpus_id'];
        $ongoing_exports = $_POST['current_exports'];
        return DbExport::getExportsProgress($corpus_id, $ongoing_exports);
    }
}