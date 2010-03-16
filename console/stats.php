<?php
/* 
 * ---
 * Calculate file statistics
 * Run:
 *   php cv10sum.php <filename>
 * ---
 * Created on 2010-02-18
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 

/******************** set configuration   *********************************************/
$config = null;
$config->filename = $argv[1];

/******************** check configuration *********************************************/

if (!$config->filename) 
	die ("Incorrect argument. Expected one of the following formats:\n" .
			"php stats.php <filename>      \n\n");  

if ( !file_exists($config->filename) )
	die("\nFile '{$config->filename}' does not exist!\n\n");

/******************** functions           *********************************************/
// 


/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$count_files = 0;
	$count_sentences = 0;
	$count_tokens = 0;
	$count_annotations = 0;

	$lines = explode("\n", file_get_contents($config->filename));
	for ($i=0; $i<count($lines); $i++){
		$line = trim($lines[$i]);
		
		if (preg_match("/^-DOCSTART( (.*) (.*))?/", $line, $matches)==1){
			if ($matches[2]=="FILE") $count_files++;
			continue;
		}
		
		if ($line == "") {
			if ($i>0 && trim($lines[$i-1])!="") $count_sentences++;
			continue;
		}
		
		if (preg_match("/^.* (O|(I)-.*|(B)-.*)$/", $line, $matches)==1){
			$count_tokens++;
		}else
			die(sprintf("\nMatch error in line %d: %s\n", $i, $line));
		if ($matches[3]=="B")
			$count_annotations++;
	}
	
	echo sprintf("=============== ========\n");
	echo sprintf("**Files**       %8d \n", $count_files);
	echo sprintf("**Sentences**   %8d \n", $count_sentences);
	echo sprintf("**Tokens**      %8d \n", $count_tokens);
	echo sprintf("**Annotations** %8d \n", $count_annotations);
	echo sprintf("=============== ========\n");
} 

/******************** main invoke         *********************************************/
main($config);
 
?>
