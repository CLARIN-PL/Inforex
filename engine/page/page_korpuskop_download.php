<?php

class Page_korpuskop_download extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anySystemRole = array();
        $this->anyCorpusRole = array();
    }

    function customPermissionRule($user = null, $corpus = null){
        return hasUserReportGenerationAccess($user, $corpus);
    }

    function execute(){
        $run_id = intval(isset($_GET['run_id']) ? $_GET['run_id'] : 0);
        $run = DbKorpuskopRun::getRunForCorpus($run_id, $this->getCorpusId());
        $file = isset($run['output_path']) ? $run['output_path'] : '';
        $downloadName = $file !== '' ? basename($file) : 'korpuskop_report.zip';
        if (is_file($file) && is_readable($file)) {
            session_write_close();
            set_time_limit(0);
            ini_set('zlib.output_compression', 'Off');
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . str_replace('"', '', $downloadName) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Encoding: identity');
            header('Content-Length: ' . filesize($file));
            header('Cache-Control: no-store, no-cache, must-revalidate, no-transform');
            header('Pragma: no-cache');
            header('X-Accel-Buffering: no');
            $handle = fopen($file, 'rb');
            while (!feof($handle)) {
                echo fread($handle, 1048576);
                flush();
            }
            fclose($handle);
            exit();
        }

        error_log("Korpuskop file not found: $file");
        $this->set('file', $file);
        $this->set('run', $run);
    }
}
