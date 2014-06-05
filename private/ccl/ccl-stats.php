<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
$engine = "../../engine/";
include($engine . "config.php");
include($engine . "config.local.php");
include($engine . "include.php");
include($engine . "cliopt.php");

mb_internal_encoding("UTF-8");

//configure parameters
$opt = new Cliopt();
$opt->addExecute("php ccl-stats.php -i batch.txt", "Process a list of files");
$opt->addParameter(new ClioptParameter("input", "i", "path", "path to a file with a list of CCL files"));


//get parameters
$config = null;
try {
	$opt->parseCli($argv);
	$input = $opt->getRequired("input");
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

$cWcclDocuments = CclReader::readCclDocumentBatch($input);
$anns = array();
$rels = array();
foreach ($cWcclDocuments as $ccl){
	echo "Read " . $ccl->fileName . "\n";
	$andoc = DocumentConverter::wcclDocument2AnnotatedDocument($ccl);
	foreach ($andoc->chunks as $chunk )
		foreach ($chunk->sentences as $sentence)
			foreach ($sentence->annotations as $annotation)
				if (isset($anns[$annotation->type]))
					$anns[$annotation->type]++;
				else
					$anns[$annotation->type]=1;
	foreach ($andoc->relations as $rel){
		if (isset($rels[$rel->type]))
			$rels[$rel->type]++;
		else
			$rels[$rel->type]=1;
	}
}
ksort($anns);
print_r($anns);
ksort($rels);
print_r($rels);
echo "Documents " . count($cWcclDocuments) . "\n";
	
?>
