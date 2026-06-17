<?php

require_once dirname(__FILE__) . '/../include/integration/KorpuskopTaskManager.php';

class Ajax_export_get_single_status extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
    }

    function customPermissionRule($user = null, $corpus = null){
        global $db;
        $exportId = intval($this->getRequestParameter('export_id', 0));
        if ($exportId > 0) {
            $action = $db->fetch_one(
                "SELECT post_export_action FROM exports WHERE export_id = ? AND corpus_id = ?",
                array($exportId, $corpus['id'])
            );
            if ($action === 'korpuskop') {
                return hasUserReportGenerationAccess($user, $corpus);
            }
        }
        return null;
    }

    function execute(){
        global $db;

        $export_id = intval($this->getRequestParameter('export_id', 0));
        if ($export_id <= 0) {
            throw new Exception('Nieprawidłowy identyfikator eksportu.');
        }

        $export = $db->fetch(
            "SELECT e.*, COUNT(ee.id) AS errors
             FROM exports e
             LEFT JOIN export_errors ee ON e.export_id = ee.export_id
             WHERE e.export_id = ? AND e.corpus_id = ?
             GROUP BY e.export_id",
            array($export_id, $this->getCorpusId())
        );

        if (!$export || !isset($export['export_id'])) {
            throw new Exception('Nie znaleziono wskazanego eksportu.');
        }

        $linkedTaskId = null;
        if ($export['status'] === 'done' && isset($export['post_export_action']) && $export['post_export_action'] === 'korpuskop') {
            $payload = array();
            if (!empty($export['post_export_payload'])) {
                $decoded = json_decode($export['post_export_payload'], true);
                if (is_array($decoded)) {
                    $payload = $decoded;
                }
            }
            $linkedTaskId = KorpuskopTaskManager::getLinkedTaskIdForExport(
                $this->getCorpusId(),
                $export_id,
                isset($payload['input_kind']) ? $payload['input_kind'] : null,
                isset($payload['focus_words']) ? $payload['focus_words'] : array()
            );
        }

        return array(
            'export' => $export,
            'linked_korpuskop_task_id' => $linkedTaskId,
            'download_url' => ($export['status'] === 'done')
                ? ('index.php?page=corpus_export&corpus=' . $this->getCorpusId() . '&export_id=' . $export_id)
                : null,
        );
    }
}
