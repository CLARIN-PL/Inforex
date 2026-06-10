<?php

class Ajax_export_repeat extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
    }

    function execute(){
        global $corpus, $db;

        $export_id = isset($_POST['export_id']) ? intval($_POST['export_id']) : 0;
        if ($export_id <= 0) {
            throw new Exception("Invalid export id");
        }

        $export = DbExport::getExport($export_id);
        if (!$export) {
            throw new Exception("Export not found");
        }
        if (intval($export['corpus_id']) !== intval($corpus['id'])) {
            throw new Exception("Export does not belong to current corpus");
        }

        $attributes = array();
        $attributes['corpus_id'] = intval($export['corpus_id']);
        $attributes['datetime_submit'] = date("Y-m-d H:i:s");
        $attributes['description'] = strval($export['description']);
        $attributes['extractors'] = strval($export['extractors']);
        $attributes['selectors'] = strval($export['selectors']);
        $attributes['indices'] = strval($export['indices']);
        $attributes['tagging'] = strval($export['tagging']);
        $attributes['export_format'] = isset($export['export_format']) ? strval($export['export_format']) : 'legacy';
        $attributes['status'] = "new";
        $attributes['message'] = null;
        $attributes['statistics'] = null;
        $attributes['progress'] = 0;
        $attributes['datetime_start'] = null;
        $attributes['datetime_finish'] = null;

        $db->insert("exports", $attributes);

        return array('status' => 'ok');
    }
}
