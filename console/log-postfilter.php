<?php
/* 
 * ---
 * Filter results of HMM. Reads from log files.
 * Dokumentacja -- wywołaj php iob-postfiler.php help
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 

include ("console_lib.php"); 
 
/******************** set configuration   *********************************************/
$config = null;
$config->log_file = $argv[1] == "file" ? $argv[2] : null;
$config->iob_file = $argv[1] != "file" ? $argv[1] : null;
$config->filter = array('upper'=>1,'freq'=>1, 'trim'=>1);

/******************** check configuration *********************************************/

$help = "Incorrect argument.\n" .
		"\n" .
		"Expected one of the following formats:\n" .
		" php iob-postfilter.php file <log_file>   \n" .
		" php iob-postfilter.php <iob_file>        \n" .
		"\n" .
		"Options:\n" .
		"  --filter <filters>                      // list of filter\n" .
		"\n" .
		"<filters> = upper                         // upper case filter\n" .
		"            freq                          // frequency filter\n" .    
		"\n";  

if ( $argv[1] == "help" or count($argv)==1) 
	die ($help);  

if ($config->iob_file != null)
	for ($i=1; $i<11; $i++){
		$file = "{$config->iob_file}.fold-{$i}.log";
		if ( !file_exists($file) )
			die("\nFile '{$file}' does not exist!\n\n{$help}");
	}

if ($config->log_file != null && !file_exists($config->log_file))
	die("\nFile '{$config->log_file}' does not exist!\n\n{$help}");

/**
 * Filters
 */
if ( ($p = array_search("--filter", $argv)) !==false && $p+1 < count($argv) ){
	$config->filter = array();
	foreach (explode(",", $argv[$p+1]) as $f)
		$config->filter[$f] = 1;
}

/******************** functions           *********************************************/
 
/**
 * 
 */
function iob_get_words_freq($filename){
	$lines = explode("\n", file_get_contents($filename));
	for ($i=0; $i<count($lines); $i++){
		$line = trim($lines[$i]);
		
		if ($line == "") {
			continue;
		}
		
		if (preg_match("/^(.*) (O|([IB])-(.*))$/", $line, $matches)==1){
			$text = token_norm($matches[1]);
			$state = isset($matches[3]) ? $matches[3] : "";
			$type = isset($matches[4]) ? $matches[4] : "";

		  	if ( isset($freq[$text]))
		  		$freq[$text]++;
		  	else
		  		$freq[$text] = 1;		
		}
	}		
	return $freq;
} 
 
/**
 * 
 */
function token_norm($text){
	//return mb_strtolower($text); 
	return $text;
}

/**
 * 
 * @param $freq - częstotliwość słów w testowanym dokumencie
 * @param $freq_ann - częstotliwość słów oznaczonych jako anotacje z podziałem na typy
 */
function post_processing($config, $responses, $freq, $freq_ann){
	$new_responses = array();
	
	foreach ($responses as $m){
		$cs = "(?:[a-z]|ą|ż|ś|ź|ę|ć|ń|ó|ł)";
		$cu	= "(?:[A-Z]|Ą|Ż|Ś|Ź|Ę|Ć|Ń|Ó|Ł)";
		$uc = "([A-Z]|Ą|Ż|Ś|Ź|Ę|Ć|Ń|Ó|Ł)([a-z]|ą|ż|ś|ź|ę|ć|ń|ó|ł)*";

		if ( isset($config->filter['trim'])){
			$matches = array();
			if (preg_match("/^(?P<pre>(:?$cs* )*)(?P<ne>(?:$cu.*?)(?: .+)*?)?(?P<pos>(?: $cs+)*)$/", $m->text, $matches)){
				if (trim($m->text) != trim($matches['ne'])){					
					echo "\n".$m->text ." --> ".$matches['ne']."\n";
					$m->from += strlen($matches['pre']);
					$m->to -= strlen($matches['pos']);
					$m->text = $matches['ne'];
				}						
			}				
		}

		if ( isset($config->filter['upper']) ){
			$ucfwords = preg_match("/^$uc( $uc)*(( - |-)$uc)?( \($uc\))?$/", $m->text);
			if (!$ucfwords) continue;		
		}
		
		if ( isset($config->filter['freq'])) {
			$t = token_norm($m->text);
			if ($freq_ann[$m->type][$t] != $freq[$t]) continue;
			echo "{$m->type} $t : {$freq_ann[$m->type][$t]} z {$freq[$t]}\n";	
		}

		// Jak dotartło do tego miejsca do przeszło przez wszystkie filtry	
		$new_responses[] = $m;
	}
	return $new_responses;
} 

/**
 * 
 */
function evaluate($references, $responses){
	$ref_txt = array();
	$res_txt = array();
	$types = array();
	foreach ($references as $m){
		$ref_txt[$m->type][] = sprintf("[%d;%d] %s", $m->from, $m->to, trim($m->text));
		$types[$m->type] = 1;
	}
	foreach ($responses as $m){
		$res_txt[$m->type][] = sprintf("[%d;%d] %s", $m->from, $m->to, trim($m->text));
		$types[$m->type] = 1;
	}
	$summary = array();
	foreach ($types as $type=>$k){
		if (!isset($res_txt[$type])) $res_txt[$type] = array();
		if (!isset($ref_txt[$type])) $ref_txt[$type] = array();
		$summary[$type] = null;	
		$summary[$type]->tp = 0;
		$summary[$type]->fn = 0;
		foreach ($ref_txt[$type] as $r)
			if (in_array($r, $res_txt[$type])){
				$summary[$type]->tp++;
				$res_txt[$type] = array_diff($res_txt[$type], array($r));
			}else
				$summary[$type]->fn++;
		$summary[$type]->fp = count($res_txt[$type]);
		
	}
	return $summary; 
}
 
