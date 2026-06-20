<?php

require_once dirname(__FILE__) . '/../include/database/CDbKorpuskopRun.php';

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
            $this->streamZipFile($file, $downloadName);
            exit();
        }
        if (is_dir($file) && is_readable($file)) {
            $this->streamDirectoryAsZip($file, $downloadName . '.zip');
            exit();
        }

        error_log("Korpuskop file not found: $file");
        $this->set('file', $file);
        $this->set('run', $run);
    }

    private function streamZipFile($file, $downloadName){
        $this->prepareDownloadHeaders($downloadName, filesize($file));
        $handle = fopen($file, 'rb');
        while (!feof($handle)) {
            echo fread($handle, 1048576);
            flush();
        }
        fclose($handle);
    }

    private function streamDirectoryAsZip($directory, $downloadName){
        $tmpZip = tempnam(sys_get_temp_dir(), 'korpuskop_');
        $zip = new ZipArchive();
        if ($zip->open($tmpZip, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Nie udało się przygotować archiwum ZIP raportu Korpuskop.');
        }

        $rootLength = strlen(rtrim($directory, DIRECTORY_SEPARATOR)) + 1;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $zip->addFile($file->getPathname(), substr($file->getPathname(), $rootLength));
            }
        }
        $zip->close();

        $this->prepareDownloadHeaders($downloadName, filesize($tmpZip));
        $handle = fopen($tmpZip, 'rb');
        while (!feof($handle)) {
            echo fread($handle, 1048576);
            flush();
        }
        fclose($handle);
        unlink($tmpZip);
    }

    private function prepareDownloadHeaders($downloadName, $contentLength){
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
        header('Content-Length: ' . $contentLength);
        header('Cache-Control: no-store, no-cache, must-revalidate, no-transform');
        header('Pragma: no-cache');
        header('X-Accel-Buffering: no');
    }
}
