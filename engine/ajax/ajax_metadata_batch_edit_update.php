<?php

/**
 * Created by mikolaj on 12.03.18.
 */
class Ajax_metadata_batch_edit_update extends CPageCorpus {

    function execute() {
        $corpus_id = $this->getCorpusId();
        if (!isset($_POST['corpus_id']) || (int)$_POST['corpus_id'] !== (int)$corpus_id) {
            throw new Exception('The metadata request does not match the authorized corpus.');
        }
        // One JSON field avoids PHP max_input_vars truncating a large spreadsheet.
        $changedDocs = isset($_POST['docs_json'])
            ? json_decode($_POST['docs_json'], true)
            : (isset($_POST['docs']) ? $_POST['docs'] : null);
        if (!is_array($changedDocs)) {
            throw new Exception('Invalid metadata changes.');
        }
        // Do not put document metadata in ChromePhp response headers: a large
        // header is rejected by the reverse proxy even after a successful save.
        return DbCorpus::batchUpdateMetadata($corpus_id, $changedDocs);
    }
}
