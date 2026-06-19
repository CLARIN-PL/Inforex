<?php

require_once dirname(__FILE__) . '/../include/database/CDbKorpuskopRun.php';

class Ajax_korpuskop_runs_status extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);
        $this->anySystemRole = array();
        $this->anyCorpusRole = array();
    }

    function customPermissionRule($user = null, $corpus = null){
        return hasUserReportGenerationAccess($user, $corpus);
    }

    function execute(){
        $runs = DbKorpuskopRun::getRunsByCorpus($this->getCorpusId(), 25);
        $hasActive = false;

        foreach ($runs as &$run) {
            $run['download_url'] = null;
            $run['view_url'] = !empty($run['task_id'])
                ? ('index.php?page=corpus_korpuskop&corpus=' . $this->getCorpusId() . '&task_id=' . intval($run['task_id']) . '&show_task=1')
                : null;

            if ($run['status'] === 'done' && !empty($run['real_run_id'])) {
                $run['download_url'] = 'index.php?page=korpuskop_download&corpus=' . $this->getCorpusId() . '&run_id=' . intval($run['real_run_id']);
            }
            if ($run['status'] === 'new' || $run['status'] === 'process') {
                $hasActive = true;
            }
        }
        unset($run);

        return array(
            'runs' => $runs,
            'has_active' => $hasActive,
        );
    }
}
