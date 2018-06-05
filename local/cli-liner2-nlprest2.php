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

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "subcorpus ID"));
$opt->addParameter(new ClioptParameter("user", "u", "id", "user ID"));

/******************** parse cli *********************************************/
try{
    /** Parse cli parameters */
	$opt->parseCli($argv);
    $dsn = CliOptCommon::parseDbParameters($opt, "localhost", "root", null, "gpw", "3306");
	$subcorpusId = $opt->getRequired("subcorpus");
	$annotationSetId = 1; // TODO make as a parameters
    $ownerUserId = $opt->getRequired("user");

    /** Setup database  */
    $GLOBALS['db'] = new Database($dsn,false);

    $annotationNameIndex = DbAnnotationType::getAnnotationTypesForSetAsNameToIdMap($annotationSetId);

    /** Validate parameters  */
    CliOptCommon::validateUserId($ownerUserId);
	CliOptCommon::validateSubcorpusId($subcorpusId);
	if ( count($annotationNameIndex) == 0 ){
	    throw new Exception("Annotation set is empty, there no annotation types");
    }

    /** Process the request  */
    $nlprest = new NlpRest2('any2txt|wcrft2|liner2({"model":"n82"})');
    $reports = DbReport::getReports(null, $subcorpusId);
    $importer = new DocumentAnnotationImporter($annotationNameIndex);

	foreach ($reports as $report){
	    echo "Processing document id={$report['id']}\n";
	    $ccl = $nlprest->processSync($report['content']);
	    $importer->importAnnotationsFromCcl($report['id'], $ccl, $ownerUserId);
	}
    $importer->printLogs();

}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}