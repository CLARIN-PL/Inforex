<?php
/* 
 * ---
 * Converts documents to IOB represenation in a simple format.
 * Wywołanie:
 *   php iob.php <corpus> all     // test all files in a folder
 *   php iob.php <corpus> resume  // continue testing all files in a folder 
 *   php iob.php <corpus> <id>    // test a files with given id
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
include("../engine/include/anntakipi/ixtTakipiAligner.php"); 
include("../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../engine/include/anntakipi/ixtTakipiHelper.php"); 

include("cliopt.php");
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("action", "type of action", array("all", "resume", "DECIMAL")));
$opt->addParameter(new ClioptParameter("corpus-location", null, "path", "path to a folder where the data will be save"));
$opt->addParameter(new ClioptParameter("output", null, "path", "name of file where to save data in a IOB format"));
$opt->addParameter(new ClioptParameter("ignore", null, "annotation", "annotation to ignore"));
$opt->addParameter(new ClioptParameter("dont-ignore", null, "annotation", "annotation not to ignore"));

$config = null;
$config->ignore = array();
$config->dontignore = array();
$config->map = array();

/******************** parse cli *********************************************/

try{
	$opt->parseCli($argv);
	
	$config->action = $opt->getArgument();
	$config->location = $opt->getRequired("corpus-location");
	$config->output = $opt->getOptional("output", "data.iob");
		
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** functions           *********************************************/
// 
function to_oai($textfile, $tagfile, $f=null){
	global $config;
	
	if (!file_exists($tagfile))
		throw new Exception("File '$tagfile' does not exist\n");		
	echo sprintf("%-40s ", $textfile);
	$takipiDoc = TakipiDocument::createFromFile($tagfile);	
	$text = file_get_contents($textfile);
	$text = TakipiHelper::replace($text);	
	$annDoc = TakipiAligner::align($text, $takipiDoc);

	foreach ($annDoc->annotations as $an)
		if (trim($an->name)=='')
			throw new Exception("Noname annotation in {$textfile}: " . $an->to_string());
	
	// Filter annotation if set.
	$all_annotation_count = count($annDoc->annotations);
	
	if (count($config->dontignore)>0)
		$annDoc->remove_other_than($config->dontignore);

	if (count($config->map)>0)
		foreach ($config->map as $from=>$to)
			$annDoc->rename_annotation_type($from, $to);
	
	$sparse = $annDoc->get_sparce_vector(count($takipiDoc->tokens));
	$final_annotation_count = count($annDoc->annotations);
	// === 
		
//	if ($final_annotation_count>0){
		$i = 0;
		fwrite($f, "-DOCSTART FILE $textfile\n");
		for ($z=0; $z<count($takipiDoc->sentenceEnds); $z++){
			for (; $i<=$takipiDoc->sentenceEnds[$z]; $i++){
				$t = $takipiDoc->tokens[$i];
				$line = sprintf("%s %s\n", trim($t->orth), $sparse[$i]);
				if ($f==null)
					echo $line;
				else
					fwrite($f, $line);
			}
			if ($f==null) echo "--EOS--\n"; else fwrite($f, "\n");
		}
		if ( $i != count($takipiDoc->tokens) )
			throw new Exception(sprintf("Number of tokens does not agree %d!=%d!", $i, count($takipiDoc->tokens)));
//	}
	
	// Print summary.
	$after = $all_annotation_count != $final_annotation_count ? " > " . sprintf("%3d", $final_annotation_count) : "";
	echo sprintf("%3d annotation(s) %8s", $all_annotation_count, $after);	
}

// Convert a name of a file with annotation to the name of tagged file.
function get_tagged_filename($annotation_file){
	return preg_replace("/(\/annotated\/)(?!.*\1)/", "/tag/", $annotation_file).".tag";
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){
	
	$count_files = 0;
	$count_sentences = 0;
	$count_tokens = 0;	
	$count_annotations = 0;
	
	if ($config->action == "all" || $config->action == "resume" ){
		$f = null;		
		if ( file_exists("progress.txt") && $config->option == "resume" ){
			$progress = explode(",", file_get_contents("progress.txt"));
			$f = fopen($config->output, "a");
		}else{
			$progress = array();
			$f = fopen($config->output, "w");
			if (count($config->ignore)>0)
				fwrite($f, "-DOCSTART CONFIG IGNORE ".implode(", ", $config->ignore)."\n");
			if (count($config->dontignore)>0)
				fwrite($f, "-DOCSTART CONFIG DONTIGNORE ".implode(", ", $config->dontignore)."\n");
		}
		
		$i = count($progress) + 1;
		
		if ($handle = opendir($config->location."/text")){
			while ( false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".."){
					
					if (in_array($file, $progress)) continue;
					$annotation_filename = $config->location . "/annotated/" . $file; 
					list($file_sentences, $file_tokens, $file_annotations) 
						= to_oai($annotation_filename, get_tagged_filename($annotation_filename), $f);
					echo sprintf("%8d\n", $i++);
					$progress[] = $file;
					file_put_contents("progress.txt", implode(",", $progress));					
				}
			}
			// If we reach this point we can remove the progress temp file
			unlink("progress.txt");
		}
		fclose($f);
	// Process a single file
	} else {
		$filename = str_pad($config->action, 7, "0", STR_PAD_LEFT) . ".txt";
		$annotation_filename = $config->location . "/annotated/" . $filename; 
		to_oai($annotation_filename, get_tagged_filename($annotation_filename));
		echo "\n";	
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>
