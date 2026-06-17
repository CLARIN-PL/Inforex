<?php

class DbActivity
{
    const ACTIVE_NOW_MINUTES = 15;
    const ACTIVE_HOUR_HOURS = 1;
    const ACTIVE_DAY_HOURS = 24;
    const ACTIVE_WEEK_DAYS = 7;

    static function getAdminDashboardSummary()
    {
        global $db;

        $sql = "SELECT
                    COUNT(DISTINCT CASE WHEN a.datetime >= NOW() - INTERVAL " . self::ACTIVE_NOW_MINUTES . " MINUTE THEN a.user_id END) AS active_now,
                    COUNT(DISTINCT CASE WHEN a.datetime >= NOW() - INTERVAL " . self::ACTIVE_HOUR_HOURS . " HOUR THEN a.user_id END) AS active_hour,
                    COUNT(DISTINCT CASE WHEN a.datetime >= NOW() - INTERVAL " . self::ACTIVE_DAY_HOURS . " HOUR THEN a.user_id END) AS active_day,
                    COUNT(DISTINCT CASE WHEN a.datetime >= NOW() - INTERVAL " . self::ACTIVE_WEEK_DAYS . " DAY THEN a.user_id END) AS active_week,
                    COUNT(CASE WHEN a.datetime >= NOW() - INTERVAL " . self::ACTIVE_DAY_HOURS . " HOUR THEN 1 END) AS events_day
                FROM activities a FORCE INDEX (activities_datetime_user_idx)
                WHERE a.user_id IS NOT NULL
                  AND a.datetime >= NOW() - INTERVAL " . self::ACTIVE_WEEK_DAYS . " DAY";

        $row = $db->fetch($sql);
        return is_array($row) ? $row : array();
    }

    static function getAdminDashboardTimeline($hours = 24)
    {
        global $db;

        $hours = max(1, intval($hours));
        $sql = "SELECT
                    DATE_FORMAT(a.datetime, '%Y-%m-%d %H:00:00') AS bucket,
                    COUNT(*) AS events_count,
                    COUNT(DISTINCT a.user_id) AS users_count
                FROM activities a FORCE INDEX (activities_datetime_user_idx)
                WHERE a.user_id IS NOT NULL
                  AND a.datetime >= NOW() - INTERVAL ? HOUR
                GROUP BY DATE_FORMAT(a.datetime, '%Y-%m-%d %H:00:00')
                ORDER BY bucket ASC";

        $rows = $db->fetch_rows($sql, array($hours));
        $indexed = array();
        foreach ($rows as $row) {
            $indexed[$row['bucket']] = $row;
        }

        $timeline = array();
        $maxEvents = 0;
        $maxUsers = 0;
        $start = new DateTimeImmutable('-' . ($hours - 1) . ' hour');
        $start = $start->setTime(intval($start->format('H')), 0, 0);
        for ($i = 0; $i < $hours; $i++) {
            $bucket = $start->modify('+' . $i . ' hour');
            $bucketKey = $bucket->format('Y-m-d H:00:00');
            $eventsCount = isset($indexed[$bucketKey]) ? intval($indexed[$bucketKey]['events_count']) : 0;
            $usersCount = isset($indexed[$bucketKey]) ? intval($indexed[$bucketKey]['users_count']) : 0;
            $maxEvents = max($maxEvents, $eventsCount);
            $maxUsers = max($maxUsers, $usersCount);
            $timeline[] = array(
                'bucket' => $bucketKey,
                'label' => $bucket->format('H:i'),
                'events_count' => $eventsCount,
                'users_count' => $usersCount,
            );
        }

        foreach ($timeline as &$row) {
            $row['events_percent'] = $maxEvents > 0 ? round(($row['events_count'] / $maxEvents) * 100, 2) : 0;
            $row['users_percent'] = $maxUsers > 0 ? round(($row['users_count'] / $maxUsers) * 100, 2) : 0;
        }
        unset($row);

        return array(
            'points' => $timeline,
            'max_events' => $maxEvents,
            'max_users' => $maxUsers,
        );
    }

