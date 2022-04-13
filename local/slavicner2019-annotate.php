<?php

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath. DIRECTORY_SEPARATOR . 'include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");

require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-32");
ob_end_clean();

/***
 * Action class
 ***************************************************************/
class ActionAnnotateTsv{

    var $opt = null;
    var $dsn = null;
    var $folder = null;
    var $corpusId = null;
    var $typeToId = array();
    var $typeToAttributeId = array();
    var $stage = "final"; //TODO
    var $userId = 1; //TODO
    var $errorCount = 0;

    function __construct(){
        $this->opt = $this->getCliopt();

        // ToDo: Make as a parameter
        $this->typeToId["LOC"] = 26773;
        $this->typeToId["PER"] = 26772;
        $this->typeToId["EVT"] = 26775;
        $this->typeToId["ORG"] = 26774;
        $this->typeToId["PRO"] = 26776;

        $this->typeToAttributeId["LOC"] = 8;
        $this->typeToAttributeId["PER"] = 6;
        $this->typeToAttributeId["EVT"] = 5;
        $this->typeToAttributeId["ORG"] = 7;
        $this->typeToAttributeId["PRO"] = 9;

    }

    function getCliopt(){
        $opt = new Cliopt("Import annotations from a TSV file in BSNLP format");
        $opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
        $opt->addParameter(new ClioptParameter("folder", "f", "path", "path to a folder to load TSV file with annotations"));
        $opt->addParameter(new ClioptParameter("corpus", "c", "id", "corpus ID"));
        return $opt;
    }

    function printHelp(){
        $this->opt->printHelp();
    }

    function parseArgv($argv){
        $this->opt->parseCli(isset($argv) ? $argv : null);
        $this->dsn = CliOptCommon::parseDbParameters($this->opt, array("localhost", "root", null, "gpw", "3306"));
        $this->folder = $this->opt->getRequired("folder");
        $this->corpusId = $this->opt->getRequired("corpus");

        /** Setup database  */
        $GLOBALS['db'] = new Database($this->dsn,false);

        /** Validate parameters  */
        CliOptCommon::validateCorpusId($this->corpusId, $this->corpusId);
        CliOptCommon::validateFolderExists($this->folder);
    }

    function process(){
        $paths = HelperPath::loadFilePathsWithExtensionFromFolder($this->folder, "out");
        $n=1;
        foreach ($paths as $path){
            echo "[INFO] File $n: $path \n";
            $this->processFile($path);
            $n++;
        }
    }

    function processFile($path){
        try {
            $this->errorCount = 0;

            $annotations = $this->loadAnnotationsFromTsv($path);
            $filename = HelperPath::getPathFilename($path);
            $reports = DbReport::getByFilenameAndCorpusId($filename, $this->corpusId);
            if (count($reports) != 1) {
                throw new Exception("Incorrect number of fetch reports for basename $filename");
            }
            $report = $reports[0];
            $content = $report[DB_COLUMN_REPORTS__CONTENT];
            $reportId = $report[DB_COLUMN_REPORTS__REPORT_ID];
            $this->logInfo("Report id: $reportId");

            $records = $this->processAnnotations($reportId, $content, $annotations);
            $this->assignUserIdTimestampStage($records, $this->userId, date("Y-m-d H:i:s"), $this->stage);
            $records = $this->filterNestedAnnotations($records);
            $flag = $this->errorCount == 0 ? FLAG_ID_FINISHED : FLAG_ID_ERROR;

            $records = $this->filterExistingAnnotations($records);
            $this->saveAnnotationRecords($records);

            DbReportFlag::changeFlagStatus(351, $flag, $reportId, $this->userId);
            DbReportFlag::changeFlagStatus(321, $flag, $reportId, $this->userId);
        } catch(Exception $ex){
            echo "Error: Failed to process $path \n";
            var_dump($ex);
        }
    }

    /**
     * @param String $path
     * @return AnnotationEid[]
     * @throws Exception
     */
    function loadAnnotationsFromTsv($path){
        $content = file($path);
        $id = trim($content[0]);
        if (!$id ){
            throw new Exception("Document id not found in $path");
        }
        $annotations = array();
        for ($i=1; $i<count($content); $i++){
            $line = trim($content[$i]);
            $fields = explode("\t", $line);
            if ( count($fields) != 4 ) {
                throw new Exception("Invalid number of fields in line $i: $line [$path]. ");
            }
            $annotations[] = new AnnotationEid($fields[0], $fields[1], $fields[2], $fields[3]);
        }
        return $annotations;
    }

    /**
     * @param String $content
     * @param AnnotationEid[] $annotations
     * @return TableReportAnnotation[]
     * @throws Exception
     */
    function processAnnotations($reportId, $content, $annotations){
        $html = new HtmlStr2($content);
        $records = array();
        foreach ($annotations as $an){
            $anRecords = $this->processAnnotation($content, $an, $html);
            foreach ($anRecords as $record){
                $record->setReportId($reportId);
                $records[] = $record;
            }
        }
        return $records;
    }

