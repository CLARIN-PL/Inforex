<?php

/**
 * Created by mikolaj on 12.03.18.
 */
class Ajax_metadata_batch_edit_update extends CPage {

    var $isSecure = false;
    function execute() {
        $changedDocs = $_POST['docs'];
        $corpus_id = $_POST['corpus_id'];
        ChromePhp::log($changedDocs);
        DbCorpus::batchUpdateMetadata($corpus_id, $changedDocs);

        return true;
    }
}
