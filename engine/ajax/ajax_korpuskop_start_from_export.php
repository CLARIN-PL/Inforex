<?php

require_once dirname(__FILE__) . '/../include/integration/KorpuskopTaskManager.php';

class Ajax_korpuskop_start_from_export extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anySystemRole = array();
        $this->anyCorpusRole = array();
    }

    function customPermissionRule($user = null, $corpus = null){
        return hasUserReportGenerationAccess($user, $corpus);
    }

    function execute(){
        global $user;

        $export_id = intval($this->getRequestParameter('export_id', 0));
        $input_kind = trim((string) $this->getRequestParameter('input_kind', KorpuskopRunner::INPUT_KIND_AUTO));
        if ($export_id <= 0) {
            throw new Exception('Nieprawidłowy identyfikator eksportu.');
        }
        if (!in_array($input_kind, array(KorpuskopRunner::INPUT_KIND_DOCUMENT, KorpuskopRunner::INPUT_KIND_DIALOG), true)) {
            throw new Exception('Nieobsługiwany typ korpusu dla Korpuskop.');
        }

        $export = DbExport::getExport($export_id);
        if (!$export || intval($export['corpus_id']) !== intval($this->getCorpusId())) {
            throw new Exception('Nie znaleziono wskazanego eksportu.');
        }
        if ($export['status'] !== 'done') {
            throw new Exception('Eksport nie został jeszcze zakończony.');
        }

        $taskId = KorpuskopTaskManager::createFromExport(
            $this->getCorpusId(),
            $export,
            $input_kind,
            isset($user['user_id']) ? intval($user['user_id']) : $this->getUserId(),
            array(
                'focus_words' => isset($_POST['focus_words']) ? $_POST['focus_words'] : array(),
            )
        );

        return array(
            'task_id' => $taskId,
            'output_path' => '',
            'config_json' => '',
        );
    }
}
