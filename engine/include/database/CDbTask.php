<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbTask{

    static function getCorpusIdForTaskId($taskId){
        global $db;
        return $db->fetch_one("SELECT corpus_id FROM tasks WHERE task_id = ?", $taskId);
    }

    static function updateTaskStatus($taskId, $status){
        global $db;

        if (!in_array($status, array('new', 'process', 'done', 'error', 'canceled'))) {
            throw new InvalidArgumentException('Invalid task status.');
        }

        $taskId = intval($taskId);
        $current = $db->fetch(
            "SELECT datetime_start, message
             FROM tasks
             WHERE task_id = ?",
            array($taskId)
        );

        if (!$current) {
            throw new InvalidArgumentException('Task not found.');
        }

        $currentDatetimeStart = array_key_exists('datetime_start', $current) ? $current['datetime_start'] : null;
        if ($currentDatetimeStart === '0000-00-00 00:00:00' || $currentDatetimeStart === '') {
            $currentDatetimeStart = null;
        }

        if ($status === 'new') {
            $nextDatetimeStart = null;
        } else if ($status === 'process') {
            $nextDatetimeStart = $currentDatetimeStart ? $currentDatetimeStart : date('Y-m-d H:i:s');
        } else {
            $nextDatetimeStart = $currentDatetimeStart;
        }

        $currentMessage = array_key_exists('message', $current) ? $current['message'] : null;
        if ($status === 'canceled') {
            $nextMessage = trim((string)$currentMessage) !== '' ? $currentMessage : 'Task canceled by administrator.';
        } else if ($status === 'new') {
            $nextMessage = null;
        } else {
            $nextMessage = $currentMessage;
        }

        $db->execute(
            "UPDATE tasks
             SET status = ?,
                 datetime_start = ?,
                 message = ?
             WHERE task_id = ?",
            array($status, $nextDatetimeStart, $nextMessage, $taskId)
        );

        if ($status === 'canceled') {
            $db->execute(
                "UPDATE tasks_reports
                 SET status = 'canceled',
                     message = CASE
                        WHEN COALESCE(message, '') = '' THEN 'Task canceled by administrator.'
                        ELSE message
                     END
                 WHERE task_id = ?
                   AND status IN ('new', 'process')",
                array($taskId)
            );
        } else if ($status === 'new') {
            $db->execute(
                "UPDATE tasks_reports
                 SET status = 'new',
                     message = ''
                 WHERE task_id = ?
                   AND status = 'canceled'",
                array($taskId)
            );
        }
    }

}
