<?php

require_once dirname(__FILE__) . '/../include/integration/KorpuskopTaskManager.php';

class Page_corpus_korpuskop extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anySystemRole = array();
        $this->anyCorpusRole = array();
        $this->includeJs('js/page_corpus_korpuskop.js');
        $this->includeJs('js/page_corpus_export.js');
        $this->includeJs('js/c_widget_relation_structure.js');
        $this->includeJs('js/c_widget_annotation_layers_and_subsets.js');
        $this->includeCss('css/page_corpus_export.css');
        $this->includeCss('css/page_corpus_korpuskop.css');
        $this->includeCss('css/c_widget_annotation_layers_and_subsets.css');
    }

    function customPermissionRule($user = null, $corpus = null){
        return hasUserReportGenerationAccess($user, $corpus);
    }

    function execute(){
        global $corpus;

        $this->setDefaults();
        $this->setupExportWidgets($corpus['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = isset($_POST['korpuskop_action']) ? trim((string) $_POST['korpuskop_action']) : '';
            try {
                if ($action === 'delete_run') {
                    $this->deleteRun();
                }
            } catch (Exception $ex) {
                $this->set('korpuskop_error', $ex->getMessage());
            }
        }

        $showTaskPanel = !empty($_GET['show_task']) || !empty($_GET['show_export']);
        $activeTaskId = $showTaskPanel ? intval(isset($_GET['task_id']) ? $_GET['task_id'] : 0) : 0;
        $activeExportId = $showTaskPanel ? intval(isset($_GET['export_id']) ? $_GET['export_id'] : 0) : 0;
        $this->set('korpuskop_active_task_id', $activeTaskId > 0 ? $activeTaskId : null);
        $this->set('korpuskop_active_export_id', $activeExportId > 0 ? $activeExportId : null);
        if (isset($_GET['deleted'])) {
            $this->set('korpuskop_notice', 'The history entry and related report files were removed.');
        }
    }

    private function setDefaults(){
        $this->set('korpuskop_error', null);
        $this->set('korpuskop_notice', null);
        $this->set('korpuskop_history_error', null);
        $this->set('korpuskop_runs', $this->safeGetRuns());
        $this->set('korpuskop_active_task_id', null);
        $this->set('korpuskop_default_kind', KorpuskopRunner::INPUT_KIND_DOCUMENT);
        $this->set('korpuskop_formats', array(
            KorpuskopRunner::INPUT_KIND_DOCUMENT => array(
                'value' => 'clarin_parquet_zst',
                'label' => 'CLARIN Parquet ZST',
            ),
            KorpuskopRunner::INPUT_KIND_DIALOG => array(
                'value' => 'dialog_parquet_zst',
                'label' => 'Dialog parquet',
            ),
        ));
    }

    private function setupExportWidgets($corpus_id){
        $corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
        $flags = DbCorporaFlag::getFlags();

        $this->setup_annotation_type_tree($corpus_id);
        $this->setup_relation_type_tree($corpus_id);
        $this->set("corpus_flags", $corpus_flags);
        $this->set("flags", $flags);
        $this->set("users", DbCorporaUsers::getCorpusUsers($corpus_id));
        $this->set("morpho_users", DbCorporaUsers::getCorpusUsers($corpus_id));
    }

    private function setup_annotation_type_tree($corpus_id){
        $annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);

        $morphoAnnotations = array(
            array('name' => 'Only tagger', 'value'=> 'tagger', 'help' => 'Get only tagger decisions.'),
            array('name' => 'User', 'value'=> 'user', 'help' => 'Get specific user decision.'),
            array('name' => 'Final', 'value'=> 'final', 'help' => 'Get final annotation decision after agreement in 2+1 system.'),
            array('name' => 'Final or tagger', 'value'=> 'final_or_tagger', 'help' => 'Get final decision or tagger if final annotation is not present.'),
        );
        $this->set('annotation_types',$annotations);
        $this->set('morpho_annotation_types',$morphoAnnotations);
    }

    private function setup_relation_type_tree($corpus_id){
        $relations = DbRelationSet::getRelationStructureTree($corpus_id);
        $this->set('relation_types', $relations);
    }

    private function deleteRun(){
        $runId = intval(isset($_POST['run_id']) ? $_POST['run_id'] : 0);
        if ($runId <= 0) {
            throw new RuntimeException('Invalid Korpuskop run identifier.');
        }

        $run = DbKorpuskopRun::getRunForCorpus($runId, $this->getCorpusId());
        if (!$run || !isset($run['run_id'])) {
            throw new RuntimeException('The selected Korpuskop run was not found.');
        }

        foreach (array('output_path', 'progress_file') as $field) {
            if (!empty($run[$field]) && is_file($run[$field])) {
                @unlink($run[$field]);
            } else if (!empty($run[$field]) && is_dir($run[$field])) {
                $this->deleteDirectory($run[$field]);
            }
        }

        DbKorpuskopRun::deleteRunForCorpus($runId, $this->getCorpusId());
        header('Location: index.php?page=corpus_korpuskop&corpus=' . $this->getCorpusId() . '&deleted=1');
        exit();
    }

    private function deleteDirectory($directory){
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @rmdir($file->getPathname());
            } else {
                @unlink($file->getPathname());
            }
        }
        @rmdir($directory);
    }

    private function safeGetRuns(){
        try {
            return DbKorpuskopRun::getRunsByCorpus($this->getCorpusId(), 25);
        } catch (Exception $ex) {
            $this->set('korpuskop_history_error', 'Report history is not available yet. Add the `korpuskop_runs` table according to the deployment guide.');
            return array();
        }
    }

}