    /**
     * @param String $content
     * @param AnnotationEid $annotation
     * @param HtmlStr2 $html
     * @return TableReportAnnotation[]
     * @throws Exception
     */
    function processAnnotation($content, $an, $html){
        echo "____Annotation: {$an->getText()}\n";

        $typeId = $this->getTypeIdForType($an->getType());
        $sharedAttributeId = $this->getAttributeIdForType($an->getType());

        $records = array();

        $matches = $this->findPhrasesInText($an->getText(), $content);
        if ( $matches == 0 ){
            throw new Exception("No matches found for {$an->getText()}" );
        }

        foreach ($matches as $match){
            $begin = $html->rawToVisIndex($match[0]);
            $end = $html->rawToVisIndex($match[1]-1);
            $text = $html->getText($begin, $end);
            if ( $text != $an->getText() ){
                $this->logError("Phrases does not match '$text' != '{$an->getText()}'");
                $this->errorCount++;
            } else {
                $record = new TableReportAnnotation();
                $record->setFrom($begin);
                $record->setTo($end);
                $record->setTypeId($typeId);
                $record->setText($text);

                $lemma = new TableReportAnnotationLemma();
                $lemma->setLemma($an->getLemma());
                $record->setMetaLemma($lemma);

                $attribute = new TableReportAnnotationSharedAttribute();
                $attribute->setSharedAttributeId($sharedAttributeId);
                $attribute->setValue($an->getEid());
                $record->setMetaSharedAttributes(array($attribute));

                $records[] = $record;
            }
        }

        return $records;
    }

    function getTypeIdForType($type){
        if (!isset($this->typeToId[$type])){
            throw new Exception("Not found type id for $type" );
        }
        return $this->typeToId[$type];
    }

    function getAttributeIdForType($type){
        if (!isset($this->typeToAttributeId[$type])){
            throw new Exception("Not found shared attribute id for $type" );
        }
        return $this->typeToAttributeId[$type];
    }

    function findPhrasesInText($phrase, $text){
        $spans = array();
        $pos = 0;
        $ret = 0;
        while ( is_integer($ret) ){
            $ret = mb_strpos($text, $phrase, $pos, "utf-8");
            if ( is_integer($ret) ) {
                $len = mb_strlen($phrase, "utf-8");
                $spans[] = array($ret, $ret + $len);
                $pos = $ret + $len;
            }
        }
        return $spans;
    }

    /**
     * @param TableReportAnnotation[] $records
     */
    function saveAnnotationRecords($records){
        foreach ($records as $record){
            $record->save();
            foreach ($record->getMetaSharedAttributes() as $attribute){
                if ( !CDbAnnotationSharedAttribute::existsAttributeEnumValue($attribute->getSharedAttributeId(), $attribute->getValue()) ) {
                    $this->logInfo("New value for attribute {$attribute->getSharedAttributeId()}: {$attribute->getValue()}");
                    CDbAnnotationSharedAttribute::addAttributeEnumValue($attribute->getSharedAttributeId(), $attribute->getValue());
                }
            }
        }
    }

    function assignUserIdTimestampStage(&$records, $userId, $creationTime, $stage){
        foreach($records as &$record){
            $record->setUserId($userId);
            $record->setCreationTime($creationTime);
            $record->setStage($stage);
            $record->setSource("auto");

            foreach ($record->getMetaSharedAttributes() as &$attribute){
                $attribute->setUserId($userId);
            }
        }
    }

    /**
     * @param TableReportAnnotation[] $annotations
     * @return TableReportAnnotation[]
     */
    function filterNestedAnnotations($annotations){
        usort($annotations, function($a, $b){
            return $b->getLength() - $a->getLength();
        });

        $selected = array();
        $index = array();
        foreach ($annotations as $an){
            $countOverlaps = 0;
            for ($i=$an->getFrom(); $i<=$an->getTo(); $i++){
                if (isset($index[$i])){
                    $countOverlaps++;
                }
            }
            if ( $countOverlaps == 0 ){
                $selected[] = $an;
                for ($i=$an->getFrom(); $i<=$an->getTo(); $i++){
                    $index[$i] = 1;
                }
            } else {
                $this->logInfo("removed nested annotation: {$an->getText()}");
            }
        }
        return $selected;
    }

    /**
     * @param TableReportAnnotation[] $annotations
     * @return TableReportAnnotation[]
     */
    function filterExistingAnnotations($annotations){
        $selected = array();
        foreach ($annotations as $an){
            $anClone = new TableReportAnnotation();
            $anClone->setFrom($an->getFrom());
            $anClone->setTo($an->getTo());
            $anClone->setTypeId($an->getType());
            $anClone->setStage($an->getStage());
            $anClone->setReportId($an->getReportId());
            if (!$anClone->exists()){
                $selected[] = $an;
            }
        }
        return $selected;
    }

    function logError($error){
        echo "[ERROR] $error\n";
    }

    function logInfo($info){
        echo "[INFO] $info\n";
    }

    function logWarning($warning){
        echo "[WARNING] $warning\n";
    }

}


/******************** scripts entry point   *********************************************/
try{
    $action = new ActionAnnotateTsv();
    $action->parseArgv(isset($argv) ? $argv : null);
    $action->process(isset($argv) ? $argv : null);
}catch(Exception $ex){
    print "!! ". $ex->getMessage() . " !!\n\n";
    $action->printHelp();
    print("\n");
    return;
}

