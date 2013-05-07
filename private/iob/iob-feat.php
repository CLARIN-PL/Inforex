<?php
/* 
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 

include("../cliopt.php");

mb_internal_encoding("UTF-8");

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("source", "", ""));
$opt->addArgument(new ClioptArgument("target", "", ""));
//$opt->addExecute("php fetch.php <source_iob> <target_iob>", "");
//$opt->addExecute("php fetch.php --folds <source_location> <target_location>", "");
$opt->addParameter(new ClioptParameter("folds", "f", null, "set divieded into folds"));

/******************** set configuration   *********************************************/
try{
	$opt->parseCli($argv);

	$config = null;
	$config->folds = $opt->exists("folds");
	$config->source = $opt->getArgument(0);
	$config->target = $opt->getArgument(1);
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** check configuration *********************************************/


/******************** functions           *********************************************/

function process_sentence(&$features, &$tokens){
	$n = array_search("class", $features);
	if ( $n === false )
		die ("`class` feature not found");
	
	$eos = array_search("eos", $features);
	if ( $eos === false )
		die ("`class` feature not found");
		
	$last = "null";
	$last2 = "null";

	// Verbs	
	foreach ($tokens as &$token){
		
		$index = count($token)-1;
		
		$token[] = $token[count($token)-1];
		$token[] = $token[count($token)-1];
		
		$token[$index] = $last;
		$token[$index+1] = $last2;
		
		if ($token[$n] == "fin" || $token[$n] == "ger"){
			$last2 = $last;
			$last = $token[1];
		}
	}

//	$start = 0;
//	$region = 0;
//	for ($i=0; $i<count($tokens); $i++){
//		if ( $tokens[$i][$eos] == "1" ){
//			for ( $j=$start; $j<=$i; $j++){
//				$tokens[$j][] = $tokens[$j][count($tokens[$j])-1];
//				$tokens[$j][count($tokens[$j])-2] = $region;
//			}
//			$region = 0;
//			$start = $i+1;
//		}else{
//			if ( $tokens[$i][count($tokens[$i])-1] != "O" )
//				$region = 1;
//		}
//	}	
	
}

function process_file($config, $source_file, $target_file){
	$input = file($source_file);
	
	$output = array();
	
	$features = array();
	$tokens = array();
	
	$i = 0;
	foreach ($input as $line){
		$line = trim($line);
		if ( strpos($line, "-DOCSTART CONFIG") !== false ){
			$features = explode(" ", str_replace("-DOCSTART CONFIG FEATURES ", "", $line));
			$features[] = "prev_verb";
			$features[] = "prev_verb2";
			//$features[] = "region";
			print_r($features);
			$output[] = "-DOCSTART CONFIG FEATURES" . implode(" ", $features);
		}
		else if ( strpos($line, "-DOCSTART FILE") !== false ){
			$output[] = "";
			$output[] = $line;
		}else if ( $line <> "" ){
			//$eos = 0;
			//if ( $i+1 == count($input) || trim($input[$i+1]) == "" )
			//	$eos = 1;
			//$p = strrpos($line, " ");
			//$line = substr($line, 0, $p) ." $eos". substr($line, $p);
			$tokens[] = explode(" ", $line);
		}else{
			if ( count($tokens) ){
				process_sentence($features, $tokens);
				foreach ($tokens as $token){
					$output[] = implode(" ", $token);
				}
				$output[] = "";
				$tokens = array();
			}
		}
		
		$i++;
	}
	
	file_put_contents($target_file, implode("\n", $output));
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){
	if ( $config->folds ){
		for ($i=1; $i<=10; $i++){
			echo "$i, ";
			process_file($config, "{$config->source}.fold-{$i}.train", "{$config->target}.fold-{$i}.train");
			process_file($config, "{$config->source}.fold-{$i}.test", "{$config->target}.fold-{$i}.test");
		}
		
	}
	else{
		process_file($config, $config->source, $config->target);
	}
} 

/******************** main invoke         *********************************************/
main($config);
 
?>
