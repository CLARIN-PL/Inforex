<?php
/* 
 * ---
 * Filter results of HMM. Reads from log files.
 * Wywołanie:
 *   php postfiler.php <filename>   // <filename> -- log file
 *   php postfiler.php <prefix>    	// <prefix> -- experiment prefix
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 

include("../console_lib.php"); 
 
/******************** set configuration   *********************************************/
$config = null;
$config->log_file = $argv[1] == "file" ? $argv[2] : null;
$config->iob_file = $argv[1] != "file" ? $argv[1] : null;

/******************** check configuration *********************************************/

$help = "Incorrect argument. Expected one of the following formats:\n" .
			"php eval.php <prefix>        // experiment prefix\n" .
			"php eval.php <filename>      // log filename\n\n";  

if ( $argv[1] == "help" ) 
	die ($help);  

if ($config->iob_file != null)
	for ($i=1; $i<11; $i++){
		$file = "{$config->iob_file}.fold-{$i}.log";
		if ( !file_exists($file) )
			die("\nFile '{$file}' does not exist!\n\n{$help}");
	}

if ($config->log_file != null && !file_exists($config->log_file))
	die("\nFile '{$config->log_file}' does not exist!\n\n{$help}");


/******************** functions           *********************************************/
// 

function handle_fold($filename, &$summary){
	echo $filename."\n";
	$lines = explode("\n", file_get_contents($filename));
	
	$annotation_types = array();
	
	$i = 0;
	$responses = array();
	for ($i=0; $i<count($lines); $i++){
		$line = trim($lines[$i]);
		if ($line == "@BEGIN" ){
			// Nowe zdanie
			$responses = array();
		}
		
		$matches = array();
		if (preg_match("/^#(.*)?\-?(FalsePositive|FalseNegative|TruePositive) : \[[0-9]*;[0-9]*\] = \[(.*)\]$/", $line, $matches)){
		//if (preg_match("/^(FalsePositive|FalseNegative|TruePositive) : \[[0-9]*;[0-9]*\] = \[(.*)\]$/", $line, $matches)){
			$m = null;
			$m->type = $matches[1] ? $matches[1] : "UNKNOWN";
			$m->category = $matches[2];
			$m->text = $matches[3];  

			if ($m->category == "TruePositive") echo "+";
			elseif ($m->category == "FalsePositive") echo "-";
			elseif ($m->category == "FalseNegative") echo "?";
			$responses[] = $m;
			
		}
		
		if ($line == "@END"){
			
			for ($j=0; $j<count($responses); $j++){
				$m = $responses[$j];
				
				if (!isset($annotation_types[$m->type])){
					$annotation_types[$m->type]->fp = 0;
					$annotation_types[$m->type]->tp = 0;
					$annotation_types[$m->type]->fn = 0;
				}
								
				if ($m->category == "FalsePositive") $annotation_types[$m->type]->fp++;
				elseif ($m->category == "FalseNegative") { $annotation_types[$m->type]->fn++; }
				elseif ($m->category == "TruePositive") $annotation_types[$m->type]->tp++;
				
			}
			$sentence_processed = true;
			$responses = array();
			
			$summary->sentences++;			
		}

	}
	
	foreach ($annotation_types as $k=>$v){
		if (!isset($summary->matrix[$k])){
			$summary->matrix[$k] = $v;
		}else{
			$summary->matrix[$k]->tp += $v->tp;
			$summary->matrix[$k]->fp += $v->fp;
			$summary->matrix[$k]->fn += $v->fn;
		}
	}
	
	print_summary_table($annotation_types);
}

/******************** main function       *********************************************/
function main ($config){

	$count_is_name = 0;
	$sentence = array();
	$summary = null;
	$summary->tp = 0;
	$summary->fp = 0;
	$summary->fn = 0;
	$summary->negative = array();
	$summary->sentences = 0;
	
	// Load log file
	if (file_exists($config->log_file)){
		handle_fold($config->log_file, $summary);	
	}else{		
		$i = 1;
		for ($i=1; $i<=10; $i++){
			echo sprintf("#########################################################\n");
			echo sprintf("# Fold %2d                                               #\n", $i);		
			echo sprintf("#########################################################\n");		
			handle_fold("{$config->iob_file}.fold-{$i}.log", $summary);
		}
	}

	echo sprintf("---------------------------------------------------------\n");
	echo sprintf("Number of sentences: %d\n", $summary->sentences);		
	echo sprintf("---------------------------------------------------------\n");

	if ($config->iob_file){
		echo "\n";				
		echo sprintf("#########################################################\n");
		echo sprintf("# Summary of 10-fold CV                                 #\n");		
		echo sprintf("#########################################################\n");
		print_summary_table($summary->matrix);
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>
