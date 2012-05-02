<?php

main($argv[1], "train");
main($argv[1], "tune");
main($argv[1], "test");

function main($foldername, $type="test"){

	echo "# Evaluation $foldername:$type\n\n";

	$relations = array('origin', 'nationality', 'location', 'affiliation', 'creator', 'composition', 'neighbourhood', 'alias');
	//$relations = array('origin');
	
	$evaluations = array();
	
	foreach ($relations as $r){
		$rules = "$foldername/{$r}_train/rules.txt";
		$background = "$foldername/{$r}_$type/background.txt";
		$examples = "$foldername/{$r}_$type/aleph";
		//$rules = "$foldername/train/relation_{$r}_rules.txt";
		//$background = "$foldername/$type/background.txt";
		//$examples = "$foldername/$type/relation_{$r}";
		if ( file_exists($rules) && file_exists($background) ){
			echo sprintf("REL %15s OK\n", $r);
			$evaluations[$r] = evaluate($rules, $background, $examples);
		}else{
			echo sprintf("REL %15s SKIPPED\n", $r);
		}
	}

	$row = "%15s %4d %4d %4d  %5s %5s %5s\n";
	echo "\n";
	echo " Relacja          TP   FP   FN    P     R     F\n";
	echo "-------------------------------------------------\n";
	foreach ($evaluations as $r=>$v){
		echo sprintf($row,	$r, $v['tp'], $v['fp'], $v['fn'], $v['p'], $v['r'], $v['f']);
	}
	echo "\n";

}


function evaluate($rules, $background, $examples){

	$classification_positive = test_rules($rules, $background, $examples.".f"); 
	$classification_negative = test_rules($rules, $background, $examples.".n"); 

	$tp = $classification_positive["yes"];
	$fn = $classification_positive["no"];
	$fp = $classification_negative["yes"];

	$p = $tp+$fp == 0 ? 0 : $tp*100/($tp+$fp);
	$r = $tp+$fn == 0 ? 0 : $tp*100/($tp+$fn);
	$f = $p + $r == 0 ? 0 : 2 * ($p * $r) / ($p + $r); 

	$p =  number_format( $p, 2, ".", "");
	$r = number_format( $r, 2, ".", "");
	$f = number_format( $f, 2, ".", "");

	return array("tp"=>$tp, "fn"=>$fn, "fp"=>$fp, "p"=>$p, "r"=>$r, "f"=>$f);
}

function test_rules($filename_rules, $filename_background, $filename_examples){
	
	//echo $filename_rules . "\n";
	//echo $filename_background . "\n";
	//echo $filename_examples . "\n";

	$file = "here_" . tmpfile();

	$descriptorspec = array(
	   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
	   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
	   2 => array("file", $file, "w") // stderr is a file to write to
	);
	
	$cwd = '.';
	$env = array();
	$pipes = array();

	$process = proc_open('yap', $descriptorspec, $pipes, $cwd, $env);
	$examples = file("$filename_examples");
	
	if (is_resource($process)) {
	    fwrite($pipes[0], "['$filename_rules'].\n");
	    fwrite($pipes[0], "['$filename_background'].\n");
	    
	    foreach ($examples as $e){
	    	fwrite($pipes[0], $e );
	    	flush($pipes[0]);
	    }
	    fclose($pipes[0]);    
	    fclose($pipes[1]);
	
	    $return_value = proc_close($process);
	
		$classification = read_classifications($file);
		unlink($file);
	}
	
	return $classification;
}


function read_classifications($filename="here.txt"){
	$lines = file($filename);
	
	//print_r($lines);
	
	$i = 0;
	$skip = 2;
	while ( $skip > 0 && $i < count($lines) ){
		if ( trim($lines[$i]) == "yes" ) $skip--;
		$i++;
	}
	
	$classification = array( "yes"=>0, "no"=>0);
	
	$n = 0;
	while ( $i < count($lines)){
		$value = trim($lines[$i]);
		$classification[$value]++;		
		$i++;
	}
	
	return $classification;
}

?>
