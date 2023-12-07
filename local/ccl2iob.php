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
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");
require_once($enginePath . "/cliopt.php");

mb_internal_encoding("UTF-8");

//configure parameters
$opt = new Cliopt();
$opt->addExecute("php export-ccl.php -i batch.txt -o output.iob", "Convert all annotations.");
$opt->addExecute("php export-ccl.php -i batch.txt -o output.iob -c _nam", "Keep only annotations with '_nam'.");
$opt->addParameter(new ClioptParameter("input", "i", "path", "path to a file with a list of CCL files"));
$opt->addParameter(new ClioptParameter("output", "o", "path", "path to an IOB file or folder"));
$opt->addParameter(new ClioptParameter("ignore", "n", "channel_name", "ignore channels (optional, multi)"));
$opt->addParameter(new ClioptParameter("reverse", "r", null, "reverse ignore annotations"));
$opt->addParameter(new ClioptParameter("contains", "c", "substring", "only channels which contain 'substring' (optional, single)"));
$opt->addParameter(new ClioptParameter("separate", "s", "file_name", "write output to a single file (optional, single)"));

//get parameters
$config = null;
try {
	$opt->parseCli($argv);
	$input = $opt->getRequired("input");
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	print("\n");
	return;
}
$ignChannels = $opt->getOptionalParameters("ignore");
$contains = $opt->getOptionalParameters("contains");
$reverse = $opt->exists("reverse");
if (count($contains))
	$contains = $contains[0];
$cWcclDocuments = CclReader::readCclDocumentBatch($input, $ignChannels, $reverse, $contains);
$channelPriority = array(
            "title_nam"=>7,
            "software_nam"=>7,
            "event_nam"=>6,
            "road_nam"=>5,
            "facility_nam"=>4,
            "company_nam"=>3,
            "astronomical_nam"=>3,
            "person_nam"=>2,
            "city_nam"=>1);

if ( !$opt->exists("separate") ){
	$output = $opt->getRequired("output");
	$writer = new IobWriter($output, $channelPriority);
	$writer->writeAll($cWcclDocuments);
	$writer->close();	
}
else {
	try {
		$iobdir = $opt->getRequired("output");
	} 
	catch(Exception $ex){
		print "!! ". $ex->getMessage() . " !!\n\n";
		$opt->printHelp();
		die("\n");
	}
	$subfolder = $iobdir . "/";
	if (!is_dir($subfolder)) mkdir($subfolder, 0777);
	foreach ($cWcclDocuments as $ccl){
		$filename = $subfolder . $ccl->getFileName() . ".iob";
		$writer = new IobWriter($filename, $channelPriority);
		$cclSet = array($ccl);
		$writer->writeAll($cclSet);
		$writer->close();
	}
}
	
?>
