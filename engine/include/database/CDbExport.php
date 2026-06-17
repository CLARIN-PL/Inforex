<?php

/**
 * Created by PhpStorm.
 * User: mszewczyk
 * Date: 2/15/18
 * Time: 10:23 AM
 */
class DbExport
{
    static function normalizeAttributes($attributes){
        $normalized = $attributes;
        foreach (array('description', 'extractors', 'selectors', 'indices', 'tagging', 'export_format', 'post_export_action', 'post_export_payload') as $field) {
            $normalized[$field] = array_key_exists($field, $normalized) ? strval($normalized[$field]) : '';
        }
        $normalized['corpus_id'] = intval($normalized['corpus_id']);
        $normalized['user_id'] = array_key_exists('user_id', $normalized) ? intval($normalized['user_id']) : 0;
        return $normalized;
    }

    static function buildSubmissionLockName($attributes){
        $normalized = self::normalizeAttributes($attributes);
        $fingerprint = md5(json_encode(array(
            $normalized['corpus_id'],
            $normalized['user_id'],
            $normalized['description'],
            $normalized['extractors'],
            $normalized['selectors'],
            $normalized['indices'],
            $normalized['tagging'],
            $normalized['export_format'],
            $normalized['post_export_action'],
            $normalized['post_export_payload'],
        )));
        return 'exports_submit_' . $fingerprint;
    }

    static function acquireSubmissionLock($attributes, $timeoutSeconds = 5){
        global $db;

        $lockName = self::buildSubmissionLockName($attributes);
        $acquired = intval($db->fetch_one("SELECT GET_LOCK(?, ?)", array($lockName, intval($timeoutSeconds))));
        if ($acquired !== 1) {
            throw new UserDataException('Nie udało się zarezerwować zlecenia eksportu. Spróbuj ponownie za chwilę.');
        }

        return $lockName;
    }

    static function releaseSubmissionLock($lockName){
        global $db;

        if ($lockName) {
            $db->fetch_one("SELECT RELEASE_LOCK(?)", array($lockName));
        }
    }

    static function findActiveDuplicateExport($attributes){
        global $db;

        $normalized = self::normalizeAttributes($attributes);
        $sql = "SELECT export_id, status
                FROM exports
                WHERE corpus_id = ?
                  AND user_id = ?
                  AND status IN ('new', 'process')
                  AND COALESCE(description, '') = ?
                  AND COALESCE(extractors, '') = ?
                  AND COALESCE(selectors, '') = ?
                  AND COALESCE(indices, '') = ?
                  AND COALESCE(tagging, '') = ?
                  AND COALESCE(export_format, 'legacy') = ?
                  AND COALESCE(post_export_action, '') = ?
                  AND COALESCE(post_export_payload, '') = ?
                ORDER BY export_id DESC
                LIMIT 1";

        return $db->fetch($sql, array(
            $normalized['corpus_id'],
            $normalized['user_id'],
            $normalized['description'],
            $normalized['extractors'],
            $normalized['selectors'],
            $normalized['indices'],
            $normalized['tagging'],
            $normalized['export_format'],
            $normalized['post_export_action'],
            $normalized['post_export_payload'],
        ));
    }

    static function getLatestActivePostExportAction($corpus_id, $user_id, $action){
        global $db;

        return $db->fetch(
            "SELECT *
             FROM exports
             WHERE corpus_id = ?
               AND user_id = ?
               AND COALESCE(post_export_action, '') = ?
               AND status IN ('new', 'process')
             ORDER BY export_id DESC
             LIMIT 1",
            array(intval($corpus_id), intval($user_id), strval($action))
        );
    }

    static function getLatestPendingPostExportAction($corpus_id, $user_id, $action){
        global $db;

        return $db->fetch(
            "SELECT *
             FROM exports
             WHERE corpus_id = ?
               AND user_id = ?
               AND COALESCE(post_export_action, '') = ?
               AND status IN ('new', 'process', 'done')
             ORDER BY export_id DESC
             LIMIT 1",
            array(intval($corpus_id), intval($user_id), strval($action))
        );
    }

    static function createOrReuseExport($attributes){
        global $db;

        $lockName = self::acquireSubmissionLock($attributes);
        try{
            $duplicate = self::findActiveDuplicateExport($attributes);
            if ($duplicate && isset($duplicate['export_id'])) {
                self::releaseSubmissionLock($lockName);
                return array(
                    'export_id' => intval($duplicate['export_id']),
                    'duplicate' => true,
                    'status' => $duplicate['status'],
                );
            }

            $db->insert("exports", $attributes);
            $newExportId = intval($db->last_id());
            self::releaseSubmissionLock($lockName);
            return array(
                'export_id' => $newExportId,
                'duplicate' => false,
                'status' => 'new',
            );
        }
        catch (Exception $ex){
            self::releaseSubmissionLock($lockName);
            throw $ex;
        }
    }

    static function getExport($export_id){
        global $db;

        $sql = "SELECT * FROM exports WHERE export_id = ?";
        return $db->fetch($sql, array($export_id));
    }

    static function getExportErrors($export_id){
        global $db;

        $sql = "SELECT * FROM export_errors WHERE export_id = ?";
        $params = array($export_id);

        $errors = $db->fetch_rows($sql, $params);

        foreach($errors as $key => $error){
            $errors[$key]['error_details'] = unserialize($error['error_details']);
        }

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

    static function updateExportStatus($export_id, $status){
        global $db;

        if (!in_array($status, array('new', 'process', 'done', 'error'))) {
            throw new InvalidArgumentException('Invalid export status.');
        }

        $sql = "UPDATE exports
                SET status = ?,
                    datetime_start = CASE
                        WHEN ? = 'new' THEN NULL
                        WHEN ? = 'process' THEN COALESCE(datetime_start, NOW())
                        ELSE datetime_start
                    END,
                    datetime_finish = CASE
                        WHEN ? IN ('new', 'process') THEN NULL
                        WHEN ? IN ('done', 'error') THEN COALESCE(datetime_finish, NOW())
                        ELSE datetime_finish
                    END,
                    progress = CASE
                        WHEN ? = 'new' THEN 0
                        ELSE progress
                    END
                WHERE export_id = ?";

        $db->execute($sql, array(
            $status,
            $status,
            $status,
            $status,
            $status,
            $status,
            intval($export_id),
        ));
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

        $params = array();
        if(!empty($current_exports)){
            foreach($current_exports as $id=>$export){
                $params[] = $id;
            }
        }

        if(!empty($ongoing_exports)){
            foreach($ongoing_exports as $id=>$export){
                $params[] = $id;
            }
        }

        array_unique($params);

        $exports_str = implode(", ", array_fill(0, count($params), "?"));

        $sql = "SELECT e.export_id, e.progress, e.status, e.datetime_finish, e.statistics, COUNT(ee.export_id) as 'error_count' FROM exports e 
                LEFT JOIN export_errors ee ON e.export_id = ee.export_id
                WHERE e.export_id IN (".$exports_str.")
                 GROUP BY e.export_id";
        $export_progress = $db->fetch_rows($sql, $params);
        return $export_progress;
    }
}
