<?php

class ExportTempDirManager {

    private $workDirName = "/tmp/";

    public function __construct($report_id = 0,$dirNameSuffix = "") {

        $this->workDirName = $this->createWorkDirName($dirNameSuffix);
        $this->report_id = $report_id;
        $this->makeWorkDir();

    } // __construct()

    public function __destruct() {

        $this->removeAllFiles();
        rmdir($this->getWorkDirName());

    }

    private function createWorkDirName($dirNameSuffix) {

        return '/tmp/Inforex'.'_'.$dirNameSuffix.'/';

    } // createWorkDirName()

    private function makeWorkDir() {

        $dir = $this->getWorkDirName();
        if(is_dir($dir)) {
            $this->removeAllFiles();
        } else {
            mkdir($dir);
        }

    } // makeWorkDir

    private function createFileBasename() {

        return str_pad($this->report_id,8,'0',STR_PAD_LEFT);

    } // createFileBaseName()

    public function removeAllFiles() {

        $basename = $this->getBaseFilename();
        if(trim($basename)) { // do only if no empty
            // remove all files and directories created
            foreach(array('conll','ini','json','txt','rel.xml','xml') as $ext) {
                $fullname = $basename.'.'.$ext;
                if(file_exists($fullname))
                    unlink($fullname);
            }
        }

    } // removeWorkDir()

    public function getWorkDirName() {

        return $this->workDirName;

    } // getWorkDirName()

    public function getBaseFilename() {

        return $this->getWorkDirName().$this->createFileBasename();

    } // getOutputBaseFilename() 

}

?>
