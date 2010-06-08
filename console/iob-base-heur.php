<?php
/* 
 * ---
 * Base line with heuristics
 * Wywołanie:
 *   php base1.php <filename>    // <filename> - iob filename
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
			"php tgaz.php <filename>      // IOB filename\n\n");  



/******************** functions           *********************************************/
// 
function is_ucfirst($string) {
	mb_internal_encoding('UTF-8');
	if (preg_match('/^[A-ZĄŻŚŹĘĆŃÓŁa-zążśźęćńół]*$/', $string)>0)
	{
		$zn = mb_substr($string, 0, 1);
		return mb_convert_case($zn, MB_CASE_UPPER, "utf-8") == $zn;  	
	}else
		return false;
	
} 

function handle_sentence(&$summary, &$sentence){
	if (count($sentence)==0) return;

	$summary->count_tokens += count($sentence);
	
	$i = 0;
	while ($i<count($sentence)){
		while ($i<count($sentence) && !$sentence[$i]->is_name) {$i++;}
		if ($i<count($sentence)){
			$n = $i;
			while ($n<count($sentence) && $sentence[$n]->is_name) {$n++;}
			$span = sprintf("[%3s:%3s]", $i, $n);
						
			$is_inside = true;
			for ($j=$i+1; $j<$n; $j++) $is_inside &= $sentence[$j]->type == "I";
						
			$ok = ($sentence[$i]->type == "B" && $is_inside && ($n==count($sentence) || $sentence[$n]->type!="I"));
			
			$annotation = "";
			$is_sa = true;
			for ($j=$i; $j<$n; $j++){ 
				$annotation .= $sentence[$j]->orth . " ";
				//$is_sa = in_array($sentence[$j]->orth, array("SA", "LLC", "Spółka", "AG", "S.A.", "Sp.", "B.V.")) || $is_sa;
			}
			$annotation = trim($annotation);
			
			if ($ok && $is_sa){
				echo "{$span}:{$annotation}\n";
				$summary->tp++;
			}else{
				$summary->fp++;
				if(isset($summary->negatives[$annotation]))
					$summary->negatives[$annotation]++;
				else
					$summary->negatives[$annotation] = 1;
			}
			$i = $n;
		}		
	}
	
	
	$sentence = array();		
}

/******************** main function       *********************************************/
function main ($config){
	
	$count_is_name = 0;
	$sentence = array();
	$summary = null;
	$summary->count = 0;
	$summary->tp = 0;
	$summary->fp = 0;
	$summary->negative = array();
	
	// Load IOB file
	$lines = explode("\n", file_get_contents($config->filename));
	for ($i=0; $i<count($lines); $i++){
		$line = trim($lines[$i]);
		
		if (preg_match("/^-DOCSTART( (.*) (.*))?/", $line, $matches)==1){
			continue;
		}
		
		if ($line == "") {
			handle_sentence($summary, $sentence);	
			continue;
		}
		
		if (preg_match("/^(.*) ((O)|(I)-.*|(B)-.*)$/", $line, $matches)==1){
			$token = null;
			$token->orth = $matches[1];
			$token->type = $matches[3].$matches[4].$matches[5];
			$token->is_name = is_ucfirst($token->orth);
			if ($token->is_name)
				$count_is_name++;
			if ($token->type=="B")
				$summary->count++; 
			$sentence[] = $token;			
		}else
			die(sprintf("\nMatch error in line %d: %s\n", $i, $line));
	}
	handle_sentence($summary, $sentence);	
	
	asort($summary->negatives);
//	foreach ($summary->negatives as $k=>$v){
//		echo sprintf("%3d x '%s'\n", $v, $k);
//	}

	$p = $summary->tp/($summary->tp+$summary->fp)*100;
	$r = $summary->tp/$summary->count*100;
	
	echo sprintf("================== =========\n");
	echo sprintf("**Tokens**         %7d\n", $summary->count_tokens);
	echo sprintf("**Is name**        %7d\n", $count_is_name);
	echo sprintf("**Samples**        %7d\n", $summary->count);
	echo sprintf("**True  Positive** %7d\n", $summary->tp);
	echo sprintf("**False Positive** %7d\n", $summary->fp);
	echo sprintf("**False Negative** %7d\n", $summary->count - $summary->tp);
	echo sprintf("**Precision**      **%4.2f**\n", $p);	
	echo sprintf("**Recall**         **%4.2f**\n", $r);	
	echo sprintf("**F1-measure**     **%4.2f**\n", 2*$p*$r/($p+$r));	
	echo sprintf("================== =========\n");
} 

/******************** main invoke         *********************************************/
main($config);
?>