    static function getAdminDashboardActiveUsers($limit = 25)
    {
        global $db;

        $limit = max(1, intval($limit));
        $sql = "SELECT
                    u.user_id,
                    u.login,
                    u.screename,
                    recent.last_activity,
                    recent.events_24h,
                    recent.events_7d,
                    recent.corpora_7d
                FROM (
                    SELECT
                        a.user_id,
                        MAX(a.datetime) AS last_activity,
                        SUM(CASE WHEN a.datetime >= NOW() - INTERVAL " . self::ACTIVE_DAY_HOURS . " HOUR THEN 1 ELSE 0 END) AS events_24h,
                        COUNT(*) AS events_7d,
                        COUNT(DISTINCT a.corpus_id) AS corpora_7d
                    FROM activities a FORCE INDEX (activities_datetime_user_idx)
                    WHERE a.user_id IS NOT NULL
                      AND a.datetime >= NOW() - INTERVAL " . self::ACTIVE_NOW_MINUTES . " MINUTE
                    GROUP BY a.user_id
                    ORDER BY last_activity DESC
                    LIMIT {$limit}
                ) recent
                JOIN users u ON u.user_id = recent.user_id
                ORDER BY recent.last_activity DESC";

        $rows = $db->fetch_rows($sql);
        foreach ($rows as &$row) {
            $row['is_active_now'] = 1;
        }
        unset($row);

        return $rows;
    }

    static function getAdminDashboardQueues()
    {
        global $db;

        $taskLabels = array(
            'liner2' => 'Liner2 tasks',
            'lpmn-postagger' => 'LLM POS tagging',
            'upload-zip-txt' => 'ZIP text imports',
            'korpuskop' => 'Corpus reports',
            'export' => 'Legacy export tasks',
        );

        $rows = array();

        $taskRows = $db->fetch_rows(
            "SELECT
                type AS queue_key,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS pending_count,
                SUM(CASE WHEN status = 'process' THEN 1 ELSE 0 END) AS processing_count,
                SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) AS error_count,
                SUM(CASE WHEN status = 'done' AND COALESCE(datetime_start, datetime) >= NOW() - INTERVAL 24 HOUR THEN 1 ELSE 0 END) AS completed_24h,
                MIN(CASE WHEN status = 'new' THEN datetime END) AS oldest_pending,
                MAX(COALESCE(datetime_start, datetime)) AS last_activity
            FROM tasks
            GROUP BY type
            ORDER BY type ASC"
        );

        foreach ($taskRows as $row) {
            $queueKey = $row['queue_key'];
            $rows[] = self::buildQueueRow(
                'task_' . $queueKey,
                isset($taskLabels[$queueKey]) ? $taskLabels[$queueKey] : ('Task queue: ' . $queueKey),
                'Tasks',
                $row
            );
        }

