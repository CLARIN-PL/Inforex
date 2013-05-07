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
 
 
/******************** set configuration   *********************************************/
$config = null;
$config->filename = $argv[1];

/******************** check configuration *********************************************/

if (!$config->filename) 
	die ("Incorrect argument. Expected one of the following formats:\n" .
			"php postfilter.php <prefix>        // experiment prefix\n" .
			"php postfilter.php <filename>      // log filename\n\n");  



/******************** functions           *********************************************/
// 
function my_ucfirst($string, $e ='utf-8') {
    if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
        $string = mb_strtolower($string, $e);
        $upper = mb_strtoupper($string, $e);
            preg_match('#(.)#us', $upper, $matches);
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e);
    }
    else {
        $string = ucfirst($string);
    }
    return $string;
} 

function handle_name($sequence, &$summary){
	if (count($sequence)>0){
		foreach ($sequence as $v)
			echo "{$v[1]}:$v[0] ";
		echo "\n";
		$summary->lengths[count($sequence)][] = $sequence;
	}
}

function handle_fold($filename, &$summary){
	$lines = explode("\n", file_get_contents($filename));
	
	$tp = 0;
	$fp = 0;
	$fn = 0;
	$tpr = 0;
	$fpr = 0;
	//$
	
	$i = 0;
	$responses = array();
	for ($i=0; $i<count($lines); $i++){
		$line = trim($lines[$i]);
		if ($line == "@BEGIN" ){
			// Nowe zdanie
			$responses = array();
		}
		
		$matches = array();
		if (preg_match("/^#(FalsePositive|FalseNegative|TruePositive) : \[[0-9]*;[0-9]*\] = \[(.*)\]$/", $line, $matches)){
			if ($matches[1] == "FalseNegative"){
				//echo ">".$line."\n";
				//print_r($matches);
				echo ".";					
			}
			$responses[] = $matches;	
		}
		
		if ($line == "@END"){
			for ($j=0; $j<count($responses); $j++){
				$matches = $responses[$j];
				if ($matches[1] == "FalsePositive") $fp++;
				elseif ($matches[1] == "FalseNegative") { $fn++; }
				elseif ($matches[1] == "TruePositive") $tp++;
				
				if ($matches[1] == "TruePositive" || $matches[1] == "FalsePositive"){
					$ucfwords = 0;
					$uc = "([A-Z]|Ą|Ż|Ś|Ź|Ę|Ć|Ń|Ó|Ł)([a-z]|ą|ż|ś|ź|ę|ć|ń|ó|ł)*";
					$ucfwords = preg_match("/^$uc( $uc)*(( - |-)$uc)?( \($uc\))?$/", $matches[2]);
					if ($ucfwords){
						//if ($matches[1] == "FalsePositive")
						//	echo $matches[0]." - nie odrzucone\n";
					}else{
						if ($matches[1] == "TruePositive"){
							$tpr++;
							//echo $matches[0]." - odrzucone\n";
						}else{
							$fpr++;
						}
					}			
				}
			}
			$sentence_processed = true;
			$responses = array();			
		}

	}
	
	$summary->tp += $tp;
	$summary->fp += $fp;
	$summary->fn += $fn;
	$summary->tpr += $tpr;
	$summary->fpr += $fpr;

	echo sprintf("================== =========\n");
	echo sprintf("**Samples**        %5d\n", $tp+$fn);
	echo sprintf("**True  Positive** %5d\n", $tp);
	echo sprintf("**False Positive** %5d\n", $fp);
	echo sprintf("**False Negative** %5d\n", $fn);
	echo sprintf("**Precision**      **%4.2f**\n", $tp/($tp+$fp));	
	echo sprintf("**Recall**         **%4.2f**\n", $tp/($tp+$fn));	
	echo sprintf("================== =========\n");
	
	$tp -= $tpr;
	$fp -= $fpr;
	echo sprintf(" Po redukcji\n");
	echo sprintf("================== =========\n");
	echo sprintf("**Samples**        %5d\n", $tp+$fn);
	echo sprintf("**True  Positive** %5d\n", $tp);
	echo sprintf("**False Positive** %5d\n", $fp);
	echo sprintf("**False Negative** %5d\n", $fn);
	echo sprintf("**Precision**      **%4.2f**\n", $tp/($tp+$fp));	
	echo sprintf("**Recall**         **%4.2f**\n", $tp/($tp+$fn));	
	echo sprintf("================== =========\n");
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
	
	// Load log file
	if (file_exists($config->filename)){
		handle_fold($config->filename, $summary);	
	}else{		
		$i = 1;
		for ($i=1; $i<=10; $i++){
			echo sprintf("#########################################################\n");
			echo sprintf("# Fold %2d                                               #\n", $i);		
			echo sprintf("#########################################################\n");		
			handle_fold("{$config->filename}.fold-{$i}.log", $summary);
		}
	}

	echo sprintf("#########################################################\n");
	echo sprintf("# Summary of 10-fold CV                                 #\n");		
	echo sprintf("#########################################################\n");		
	echo sprintf("== Base results ==\n");
	echo sprintf("================== =========\n");
	echo sprintf("**Samples**        %5d\n", $summary->tp+$summary->fn);
	echo sprintf("**True  Positive** %5d\n", $summary->tp);
	echo sprintf("**False Positive** %5d\n", $summary->fp);
	echo sprintf("**False Negative** %5d\n", $summary->fn);
	echo sprintf("**Precision**      **%4.2f**\n", $summary->tp/($summary->tp+$summary->fp));	
	echo sprintf("**Recall**         **%4.2f**\n", $summary->tp/($summary->tp+$summary->fn));	
	echo sprintf("================== =========\n");
	
	$summary->tp -= $summary->tpr;
	$summary->fp -= $summary->fpr;
	echo sprintf("== Results after filtering ==\n");
	echo sprintf("================== =========\n");
	echo sprintf("**Samples**        %5d\n", $summary->tp+$summary->fn);
	echo sprintf("**True  Positive** %5d\n", $summary->tp);
	echo sprintf("**False Positive** %5d\n", $summary->fp);
	echo sprintf("**False Negative** %5d\n", $summary->fn);
	echo sprintf("**Precision**      **%4.2f**\n", $summary->tp/($summary->tp+$summary->fp));	
	echo sprintf("**Recall**         **%4.2f**\n", $summary->tp/($summary->tp+$summary->fn));	
	echo sprintf("================== =========\n");
} 

/******************** main invoke         *********************************************/
main($config);
?>
