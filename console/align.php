<?php
/* 
 * ---
 * Test urównoleglenia pliku otagowane z plikiem oznakowanym.
 * Wywołanie:
 *   php align.php all     // test all files in a folder
 *   php align.php resume  // continue testing all files in a folder 
 *   php align.php <id>    // test a files with given id
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
include("../engine/include/anntakipi/ixtTakipiAligner.php"); 
include("../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../engine/include/anntakipi/ixtTakipiHelper.php"); 
 
/******************** set configuration   *********************************************/
$config = null;
$config->location = "/nlp/corpora/takipi/gpw2004-beta";
$config->option = $argv[1];

/******************** check configuration *********************************************/

if ($config->option != "all" && $config->option != "resume" && intval($config->option)==0) 
	die ("Incorrect argument. Expected one of the following formats:\n" .
			"php align.php all       // test all files in a folder\n" .
			"php align.php resume    // continue testing all files in a folder\n" .
			"php align.php <id>      // test a files with given id\n\n");  

/******************** functions           *********************************************/
// 
function align($textfile, $tagfile){
	if (!file_exists($tagfile))
		throw new Exception("File '$tagfile' does not exist\n");		
	echo sprintf("%-30s ", $textfile);
	$takipiDoc = TakipiDocument::createFromFile($tagfile);	
	$text = file_get_contents($textfile);
	$text = TakipiHelper::replace($text);	
	$annDoc = TakipiAligner::align($text, $takipiDoc);
	foreach ($annDoc->annotations as $an)
		if (trim($an->name)=='')
			throw new Exception("Noname annotation in {$textfile}: " . $an->to_string());
	echo sprintf("%3d annotation(s)", count($annDoc->annotations));	
}

// Convert a name of a file with annotation to the name of tagged file.
function get_tagged_filename($annotation_file){
	return preg_replace("/(\/annotated\/)(?!.*\1)/", "/tag/", $annotation_file).".tag";
}

/******************** main function       *********************************************/
// Pricess all files in a folder
function main ($config){
	if ($config->option == "all" || $config->option == "resume" ){
		if ( file_exists("progress.txt") && $config->option == "resume" )
			$progress = explode(",", file_get_contents("progress.txt"));
		else
			$progress = array();
		
		$i = count($progress) + 1;
		
		if ($handle = opendir($config->location."/text")){
			while ( false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".."){
					
					if (in_array($file, $progress)) continue;
					$annotation_filename = $config->location . "/annotated/" . $file; 
					align($annotation_filename, get_tagged_filename($annotation_filename));
					echo sprintf("%8d\n", $i++);
					$progress[] = $file;
					file_put_contents("progress.txt", implode(",", $progress));				
				}
			}
			// If we reach this point we can remove the progress temp file
			unlink("progress.txt");
		}
	// Process a single file
	} else {
		$filename = str_pad($config->option, 7, "0", STR_PAD_LEFT) . ".txt";
		$annotation_filename = $config->location . "/annotated/" . $filename; 
		align($annotation_filename, get_tagged_filename($annotation_filename));
		echo "\n";	
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>
