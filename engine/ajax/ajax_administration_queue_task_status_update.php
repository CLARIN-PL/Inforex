<?php

require_once dirname(__FILE__) . '/../include/database/CDbTask.php';

class Ajax_administration_queue_task_status_update extends CPageAdministration {

    function execute(){
        global $db;

        $taskId = intval($this->getRequestParameter('task_id', 0));
        $status = trim((string) $this->getRequestParameter('status', ''));

        if ($taskId <= 0) {
            throw new Exception('Invalid task identifier.');
        }

        if (!in_array($status, array('new', 'process', 'done', 'error', 'canceled'))) {
            throw new Exception('Invalid target status.');
        }

        $task = $db->fetch(
            "SELECT task_id, status, corpus_id, type, current_step, max_steps, datetime, datetime_start, message
             FROM tasks
             WHERE task_id = ?",
            array($taskId)
        );

        if (!$task || !isset($task['task_id'])) {
            throw new Exception('Task not found.');
        }

        DbTask::updateTaskStatus($taskId, $status);

        $updated = $db->fetch(
            "SELECT task_id, status, current_step, max_steps, datetime, datetime_start, message, corpus_id, type
             FROM tasks
             WHERE task_id = ?",
            array($taskId)
        );

        return array(
            'task' => $updated,
            'message' => 'Task status has been updated.'
        );
    }
}
