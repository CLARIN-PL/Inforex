<?php
/* 
 * ---
 * Calculate file statistics
 * Run:
 *   php iob-continous.php <filename> <filename>
 *   php iob-continous.php 10cv <location>
 * ---
 * Created on 2010-02-18
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 

include("../cliopt.php");

mb_internal_encoding("UTF-8");

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("source", "", ""));
$opt->addArgument(new ClioptArgument("target", "", ""));
$opt->addExecute("php fetch.php <source_iob> <target_iob>", "");
$opt->addExecute("php fetch.php --folds <source_location> <target_location>", "");
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

function process_file($config, $source_file, $target_file){
	$input = file($source_file);
	
	$output = array();
	
	$i = 0;
	foreach ($input as $line){
		$line = trim($line);
		if ( strpos($line, "-DOCSTART CONFIG") !== false ){
			$output[] = $line . " eos";
		}
		else if ( strpos($line, "-DOCSTART FILE") !== false ){
			$output[] = "";
			$output[] = $line;
		}else if ( $line <> "" ){
			$eos = "INS";
			if ( $i+1 == count($input) || trim($input[$i+1]) == "" )
				$eos = "EOS";
			$p = strrpos($line, " ");
			$line = substr($line, 0, $p) ." $eos". substr($line, $p);
			$output[] = $line;
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
