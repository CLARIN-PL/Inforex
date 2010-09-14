<?php
/* This script align a Takipi document with in-line annotaion. 
 * Input: 
 *  - a file with a Takipi document,
 *  - a file with a text with in-line annotations,
 * Output:
 *  - a Takipi document with IOB annotations.
 *  
 *  The script can process a single file or all files in given folder.
 * 
 * 
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
include("../engine/include/anntakipi/ixtTakipiStruct.php"); 
include("../engine/include/anntakipi/ixtTakipiReader.php"); 
include("../engine/include/anntakipi/ixtTakipiWriter.php"); 
include("../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../engine/include/anntakipi/ixtTakipiAligner.php"); 
include("../engine/include/anntakipi/ixtTakipiHelper.php"); 

include("cliopt.php");
 
function myErrorHandler($errno, $errstr, $errfile, $errline){
	print "\n";
	print "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
	print "!! Errno  : $errno \n";
	print "!! Errstr : $errstr \n";
	print "!! Errfile: $errfile \n";
	print "!! Errline: $errline \n";
	print "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
	print "\n";
}
set_error_handler("myErrorHandler");

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("location", null, "folder", "folder with files to process"));
$opt->addParameter(new ClioptParameter("input", null, "filename", "single file to process"));
$opt->addParameter(new ClioptParameter("output", null, "filename", "where to save result"));
$opt->addParameter(new ClioptParameter("dry-run", null, null, "process without saving results"));

$config = (object) array();
$config->ignore = array();

/******************** parse cli *********************************************/

try{
	$opt->parseCli($argv);

	if ($opt->exists("input")){
		$config->input = $opt->getRequired("input");
		$config->output = $opt->getRequired("output");
	}else{
		$config->location = $opt->getRequired("location");
	}
	
	$config->dryrun = $opt->exists("dry-run"); 
	
}catch(Exception $ex){
	print "\n!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** functions           *********************************************/
// 
function process($input, $output, $config){
	global $config;
	
	if (!file_exists($input))
		throw new Exception("File '$input' does not exist\n");
				
	echo sprintf("Loaded file: %-40s \n", $input);
	
	$ann = TakipiHelper::replace(file_get_contents(str_replace(array("/tag/", ".tag"), array("/annotated/", ""), $input)));	
			
	$r = new TakipiReader();
	$r->loadFile($input);	
	$document = $r->readDocument();
	$r->close();
		
	TakipiAligner::align($ann, $document);
		
	if (!$config->dryrun){
		$w = new TakipiWriter($output);
		$w->writeDocument($document); 
		$w->close();	
	}
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){
	
	$files = array();
	
	if ( isset($config->input) ){
		$files[$config->input] = $config->output;
	}else{
		$folder_input = realpath($config->location) . DIRECTORY_SEPARATOR ."tag" . DIRECTORY_SEPARATOR;
		$folder_output = realpath($config->location) . DIRECTORY_SEPARATOR ."tag-iob" . DIRECTORY_SEPARATOR;
		if (!$config->dryrun && !file_exists($folder_output))
			mkdir($folder_output);
		if ($handle = opendir($config->location."/tag"))
			while ( false !== ($file = readdir($handle)))
				if ($file != "." && $file != "..")
					$files[$folder_input . $file] = $folder_output . $file;															
	} 
	
	foreach ($files as $input=>$output){
		process($input, $output, $config);
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>
