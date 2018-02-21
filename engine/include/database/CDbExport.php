<?php

/**
 * Created by PhpStorm.
 * User: mszewczyk
 * Date: 2/15/18
 * Time: 10:23 AM
 */
class DbExport
{
    static function getExportStats($export_id)
    {
        global $db;

        $sql = "SELECT statistics FROM exports 
                    WHERE export_id = ?";
        $params = array($export_id);
        $stats = unserialize($db->fetch_one($sql, $params));


        return $stats;
    }

    static function updateExportProgress($export_id, $percent_done){
        global $db;

        $sql = "UPDATE exports SET progress = ? WHERE export_id = ?";
        $params = array($percent_done, $export_id);

        $db->execute($sql, $params);
    }

    static function saveStatistics($export_id, $stats){
        global $db;

        $stats = serialize($stats);

        $sql = "UPDATE exports SET statistics = ? WHERE export_id = ?";
        $params = array($stats, $export_id);

        $db->execute($sql, $params);
    }

    static function getActiveExports($corpus_id){
        global $db;

        $sql_new = "SELECT export_id FROM exports 
                WHERE (corpus_id = ? AND status = 'new') 
                ORDER BY datetime_submit ASC";
        $params = array($corpus_id);

        $exports['scheduled_exports'] = $db->fetch_rows($sql_new, $params);

        $sql_process = "SELECT export_id FROM exports 
                        WHERE (corpus_id = ? AND status = 'process') 
                        ORDER BY datetime_submit ASC";
        $exports['current_exports']= $db->fetch_rows($sql_process, $params);

        return $exports;
    }

    static function getExportsProgress($exports){
        global $db;

        $current_exports = $exports['current_exports'];
        if(empty($current_exports)){
            return array();
        }

        $export_id_str = implode(", ", array_fill(0, count($current_exports), "?"));
        $params = array();
        foreach($current_exports as $export){
            $params[] = intval($export['export_id']);
        }

        $sql = "SELECT export_id, progress, status FROM exports 
                WHERE export_id IN (".$export_id_str.")";
        $export_progress = $db->fetch_rows($sql, $params);
        return $export_progress;
    }
}