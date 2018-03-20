<?php

/**
 * Created by PhpStorm.
 * User: mszewczyk
 * Date: 2/15/18
 * Time: 10:23 AM
 */
class DbExport
{
    static function getExportErrors($export_id){
        global $db;

        $sql = "SELECT * FROM export_errors WHERE export_id = ?";
        $params = array($export_id);

        $errors = $db->fetch_rows($sql, $params);

        foreach($errors as $key => $error){
            $errors[$key]['error_details'] = unserialize($error['error_details']);
        }

        ChromePhp::log($errors);

        return $errors;
    }

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

    static function saveErrors($export_id, $errors){
        global $db;
        
        foreach($errors as $error){
            $values = array(
                'export_id' => $export_id,
                'message' => $error['message'],
                'error_details' => serialize($error['details']),
                'count' => $error['count']
            );
            $db->insert('export_errors', $values);
        }
    }

    static function getActiveExports($corpus_id){
        global $db;

        $sql_new = "SELECT export_id FROM exports 
                WHERE (corpus_id = ? AND status = 'new') 
                ORDER BY datetime_submit ASC";
        $params = array($corpus_id);
        $scheduled_exports = $db->fetch_rows($sql_new, $params);

        $exports = array();
        foreach($scheduled_exports as $scheduled_export){
            $exports['scheduled_exports'][$scheduled_export['export_id']] = 1;
        }

        $sql_process = "SELECT export_id FROM exports 
                        WHERE (corpus_id = ? AND status = 'process') 
                        ORDER BY datetime_submit ASC";
        $current_exports = $db->fetch_rows($sql_process, $params);
        foreach($current_exports as $current_export){
            $exports['current_exports'][$current_export['export_id']] = 1;
        }
        return $exports;
    }

    /**
     * Gets the exports progress. Uses the getActiveExports(corpus_id) function to get the exports in progress.
     * Also, gets the current exports from the front-end in $ongoing_exports array (needed to get the 'done' status after processing)
     * @param $corpus_id
     * @param $ongoing_exports
     * @return array
     */

    static function getExportsProgress($corpus_id, $ongoing_exports){
        global $db;
        $all_exports = self::getActiveExports($corpus_id);
        $current_exports = $all_exports['current_exports'];
        if(empty($current_exports) && empty($ongoing_exports)){
            return array();
        }

        $export_id_str = implode(", ", array_fill(0, count($current_exports), "?"));
        $params = array();
        foreach($current_exports as $id=>$export){
            $params[] = $id;
        }

        $ongoing_exports_str = implode(", ", array_fill(0, count($ongoing_exports), "?"));
        foreach($ongoing_exports as $id=>$export){
            $params[] = $id;
        }

        $sql = "SELECT e.export_id, e.progress, e.status, e.statistics, COUNT(ee.export_id) as 'error_count' FROM exports e 
                LEFT JOIN export_errors ee ON e.export_id = ee.export_id
                WHERE e.export_id IN (".$export_id_str.") OR e.export_id IN (".$ongoing_exports_str.")
                GROUP BY e.export_id";
        $export_progress = $db->fetch_rows($sql, $params);
        return $export_progress;
    }
}