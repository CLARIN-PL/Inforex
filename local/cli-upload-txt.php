<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath. DIRECTORY_SEPARATOR . 'include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR )."config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-32");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to a folder with documents"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "subcorpus ID"));
$opt->addParameter(new ClioptParameter("user", "u", "id", "user ID"));

/******************** parse cli *********************************************/
try{
    /** Parse cli parameters */
	$opt->parseCli($argv);
    $dsn = CliOptCommon::parseDbParameters($opt, array("localhost", "root", null, "gpw", "3306"));
	$sourceFolder = $opt->getRequired("folder");
	$targetSubcorpusId = $opt->getRequired("subcorpus");
	$ownerUserId = $opt->getRequired("user");

    /** Setup database  */
    $GLOBALS['db'] = new Database($dsn,false);

    /** Validate parameters  */
	CliOptCommon::validateUserId($ownerUserId);
	CliOptCommon::validateSubcorpusId($targetSubcorpusId);
	CliOptCommon::validateFolderExists($sourceFolder);

    /** Process the request  */
    $corpus = DbSuborpus::get($targetSubcorpusId);
    $corpusId = intval($corpus['corpus_id']);
    $importer = new CorpusDocumentImporter($corpusId);

    $files = DocumentReaderTxt::getFolderFiles($sourceFolder);
	$pairs = DocumentReaderTxt::pairTxtAndIniFiles($files);

	foreach ($pairs as $item){
		if ( isset($item["ini"]) ) {
		    echo "Processing {$item["txt"]}\n";
            $ini = DocumentReaderTxt::loadMetadataFromIniFile($item["ini"]);
            $content = file_get_contents($item["txt"]);
            $ini["metadata"]["subcorpus_id"] = $targetSubcorpusId;
            $ini["metadata"]["format_id"] = 2; // plain
            $ini["metadata"]["user_id"] = $ownerUserId;
            $ini["metadata"]["status"] = 2;
            $ini["metadata"]["lang"] = "pl"; // TODO make as script parameter
            $importer->insert($content, $ini["metadata"], $ini["custom"]);
        }
	}
    $importer->printLogs();

}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	print("\n");
	return;
}