        $exportRows = $db->fetch_rows(
            "SELECT
                CASE
                    WHEN post_export_action = 'korpuskop' THEN 'report_exports'
                    ELSE 'document_exports'
                END AS queue_key,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS pending_count,
                SUM(CASE WHEN status = 'process' THEN 1 ELSE 0 END) AS processing_count,
                SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) AS error_count,
                SUM(CASE WHEN status = 'done' AND COALESCE(datetime_finish, datetime_start, datetime_submit) >= NOW() - INTERVAL 24 HOUR THEN 1 ELSE 0 END) AS completed_24h,
                MIN(CASE WHEN status = 'new' THEN datetime_submit END) AS oldest_pending,
                MAX(COALESCE(datetime_finish, datetime_start, datetime_submit)) AS last_activity
            FROM exports
            GROUP BY queue_key
            ORDER BY queue_key ASC"
        );

        foreach ($exportRows as $row) {
            $rows[] = self::buildQueueRow(
                'export_' . $row['queue_key'],
                $row['queue_key'] === 'report_exports' ? 'Report export preparation' : 'Document exports',
                'Exports',
                $row
            );
        }

        usort($rows, function ($left, $right) {
            $leftLoad = $left['pending_count'] + $left['processing_count'];
            $rightLoad = $right['pending_count'] + $right['processing_count'];
            if ($leftLoad !== $rightLoad) {
                return $rightLoad - $leftLoad;
            }
            return strcmp($left['label'], $right['label']);
        });

        $summary = array(
            'active_queues' => 0,
            'pending_items' => 0,
            'processing_items' => 0,
            'error_items' => 0,
            'completed_24h' => 0,
        );

        foreach ($rows as $row) {
            if ($row['pending_count'] > 0 || $row['processing_count'] > 0) {
                $summary['active_queues']++;
            }
            $summary['pending_items'] += $row['pending_count'];
            $summary['processing_items'] += $row['processing_count'];
            $summary['error_items'] += $row['error_count'];
            $summary['completed_24h'] += $row['completed_24h'];
        }

        return array(
            'summary' => $summary,
            'rows' => $rows,
        );
    }

    static function getAdminDashboardQueueDetail($queueId, $status, $limit = 100)
    {
        global $db;

        $statusMap = array(
            'waiting' => 'new',
            'processing' => 'process',
            'error' => 'error',
            'completed' => 'done',
        );

        if (!isset($statusMap[$status]) || !$queueId) {
            return null;
        }

        $dbStatus = $statusMap[$status];
        $limit = max(1, intval($limit));

        if (strpos($queueId, 'task_') === 0) {
            $taskType = substr($queueId, 5);
            $rows = $db->fetch_rows(
                "SELECT
                    t.task_id AS item_id,
                    t.status,
                    t.type,
                    t.datetime,
                    t.datetime_start,
                    t.description,
                    t.message,
                    t.current_step,
                    t.max_steps,
                    c.id AS corpus_id,
                    c.name AS corpus_name,
                    u.login,
                    u.screename
                 FROM tasks t
                 LEFT JOIN corpora c ON c.id = t.corpus_id
                 LEFT JOIN users u ON u.user_id = t.user_id
                 WHERE t.type = ?
                   AND t.status = ?
                 ORDER BY COALESCE(t.datetime_start, t.datetime) DESC
                 LIMIT {$limit}",
                array($taskType, $dbStatus)
            );

            return array(
                'queue_id' => $queueId,
                'queue_label' => self::getQueueLabelById($queueId),
                'status_key' => $status,
                'status_label' => self::getQueueStatusLabel($status),
                'group_label' => 'Tasks',
                'items' => $rows,
                'item_kind' => 'task',
            );
        }

        if (strpos($queueId, 'export_') === 0) {
            $exportKind = substr($queueId, 7);
            $condition = $exportKind === 'report_exports'
                ? "COALESCE(e.post_export_action, '') = 'korpuskop'"
                : "COALESCE(e.post_export_action, '') <> 'korpuskop'";

            $rows = $db->fetch_rows(
                "SELECT
                    e.export_id AS item_id,
                    e.status,
                    e.datetime_submit,
                    e.datetime_start,
                    e.datetime_finish,
                    e.description,
                    e.message,
                    e.progress,
                    e.export_format,
                    e.post_export_action,
                    c.id AS corpus_id,
                    c.name AS corpus_name,
                    u.login,
                    u.screename
                 FROM exports e
                 LEFT JOIN corpora c ON c.id = e.corpus_id
                 LEFT JOIN users u ON u.user_id = e.user_id
                 WHERE {$condition}
                   AND e.status = ?
                 ORDER BY COALESCE(e.datetime_finish, e.datetime_start, e.datetime_submit) DESC
                 LIMIT {$limit}",
                array($dbStatus)
            );

            return array(
                'queue_id' => $queueId,
                'queue_label' => self::getQueueLabelById($queueId),
                'status_key' => $status,
                'status_label' => self::getQueueStatusLabel($status),
                'group_label' => 'Exports',
                'items' => $rows,
                'item_kind' => 'export',
            );
        }

        return null;
    }

    private static function buildQueueRow($id, $label, $groupLabel, $row)
    {
        return array(
            'id' => $id,
            'label' => $label,
            'group_label' => $groupLabel,
            'pending_count' => intval($row['pending_count']),
            'processing_count' => intval($row['processing_count']),
            'error_count' => intval($row['error_count']),
            'completed_24h' => intval($row['completed_24h']),
            'oldest_pending' => $row['oldest_pending'],
            'oldest_pending_age' => self::humanizeAge($row['oldest_pending']),
            'last_activity' => $row['last_activity'],
        );
    }

    private static function getQueueLabelById($queueId)
    {
        $taskLabels = array(
            'liner2' => 'Liner2 tasks',
            'lpmn-postagger' => 'LLM POS tagging',
            'upload-zip-txt' => 'ZIP text imports',
            'korpuskop' => 'Corpus reports',
            'export' => 'Legacy export tasks',
        );

        if (strpos($queueId, 'task_') === 0) {
            $taskType = substr($queueId, 5);
            return isset($taskLabels[$taskType]) ? $taskLabels[$taskType] : ('Task queue: ' . $taskType);
        }

        if ($queueId === 'export_report_exports') {
            return 'Report export preparation';
        }

        if ($queueId === 'export_document_exports') {
            return 'Document exports';
        }

        return $queueId;
    }

    private static function getQueueStatusLabel($status)
    {
        $labels = array(
            'waiting' => 'Waiting',
            'processing' => 'Processing',
            'error' => 'Errors',
            'completed' => 'Completed',
        );

        return isset($labels[$status]) ? $labels[$status] : $status;
    }

    private static function humanizeAge($datetime)
    {
        if (!$datetime) {
            return '—';
        }

        $timestamp = strtotime($datetime);
        if ($timestamp === false) {
            return '—';
        }

        $seconds = max(0, time() - $timestamp);
        if ($seconds < 60) {
            return $seconds . 's';
        }

        $minutes = floor($seconds / 60);
        if ($minutes < 60) {
            return $minutes . 'm';
        }

        $hours = floor($minutes / 60);
        if ($hours < 24) {
            return $hours . 'h';
        }

        $days = floor($hours / 24);
        return $days . 'd';
    }
}
