<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$engine = realpath(dirname(__FILE__) . "/../engine/");
include($engine . "/config.php");
include($engine . "/config.local.php");
include($engine . "/include.php");
include($engine . "/cliopt.php");
include($engine . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();

/******************** set configuration   *********************************************/

define("PARAM_DB_URI", "db-uri");
define("PARAM_SUBCORPUS", "subcorpus");
define("PARAM_DOCUMENT", "document");
define("PARAM_USER", "user");
define("PARAM_STORE", "store");

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter(PARAM_DB_URI, "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter(PARAM_SUBCORPUS, "s", "id", "subcorpus ID"));
$opt->addParameter(new ClioptParameter(PARAM_DOCUMENT, "d", "id", "id of the document"));
$opt->addParameter(new ClioptParameter(PARAM_USER, "u", "id", "user ID"));
$opt->addParameter(new ClioptParameter(PARAM_STORE, "S", null, "store results in the database"));

/******************** parse cli *********************************************/
try{
    /** Parse cli parameters */
    $opt->parseCli($argv);

    $modelsAnnotationSets = array("n82"=>1, "timex4"=>15);

    $dsn = CliOptCommon::parseDbParameters($opt, $config->dsn);
    $subcorpusIds = $opt->getParameters(PARAM_SUBCORPUS);
    $documentIds = $opt->getParameters(PARAM_DOCUMENT);
    $ownerUserId = $opt->getRequired(PARAM_USER);
    $store = $opt->exists(PARAM_STORE);
    $corpusId = null;
    $flags = null;
    $model = "n82";
    $annotationStage = "new";
    $annotationSetId = $modelsAnnotationSets[$model];

    /** Setup database  */
    $GLOBALS['db'] = new Database($dsn,false);

    $annotationNameIndex = DbAnnotationType::getAnnotationTypesForSetAsNameToIdMap($annotationSetId);
    $logger = new GroupedLogger();

    /** Validate parameters  */
    CliOptCommon::validateUserId($ownerUserId);
    CliOptCommon::validateSubcorpusId($subcorpusIds, true);
    CliOptCommon::validateDocumentId($documentIds, true);
    if ( count($annotationNameIndex) == 0 ){
        throw new Exception("Annotation set is empty, there no annotation types");
    }
    echo "Parameters validation... OK\n";

    /** Process the request  */
    $lpmn = sprintf('any2txt|wcrft2|liner2({"model":"%s"})', $model);
    $nlprest = new NlpRest2($lpmn);
    $reports = DbReport::getReports($corpusId, $subcorpusIds, $documentIds, $flags, array('id', 'content'));
    $reportCount = count($reports);
    echo "Number of documents to process: $reportCount\n";

    $n=0;
    foreach ($reports as $report){
        $n++;
        $reportId = $report['id'];
        echo "Processing document id=$reportId ($n out of $reportCount)\n";
        $ccl = $nlprest->processSync($report['content']);
        $annotations = HelperBootstrap::transformCclToAnnotations($ccl);
        echo "  ..number of annotations: " . count($annotations) . "\n";
        foreach ($annotations as $an){
            if (!isset($annotationNameIndex[$an->getType()])){
                $logger->warn("Annotation type {$an->getType()} not found in the mapping", "Error for $reportId");
            } else {
                $an->setReportId($reportId);
                $an->setTypeId($annotationNameIndex[$an->getType()]);
                $an->setUserId($ownerUserId);
                $an->setCreationTime(date("Y-m-d H:i:s"));
                $an->setStage($annotationStage);
                $an->setSource("bootstrapping");
                if ( $store ) {
                    $an->save();
                } else {
                    print_r($an);
                }
            }
        }
    }
    $logger->printLogs();

}catch(Exception $ex){
    print "!! ". $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    die("\n");
}