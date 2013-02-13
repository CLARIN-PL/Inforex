<?php
/*
 * Jan Kocoń <jan.kocon@pwr.wroc.pl>
 */
global $config;
include("../cliopt.php");
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");


mb_internal_encoding("UTF-8");

//configure parameters
$opt = new Cliopt();
$opt->addExecute("php export-ccl.php --ccldir ccl_dir --iobdir iob_dir",null);
$opt->addParameter(new ClioptParameter("ccldir", "i", "path", "path to folder with input CCL files"));
$opt->addParameter(new ClioptParameter("iobdir", "o", "path", "path to folder with output IOB files"));
$opt->addParameter(new ClioptParameter("ignchan", "n", "channel_name", "ignore channels (optional, multi)"));
$opt->addParameter(new ClioptParameter("contains", "s", "substring", "only channels which contain 'substring' (optional, single)"));
$opt->addParameter(new ClioptParameter("single", "f", "file_name", "write output to a single file (optional, single)"));


//get parameters
$config = null;
try {
	$opt->parseCli($argv);
	$ccldir = $opt->getRequired("ccldir");
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}
$ignChannels = $opt->getOptionalParameters("ignchan");
$contains = $opt->getOptionalParameters("contains");
if (count($contains))
	$contains = $contains[0];
$cWcclDocuments = CclReader::readCclDocumentFromFolder2($ccldir, $ignChannels, $contains);
$exportManager = new ExportManager();
$channelPriority = $exportManager->channelPriority;

if ($opt->exists("single")){
	$singleFile = $opt->getOptionalParameters("single");
	if (count($singleFile))
		$singleFile = $singleFile[0];
	$writer = new IobWriter($singleFile, $channelPriority);
	$writer->writeAll($cWcclDocuments);
	$writer->close();	
}
else {
	try {
		$iobdir = $opt->getRequired("iobdir");
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
