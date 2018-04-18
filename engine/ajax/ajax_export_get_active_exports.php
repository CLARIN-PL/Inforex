<?php
class Ajax_export_get_active_exports extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
    }

	function execute(){
        $corpus_id = $_POST['corpus_id'];
        $active_exports = DbExport::getActiveExports($corpus_id);
        return $active_exports;
    }
}