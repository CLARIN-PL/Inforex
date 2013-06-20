<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/** 
 * Converts set of files from ccl to tei format. 
 * Before convertion the annotations are flatten according to prorities
 * defined in ExportManager.
 */ 
 
$engine = "../../engine/";
include($engine . "config.php");
include($engine . "config.local.php");
include($engine . "include.php");
include($engine . "cliopt.php");

mb_internal_encoding("UTF-8");

//--------------------------------------------------------

//configure parameters
$opt = new Cliopt();
$opt->addExecute("php ccl2tei.php -i file.ccl -o folder",null);
$opt->addExecute("php ccl2tei.php -i batch.txt -o folder -b",null);
$opt->addParameter(new ClioptParameter("input", "i", "file", "ccl or batch file"));
$opt->addParameter(new ClioptParameter("output", "o", "folder", "output_folder"));
$opt->addParameter(new ClioptParameter("batch", "b", null, "treat input file as a batch file"));

//get parameters & set db configuration
$paths = array();
$config = null;

try {
	$opt->parseCli($argv);
	$batch = $opt->exists("batch");
	$input = $opt->getRequired("input");
	$folder = $opt->getRequired("output");
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

if ( $batch ){
	$dirname = dirname($input);
	foreach ( file($input) as $filename ) {
		$paths[] = $dirname . '/' . trim($filename);
	}
}else{
	$paths[] = $input;
}

if (!is_dir($folder)) 
	mkdir($folder, 0777);
	
$exportManager = new ExportManager();
$channelPriority = $exportManager->channelPriority;
$fl = new CclAnnotationFlattern($channelPriority);
$tei = new TeiWriter();

foreach ($paths as $path){
	$ccl = CclReader::readCclFromFile($path, array(), false, "_nam");
	$fl->flattenDocument($ccl);	
	$tei->ccl2teiWrite($ccl, $folder, basename($path, ".xml"), "wcrft");	
}

?>
