<?php


class TaskProcessorUploadZipTxt extends ATaskProcessor{

    var $subcorpora = array();
    var $autosplit = false;

    function process(){
        $params = $this->getParameters();
        $path = $params['path'];
        $this->autosplit = boolval($params['autosplit']);
        $this->subcorpusId = $params['subcorpus_id'] ? $params['subcorpus_id'] : null;
        $this->subcorpora = $this->getSubcorpora($this->task->getCorpusId());

        $this->importZip($path, $this->task->getUserId(), $this->task->getCorpusId());
        unlink($path);
    }

    function importZip($path, $userId, $corpusId){
        global $db;
        $this->info("Importing to corpus $corpusId from zip $path");
        $zip = new ZipArchive();
        $res = $zip->open($path);

        if ($res !== TRUE) {
            throw new Exception("Failed to zip file");
        }

        $tempfile = tempnam(sys_get_temp_dir(), "upload_" . $corpusId);
        if (file_exists($tempfile)) { unlink($tempfile); }
        mkdir($tempfile);
        $zip->extractTo($tempfile);
        $zip->close();

        $files = array();
        $this->getDirContents($tempfile, $files);

        $this->task->setMaxSteps(count($files));
        $this->task->update();
        $currentStep=0;

        foreach ($files as $path){
            $this->task->setCurrentStep(++$currentStep);
            $this->task->update();

            $file_extension = pathinfo($path, PATHINFO_EXTENSION);
            $filename = basename($path, ".".$file_extension);
            $basename = basename($filename);

            if ( $file_extension != "txt" ){
                continue;
            }

            $message = "The document was uploaded correctly";
            $source = "";
            $author = "";
            $date = null;

            $inipath = substr($path, 0, strlen($filename)-4) . ".ini";
            if ( file_exists($inipath) ){
                $ini = parse_ini_file($inipath, true, INI_SCANNER_RAW);
                $title = $this->parseTitle($ini["metadata"]["title"], $basename);
                $source = $ini["metadata"]["url"];
                $author = $ini["metadata"]["author"];
                $date =  $this->parseDate($ini["metadata"]["publish_date"]);;
            } else {
                $message = "The document content was uploaded correctly. A file with metadata was not found.";
            }

            list($title, $subcorpusName) = $this->splitBasename($basename);
            $document = array();
            $document['subcorpus_id'] = $this->getSubcorpusId($subcorpusName, $corpusId);
            $document['corpora'] = $corpusId;
            $document['title'] = $title;
            $document['source'] = $source;
            $document['author'] = $author;
            $document['date'] = $date;
            $document['user_id'] = $userId;
            $document['filename'] = $filename;
            $document['content'] = file_get_contents($path);
            $document['status'] = 2;
            $document['format_id'] = 2; // TXT
            $db->insert("reports", $document);

            $report_id = $db->last_id();
            DbReport::insertEmptyReportExt($report_id);

            $taskDocuments = array();
            $taskDocuments[DB_COLUMN_TASKS_REPORTS__TASK_ID] = $this->task->getId();
            $taskDocuments[DB_COLUMN_TASKS_REPORTS__REPORT_ID] = $report_id;
            $taskDocuments[DB_COLUMN_TASKS_REPORTS__STATUS] = "done";
            $taskDocuments[DB_COLUMN_TASKS_REPORTS__MESSAGE] = $message;
            $db->insert(DB_TABLE_TASKS_REPORTS, $taskDocuments);

            $this->info("- file $title imported");
        }

        $this->rmrecursively($tempfile);
    }

    function splitBasename($basename){
        $title = $basename;
        $subcorpusName = null;
        if ( $this->autosplit ) {
            $parts = explode("-", $basename);
            if (count($parts) > 1) {
                $subcorpusName = $parts[0];
                $title = $parts[1];
            }
        }
        return array($title, $subcorpusName);
    }

    function getSubcorpora($corpusId){
        $subcorpora = array();
        foreach ( DbCorpus::getCorpusSubcorpora($corpusId) as $row ){
            $subcorpora[strtolower($row['name'])] = $row['subcorpus_id'];
        }
        return $subcorpora;
    }

    function getSubcorpusId($subcorpusName, $corpusId){
        if ( $this->autosplit ) {
            if ($subcorpusName != null) {
                if (!isset($this->subcorpora[strtolower($subcorpusName)])) {
                    $subcorpus_id = DbCorpus::createSubcopus($corpusId, $subcorpusName, "");
                    $this->subcorpora[strtolower($subcorpusName)] = $subcorpus_id;
                } else {
                    $subcorpus_id = $this->subcorpora[strtolower($subcorpusName)];
                }
                return $subcorpus_id;
            }
        }
        return $this->subcorpusId;
    }

    function getDirContents($dir, &$results = array()){
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    function parseDate($date){
        $date = explode(" ", $date);
        $date = $date[0];
        $date = trim($date);
        if ( $date == "" ){
            return null;
        } else {
            return date("Y-m-d", strtotime($date));
        }
    }

    function parseTitle($title, $titleIfEmpty){
        $title = trim($title);
        return $title == "" ? $titleIfEmpty : $title;
    }

    function rmrecursively($target) {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
            foreach( $files as $file ){
                $this->rmrecursively( $file );
            }
            if (file_exists($target)) {
                rmdir($target);
            }
        } elseif(is_file($target)) {
            unlink( $target );
        }
    }
}