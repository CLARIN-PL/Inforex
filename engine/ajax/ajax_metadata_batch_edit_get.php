<?php

/**
 * Created by mikolaj on 12.03.18.
 */
class Ajax_metadata_batch_edit_get extends CPageCorpus {

    function execute() {
        $corpus_id = $_POST['corpus_id'];
        $documents = DbCorpus::getDocumentsWithMetadata($corpus_id);

        return $documents;
    }
}
