<?php

class Ajax_korpuskop_task_status extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);
        $this->anySystemRole = array();
        $this->anyCorpusRole = array();
    }

    function customPermissionRule($user = null, $corpus = null){
        return hasUserReportGenerationAccess($user, $corpus);
    }

    function execute(){
        global $db;

        $task_id = intval($this->getRequestParameter('task_id', 0));
        $task = $db->fetch(
            "SELECT task_id, type, status, current_step, max_steps, description, message, datetime
             FROM tasks
             WHERE task_id = ? AND corpus_id = ? AND type = 'korpuskop'",
            array($task_id, $this->getCorpusId())
        );

        if (!$task || !isset($task['task_id'])) {
            throw new Exception('Nie znaleziono zadania Korpuskop.');
        }

        $queue = intval($db->fetch_one(
            "SELECT COUNT(*) FROM tasks WHERE corpus_id = ? AND type = 'korpuskop' AND status IN ('new','process') AND task_id < ?",
            array($this->getCorpusId(), $task_id)
        ));

        $percent = sprintf('%3.0f', intval($task['max_steps']) === 0 ? 0 : intval($task['current_step']) * 100.0 / intval($task['max_steps']));
        $messagePayload = null;
        if (!empty($task['message'])) {
            $decoded = json_decode($task['message'], true);
            if (is_array($decoded)) {
                $messagePayload = $decoded;
            }
        }

        $run = DbKorpuskopRun::getRunByTask($task_id, $this->getCorpusId());
        $downloadUrl = null;
        if ($run && !empty($run['run_id']) && $run['status'] === 'done') {
            $downloadUrl = 'index.php?page=korpuskop_download&corpus=' . $this->getCorpusId() . '&run_id=' . intval($run['run_id']);
        }

        return array(
            'task' => $task,
            'queue' => $queue,
            'percent' => $percent,
            'message_payload' => $messagePayload,
            'run' => $run,
            'export_id' => is_array($messagePayload) && isset($messagePayload['export_id']) ? intval($messagePayload['export_id']) : null,
            'download_url' => $downloadUrl,
        );
    }
}
