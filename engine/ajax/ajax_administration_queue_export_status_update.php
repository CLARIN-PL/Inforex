<?php

require_once dirname(__FILE__) . '/../include/database/CDbExport.php';

class Ajax_administration_queue_export_status_update extends CPageAdministration {

    function execute(){
        global $db;

        $exportId = intval($this->getRequestParameter('export_id', 0));
        $status = trim((string) $this->getRequestParameter('status', ''));

        if ($exportId <= 0) {
            throw new Exception('Invalid export identifier.');
        }

        if (!in_array($status, array('new', 'process', 'done', 'error'))) {
            throw new Exception('Invalid target status.');
        }

        $export = $db->fetch(
            "SELECT export_id, status, corpus_id, post_export_action, export_format
             FROM exports
             WHERE export_id = ?",
            array($exportId)
        );

        if (!$export || !isset($export['export_id'])) {
            throw new Exception('Export not found.');
        }

        DbExport::updateExportStatus($exportId, $status);

        $updated = $db->fetch(
            "SELECT export_id, status, progress, datetime_submit, datetime_start, datetime_finish, corpus_id
             FROM exports
             WHERE export_id = ?",
            array($exportId)
        );

        return array(
            'export' => $updated,
            'message' => 'Export status has been updated.'
        );
    }
}
