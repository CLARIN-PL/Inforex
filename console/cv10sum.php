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
$config->filename = $argv[1];

/******************** check configuration *********************************************/

if (!$config->filename) 
	die ("Incorrect argument. Expected one of the following formats:\n" .
			"php cv10sum.php <filename>      \n\n");  

for ($i=1; $i<11; $i++){
	$file = "{$config->filename}.fold-{$i}.log";
	if ( !file_exists($file) )
		die("\nFile '{$file}' does not exist!\n\n");
}

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
		$file = "{$config->filename}.fold-{$i}.log";
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
	echo sprintf("**Total** **%5.2f** **%5.2f** **%3d** **%3d** **%3d**\n", $stp/($stp+$sfp), $stp/($stp+$sfn), $stp, $sfn, $sfp);
	echo sprintf("========= ========= ========= ======= ======= =======\n");
} 

/******************** main invoke         *********************************************/
main($config); 
?>
