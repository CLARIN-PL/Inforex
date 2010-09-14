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

function handle_annotation(&$annotation, $type, &$summary){
	if (count($annotation)>0){
		$text = implode(" ", $annotation);
		if (!isset($summary[$type]))
			$summary[$type] = array();
		if (!isset($summary[$type][$text]))
			$summary[$type][$text] = 1;
		else
			$summary[$type][$text]++;
		$annotation = array();
	}
} 


/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$count_files = 0;
	$count_sentences = 0;
	$count_tokens = 0;
	$count_annotations = 0;
	$types = array();
	$options = array();
	$options_config = array();
	$summary = array();
	$annotation = array();
	$last_annotation_type = "";

	$lines = explode("\n", file_get_contents($config->filename));
	for ($i=0; $i<count($lines); $i++){
		$line = trim($lines[$i]);
		
		if (preg_match("/^-DOCSTART( (.*?) (.*))?/", $line, $matches)==1){
			if ($matches[2]=="FILE"){
				$count_files++;
				echo ".";
			}elseif ($matches[2]=="CONFIG"){
				$options_config[] = $matches[3]; 
			}else{
				$options[$matches[2]] = $matches[3]; 				
			} 
			continue;
		}
		
		if ($line == "") {
			if ($i>0 && trim($lines[$i-1])!="") $count_sentences++;
			continue;
		}
		
		if (preg_match("/^(.*) (O|([IB])-(.*))$/", $line, $matches)==1){
			$count_tokens++;
		}else
			die(sprintf("\nMatch error in line %d: %s\n", $i, $line));
		
		$text = $matches[1];
		$state = count($matches)>3 ? $matches[3] : null;
		$type = count($matches)>4 ? $matches[4] : null;
		
		if ($state=="B"){
			handle_annotation($annotation, $last_annotation_type, $summary);
			
			$last_annotation_type = $type;
			$annotation = array($text);
			$count_annotations++;
			if (!isset($types[$type]))
				$types[$type] = 1;
			else
				$types[$type]++;
		}elseif($state=="I"){
			$annotation[] = $text;
		}else{
			handle_annotation($annotation, $last_annotation_type, $summary);			
		}
	}
	
	print_r($summary);
	
	$patterns = array();
	$patterns['/^[^ ]+$/'] = 0;
	$patterns['/^.( .)? .+$/'] = 0;
	$patterns['/^.+ .+( )?-( )?.+$/'] = 0;
	$patterns['/^.+ .+ .+$/'] = 0;
	$patterns['/^.+ .+$/'] = 0;
	$patterns['other'] = 0;
	foreach ($summary['PERSON'] as $k=>$v){
		$matched = false;
		foreach ($patterns as $p=>$c){
			if ($p != "other" && preg_match($p, $k)){
				$matched = true;
				$patterns[$p]+=$v;
				break;
			}
		}
		if (!$matched){
			$patterns['other']++;
		}
	}
	
	print_r($patterns);
	
	echo sprintf("\n");
	echo sprintf("# Statistics for {$config->filename}:\n");
	echo sprintf("=============== ========\n");
	echo sprintf("**Files**       %8d \n", $count_files);
	echo sprintf("**Sentences**   %8d \n", $count_sentences);
	echo sprintf("**Tokens**      %8d \n", $count_tokens);
	echo sprintf("**Annotations** %8d \n", $count_annotations);
	echo sprintf("=============== ========\n");
	echo sprintf("# Annotations:\n");	
	echo sprintf("--------------- --------\n");
	foreach ($types as $k=>$v){
		echo sprintf("%-15s %8d\n", $k, $v);
		echo sprintf("- %-13s %8d\n", "unique", count(array_keys($summary[$k])));
		$c = 0; foreach ($summary[$k] as $text=>$count) if ($count==1) $c++;
		echo sprintf("- %-13s %8d\n", "single", $c);
		echo sprintf("- %-13s %8.2f\n", "% of single", $c/$v*100);
	}
	echo sprintf("=============== ========\n");
	echo sprintf("# Config:\n");	
	echo sprintf("--------------- --------\n");
	foreach ($options_config as $v){
		echo sprintf("* %s\n", $v); 
	}
	echo sprintf("=============== ========\n");
	echo sprintf("# Options:\n");	
	echo sprintf("--------------- --------\n");
	if (count($options)==0) echo "none\n";
	foreach ($options as $k=>$v){
		echo sprintf("* %-15s: %s\n", $k, $v); 
	}
	echo sprintf("=============== ========\n");
} 

/******************** main invoke         *********************************************/
main($config);
 
?>
