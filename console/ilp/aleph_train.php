<?php
/**
 * Skrypt do transformacji korpusu w formacie CCL to bazy wiedzy na potrzeby ILP.
 * Michał Marcińczuk <marcinczuk@gmail.com>
 * październik 2011
 */
mb_internal_encoding("UTF-8");

include("../cliopt.php");
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
ob_end_clean();

if ( isset($argv[1]) && $argv[1]=="all")
	$relations = array('origin', 'nationality', 'location', 'affiliation', 'creator', 'composition', 'neighbourhood', 'alias');
else
	$relations = array('nationality');

$c = 1;
foreach ($relations as $r){
	echo "**************************************\n";
	echo sprintf("* %d z %d -- %s\n", $c++, count($relations), $r);
	echo "**************************************\n";
	train("logic4/train", $r);
}

/**
 * 
 */
function train($target, $relation){
		
	$background = "$target/background.txt";
	$header = "$target/aleph_header.txt";
	$positives = "$target/relation_$relation.f";
	$negatives = "$target/relation_$relation.n";
	
	$files_required = array($background, $positives, $header, $negatives);	
	foreach ($files_required as $file)
		if( !file_exists($file) )
			die("! File not found '$file' !");
	
	$script = "tmp_script.txt";
	$log = "$target/relation_{$relation}.log";
	$rules = "$target/relation_{$relation}_rules.txt";
	$background_tmp = "$target/relation_$relation.b";
	
	$f = fopen($background_tmp, "w");
	fwrite($f, file_get_contents($header));
	fwrite($f, "\n\n");
	fwrite($f, file_get_contents($background));
	fclose($f);
	
	$commands = array();
	$commands[] = "['aleph'].";
	$commands[] = "read_all('$target/relation_$relation').";
	$commands[] = "induce_max.";
	$commands[] = "write_rules('$rules').";

	file_put_contents($script, implode("\n", $commands));

	shell_exec("yap < $script | tee $log");	
}


?>