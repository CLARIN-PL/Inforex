<?php
/* 
 * ---
 * Summary of the cross validation.
 * Run:
 *   php cv10sum.php <filename>
 * ---
 * Created on 2010-02-18
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
 /******************** set configuration   *********************************************/
$config = null;
$config->log_file = $argv[1] == "file" ? $argv[2] : null;
$config->iob_file = $argv[1] != "file" ? $argv[1] : null;

/******************** check configuration *********************************************/

$help = "Incorrect argument. Expected one of the following formats:\n" .
			"php cv10sum.php file <log_filename> \n" .
			"php cv10sum.php <iob_filename>      \n\n";

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

function get_value($content, $regex){
	if (preg_match($regex, $content, $matches)==1){
		return $matches[1];
	}else{
		die("\nMore than 1 statement found: {$regex}!\n");
	}	
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){
	$precision = array();
	$recall = array();
	$tp = array();
	$tn = array();
	$tp = array();
	$stp = 0;
	$sfp = 0;
	$sfn = 0;
	for ($i=1; $i<11; $i++){
		$file = "{$config->iob_file}.fold-{$i}.log";
		$content = file_get_contents($file);
		$precision[] = get_value($content, "/Precision=([0-9]{1,3}.[0-9]+)/");
		$recall[] = get_value($content, "/Recall=([0-9]{1,3}.[0-9]+)/");
		$tp[] = get_value($content, "/True Positive=([0-9]+)/");
		$fn[] = get_value($content, "/False Negative=([0-9]+)/");
		$fp[] = get_value($content, "/False Positive=([0-9]+)/");
		$stp += $tp[$i-1];
		$sfp += $fp[$i-1];
		$sfn += $fn[$i-1];
	}	
	
	
			
	echo sprintf("========= ========= ========= ======= ======= =======\n");
	echo sprintf("Fold nr   P         R         TP      FN      FP \n");
	echo sprintf("========= ========= ========= ======= ======= =======\n");
	for ($i=0; $i<10; $i++)
	echo sprintf("Fold %2d  %5.2f     %5.2f     %3d     %3d     %3d\n", $i+1, $precision[$i], $recall[$i], $tp[$i], $fn[$i], $fp[$i]);
	echo sprintf("--------- --------- --------- ------- ------- -------\n");
	echo sprintf("**Total** **%5.2f** **%5.2f** **%3d** **%3d** **%3d**\n", $stp/($stp+$sfp)*100, $stp/($stp+$sfn)*100, $stp, $sfn, $sfp);
	echo sprintf("========= ========= ========= ======= ======= =======\n");
} 

/******************** main invoke         *********************************************/
main($config); 
?>
