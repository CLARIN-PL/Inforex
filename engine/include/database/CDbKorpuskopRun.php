<?php

class DbKorpuskopRun
{
    const TABLE = 'korpuskop_runs';

    static function insertRun($values)
    {
        global $db;
        $db->insert(self::TABLE, $values);
        return $db->last_id();
    }

    static function getRunsByCorpus($corpus_id, $limit = 50)
    {
        global $db;
        $limit = max(1, intval($limit));
        $sql = "SELECT
                    t.task_id,
                    t.status AS task_status,
                    t.datetime AS task_datetime,
                    t.user_id AS task_user_id,
                    tu.screename AS task_screename,
                    kr.run_id,
                    kr.corpus_id,
                    kr.user_id,
                    kr.input_path,
                    kr.input_kind,
                    kr.output_path,
                    kr.config_json_path,
                    kr.progress_file,
                    kr.status AS run_status,
                    kr.exit_code,
                    kr.message,
                    kr.file_size,
                    kr.created_at,
                    kr.finished_at,
                    ru.screename AS run_screename
                FROM tasks t
                LEFT JOIN (
                    SELECT kr1.*
                    FROM " . self::TABLE . " kr1
                    JOIN (
                        SELECT MAX(run_id) AS run_id
                        FROM " . self::TABLE . "
                        WHERE corpus_id = ?
                        GROUP BY task_id
                    ) latest ON latest.run_id = kr1.run_id
                ) kr ON kr.task_id = t.task_id AND kr.corpus_id = t.corpus_id
                LEFT JOIN users tu ON tu.user_id = t.user_id
                LEFT JOIN users ru ON ru.user_id = kr.user_id
                WHERE t.corpus_id = ? AND t.type = 'korpuskop'
                ORDER BY t.task_id DESC
                LIMIT {$limit}";
        $rows = $db->fetch_rows($sql, array($corpus_id, $corpus_id));
        foreach ($rows as &$row) {
            $row['status'] = !empty($row['run_status']) ? $row['run_status'] : $row['task_status'];
            $row['screename'] = !empty($row['run_screename']) ? $row['run_screename'] : $row['task_screename'];
            $row['created_at'] = !empty($row['created_at']) ? $row['created_at'] : $row['task_datetime'];
            $row['real_run_id'] = isset($row['run_id']) && $row['run_id'] !== null ? intval($row['run_id']) : null;
            if ($row['real_run_id'] === null) {
                $row['run_id'] = 'task-' . $row['task_id'];
            }
            if (empty($row['input_kind'])) {
                $row['input_kind'] = '-';
            }
            if (!isset($row['input_path'])) {
                $row['input_path'] = '';
            }
            if (!isset($row['output_path'])) {
                $row['output_path'] = '';
            }
        }
        unset($row);
        return $rows;
    }

    static function getRun($run_id)
    {
        global $db;
        $sql = "SELECT * FROM " . self::TABLE . " WHERE run_id = ?";
        return $db->fetch($sql, array($run_id));
    }

    static function getRunForCorpus($run_id, $corpus_id)
    {
        global $db;
        $sql = "SELECT * FROM " . self::TABLE . " WHERE run_id = ? AND corpus_id = ?";
        return $db->fetch($sql, array($run_id, $corpus_id));
    }

    static function getRunByTask($task_id, $corpus_id)
    {
        global $db;
        $sql = "SELECT * FROM " . self::TABLE . " WHERE task_id = ? AND corpus_id = ? ORDER BY run_id DESC LIMIT 1";
        return $db->fetch($sql, array($task_id, $corpus_id));
    }

    static function getLatestActiveRunByCorpus($corpus_id)
    {
        global $db;
        $sql = "SELECT kr.*, u.screename
                FROM " . self::TABLE . " kr
                LEFT JOIN users u ON u.user_id = kr.user_id
                WHERE kr.corpus_id = ?
                  AND kr.task_id IS NOT NULL
                  AND kr.status IN ('new', 'process')
                ORDER BY kr.run_id DESC
                LIMIT 1";
        return $db->fetch($sql, array($corpus_id));
    }

    static function getLatestRunByCorpus($corpus_id)
    {
        global $db;
        $sql = "SELECT kr.*, u.screename
                FROM " . self::TABLE . " kr
                LEFT JOIN users u ON u.user_id = kr.user_id
                WHERE kr.corpus_id = ?
                ORDER BY kr.run_id DESC
                LIMIT 1";
        return $db->fetch($sql, array($corpus_id));
    }

    static function updateRunByTask($task_id, $corpus_id, $values)
    {
        global $db;
        $db->update(self::TABLE, $values, array(
            'task_id' => $task_id,
            'corpus_id' => $corpus_id,
        ));
    }

    static function deleteRunForCorpus($run_id, $corpus_id)
    {
        global $db;
        $sql = "DELETE FROM " . self::TABLE . " WHERE run_id = ? AND corpus_id = ?";
        $db->execute($sql, array($run_id, $corpus_id));
    }
}