/**
 * 
 */
function handle_name($sequence, &$summary){
	if (count($sequence)>0){
		foreach ($sequence as $v)
			echo "{$v[1]}:$v[0] ";
		echo "\n";
		$summary->lengths[count($sequence)][] = $sequence;
	}
}

/**
 * 
 */
function handle_fold($config, $filename, &$summary){
	// Policz częstotliwość występowania słów w zbiorze testowym
	$lines = explode("\n", file_get_contents($filename));
	$freq = isset($config->filter['freq']) ? iob_get_words_freq( str_replace(".log", ".test", $filename)) : null;
	
	$annotation_types = $annotation_types_post = array();
	$references = $responses = array();

	// Anotacje referencyjne i rozpoznane w poszczególnych zdaniach
	$sentence_annotations = array();

	for ($i=0; $i<count($lines); $i++){
		$line = trim($lines[$i]);
		
		if ($line == "@BEGIN" ){
			echo ".";
			$responses = $references = array();
		}
		
		$matches = array();
		if (preg_match("/^#?(.*)?\-?(FalsePositive|FalseNegative|TruePositive) : \[([0-9]*)[;,]([0-9]*)\] = \[(.*)\]$/", $line, $matches)){
			$m = null;
			$m->type = $matches[1] ? $matches[1] : "UNKNOWN";
			$m->category = $matches[2];
			$m->from = $matches[3];
			$m->to = $matches[4];
			$m->text = $matches[5];

			if ($m->category == "TruePositive"){
				$references[] = $m;
				$responses[] = $m;
				echo "+";
			}
			elseif ($m->category == "FalsePositive"){
				$responses[] = $m;
				echo "-";
			}
			elseif ($m->category == "FalseNegative"){
				$references[] = $m;
				echo "?";
			}			
		}

		if ($line == "@END"){
			$sentence_annotations[] = array($references, $responses);
			$responses = $references = array();
		}
	}
	
	// Liczenie częstości elementów oznaczonych jako anotacje
	$freq_ann = array();
	foreach ($sentence_annotations as $references_responses)
		foreach ($references_responses[1] as $r){
			$text = token_norm($r->text);
			if (isset($freq_ann[$r->type][$text]))
				$freq_ann[$r->type][$text]++;
			else
				$freq_ann[$r->type][$text] = 1;
		}
	
	// Analiza poszczególnych zdań
	for ($i=0; $i<count($sentence_annotations); $i++){
		$references = $sentence_annotations[$i][0];
		$responses =  $sentence_annotations[$i][1];
		
		$eval = evaluate($references, $responses);
		
		foreach ($eval as $type=>$v){
			if (isset($annotation_types[$type])){
				$annotation_types[$type]->tp += $v->tp;
				$annotation_types[$type]->fp += $v->fp;
				$annotation_types[$type]->fn += $v->fn;
			}else
				$annotation_types[$type] = $v;
		}			

		$responses = post_processing($config, $responses, $freq, $freq_ann);
		
		foreach ($responses as $m){
			if (isset($summary->passed[$m->text]))
				$summary->passed[$m->text]++;
			else
				$summary->passed[$m->text] = 1;
		}			

		$eval = evaluate($references, $responses);
		foreach ($eval as $type=>$v){
			if (isset($annotation_types_post[$type])){
				$annotation_types_post[$type]->tp += $v->tp;
				$annotation_types_post[$type]->fp += $v->fp;
				$annotation_types_post[$type]->fn += $v->fn;
			}else
				$annotation_types_post[$type] = $v;
		}			
	}
	
	// Podsumowanie
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

	echo sprintf(" Po redukcji\n");
	foreach ($annotation_types_post as $k=>$v){
		if (!isset($summary->matrix[$k])){
			$summary->matrix_post[$k] = $v;
		}else{
			$summary->matrix_post[$k]->tp += $v->tp;
			$summary->matrix_post[$k]->fp += $v->fp;
			$summary->matrix_post[$k]->fn += $v->fn;
		}
	}	
	print_summary_table($annotation_types_post);
}

/******************** main function       *********************************************/
function main ($config){

	$summary = null;
	$summary->tp = 0;
	$summary->fp = 0;
	$summary->fn = 0;
	$summary->negative = array();
	
	// Load log file
	if ($config->log_file){
		handle_fold($config, $config->log_file, $summary);	
	}else{		
		$i = 1;
		for ($i=1; $i<=10; $i++){
			echo sprintf("#########################################################\n");
			echo sprintf("# Fold %2d                                               #\n", $i);		
			echo sprintf("#########################################################\n");		
			handle_fold($config, "{$config->iob_file}.fold-{$i}.log", $summary);
		}
	}

	if ($config->iob_file){		
		echo sprintf("#########################################################\n");
		echo sprintf("# Summary of 10-fold CV                                 #\n");		
		echo sprintf("#########################################################\n");
		print_summary_table($summary->matrix);		
		
		echo sprintf("\n================= after filtering ======================\n");		
		print_summary_table($summary->matrix_post);
	}
		
} 

/******************** main invoke         *********************************************/
main($config);

?>


