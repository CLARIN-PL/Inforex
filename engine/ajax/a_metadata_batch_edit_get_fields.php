<?php
class Ajax_metadata_batch_edit_get_fields extends CPage {

    var $isSecure = false;
    function execute() {
        $corpus_id = $_POST['corpus_id'];
        $data['columns'] = DbCorpus::getCorpusAllMetadataColumns($corpus_id);
        $data['filenames'] = DbCorpus::getDocumentFilenames($corpus_id);


        return $data;
    }
}
