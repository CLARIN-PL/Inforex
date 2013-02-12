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
$opt->addParameter(new ClioptParameter("ccldir", "c", "path", "path to folder with input CCL files"));
$opt->addParameter(new ClioptParameter("iobdir", "i", "path", "path to folder with output IOB files"));
$opt->addParameter(new ClioptParameter("ignchan", "n", "channel_name", "ignore channels (optional, multi)"));
$opt->addParameter(new ClioptParameter("contains", "s", "string", "only channels which contain 'string' (optional, single)"));


//get parameters
$config = null;
try {
	$opt->parseCli($argv);
	$ccldir = $opt->getRequired("ccldir");
	$iobdir = $opt->getRequired("iobdir");
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
$subfolder = $iobdir . "/";
if (!is_dir($subfolder)) mkdir($subfolder, 0777);
foreach ($cWcclDocuments as $ccl){
	$filename = $subfolder . $ccl->getFileName() . ".iob";
	IobWriter::write(array($ccl), $filename);
}
	
?>
