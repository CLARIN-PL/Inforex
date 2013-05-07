<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-02-15
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

include("../cliopt.php");
 
/******************** set configuration   *********************************************/
$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("iob", "path to an IOB file"));
$opt->addParameter(new ClioptParameter("base", "b", "iob", "path to a base split of another IOB"));
$opt->addParameter(new ClioptParameter("documents", "d", null, "keep whole documents between folds"));

/******************** parse cli *********************************************/
$config = null;
$config->folds = 5;
$config->keepDocuments = true;
 
try{
	$opt->parseCli($argv);
	$config->base = $opt->getOptional('base', false);
	$config->input = $opt->getArgument();
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** misc function       *********************************************/

function save_fold($config, &$folds, $fold_num, $docstart){
	$lines = implode("\n", $docstart)."\n";
	for ($i=0; $i<count($folds); $i++)
		if ($i+1 != $fold_num)
			$lines .= implode("\n", $folds[$i]) . "\n\n";
	file_put_contents($config->input . ".fold-{$fold_num}.train", $lines);
	file_put_contents($config->input . ".fold-{$fold_num}.test", implode("\n", $docstart)."\n".implode("\n", $folds[$fold_num-1]));	
}

function split_random($config, $lines){
	$count = count($lines);
	$docstart = array();
	$count_train_set = 0;
	$count_in_sentence = 0;
	$examples_in_fold = $count/$config->folds;
		
	// Split into folds
	$folds = array();
	for ($i=0; $i<$config->folds; $i++)
		$folds[$i] = array();
		
	$i = 0;
	$fold_num = 0;
	$count_total = 0;
	echo "======== =========== =========\n";
	echo "Fold     Annotations Sentences\n";
	echo "======== =========== =========\n";
	
	while (mb_substr($lines[$i], 0, 16) == "-DOCSTART CONFIG" && $i < count($lines)){
		$docstart[] = $lines[$i];
		$i++;
	}

	print_r($docstart);
	
	$sentences = index_sentences($lines);

	if ( $config->keepDocuments){
		$d = 0;
		foreach ($sentences as $name=>$document){
			echo ".";
			$num = floor($d++/$config->folds);
			$folds[$num][] = "-DOCSTART FILE $name";
			foreach ($document as $sentence){
				$folds[$num] = array_merge($folds[$num], $sentence);
				$folds[$num][] = "";
			}
		}
	}
	else{
		while ($i<count($lines)){
			$this_fold = 0;
			while ($i<count($lines) && !($count_total >=$count*($fold_num+1)/$config->folds && trim($lines[$i])=='') ){
				if (strpos($lines[$i], " B-")!==false){
					$this_fold++;
					$count_total++;
				}
				$folds[$fold_num][] = $lines[$i];
				$i++;
			}
			// Skip an empty line.
			$i++;		
			echo sprintf("Fold %2d %3d            %6d \n", $fold_num+1, $this_fold, count($folds[$fold_num]));
			$fold_num++;
			if ( $fold_num == $config->folds ){
				while($i<count($lines))
					$folds[$fold_num-1][] = $lines[$i++];
			}
		}
		echo "======== =========== =========\n";
	}

	for ($n=1; $n<=$config->folds; $n++){
		echo " Saving fold $n\n";
		save_fold($config, $folds, $n, $docstart);
	}	
}

/******************** misc function       *********************************************/
function split_base($config, $lines){
	// Load headers
	$docstarts = array();
	$filename_mapping = array();
	$i=0;
	foreach ($lines as $line){
		if (mb_substr($line, 0, 16) == "-DOCSTART CONFIG" )
			$docstarts[] = $line;
		else if (mb_substr($line, 0, 14) == "-DOCSTART FILE" ){
			$file = trim(mb_substr($line, 15));
			$filename_mapping[basename($file)] = $file;
		}
	}
	
	$base = load_base_order($config->base);	
	$sentences = index_sentences($lines);

	print_r($base);

	// Split sentences according to base
	$moved_to_next = array();  // lines left from previous fold
	$folds = array();
	for ($i=1; $i<=$config->folds; $i++){
		$count = count($base[$i]);		
		// Copy the sentences left from previous fold
		$fold = $moved_to_next;
		
		// Go through complete sentences
		for ($n=0; $n<$count-1; $n++){
			$docname = $base[$i][$n][0];
			$fold[] = "-DOCSTART FILE ".$filename_mapping[$docname];
			if ( !is_array($sentences[$docname]) )
				throw new Exception("'$docname' not found");
			foreach ($sentences[$docname] as $sentence){
				foreach ($sentence as $line)
					$fold[] = $line;
				$fold[] = "";
			}
		}	
		// The last one might be incomplete
		list($docname, $countSentence) = $base[$i][$n];
		$fold[] = "-DOCSTART FILE ".$filename_mapping[$docname];
		for ($j=0; $j<$countSentence; $j++){
//			if (!is_array($sentences[$docname][$j]))
//				throw new Exception("'$docname' not found");
			foreach ($sentences[$docname][$j] as $line)
				$fold[] = $line;
			$fold[] = "";
		}
		
		// Copy remaining sentences to the next fold
		$countSentence = count($sentences[$docname]);
		$moved_to_next = array();
		for (; $j<$countSentence; $j++){
			foreach ($sentences[$docname][$j] as $line)
				$moved_to_next[] = $line;
			$moved_to_next[] = "";
		}
		
		$folds[] = $fold;
	}
	
	for ($n=1; $n<=$config->folds; $n++){
		echo " Saving fold $n\n";
		save_fold($config, $folds, $n, $docstarts);
	}			
}

/**
 * Load document order and sentence split from another 10-fold IOB.
 */
function load_base_order($iob, $config){
	$docstart = null;
	$sentences = 0;
	$folds = array();	
	$prev_empty_line = false;
	for ($i=1; $i<=$config->folds; $i++){
		$file = "$iob.fold-$i.test";
		$lines = file($file);
		$lines[] = "";
		foreach ($lines as $line){
			if (preg_match("/-DOCSTART FILE (.*)/", $line, $match)){
				if ( $docstart != null ){
					echo $docstart . "\n";
					$folds[$i][] = array($docstart);
					$sentences = 0;
				}
				$docstart = basename(trim($match[1]));
				$prev_empty_line = false;
			}
			else if ( trim($line)=="" ){
				if (!$prev_empty_line)
					$sentences++;
				$prev_empty_line = true;
			}else
				$prev_empty_line = false;
		}
		$folds[$i][] = array($docstart,  $sentences);
		echo $i." = $sentences\n";
		$docstart = null;
	}
	print_r($folds);
	return $folds;
}			

/**
 * Index documents and sentences. Creates an array (document_nam => list_of_sentences) 
 */
function index_sentences($lines){
	// Index files and sentences
	$docs = array();
	$docstart = null;
	$sentence = array();
	$lines[] = "";
	foreach ($lines as $line){
		if (preg_match("/-DOCSTART FILE (.*)/", $line, $match)){
			if ( $docstart != null ){
				if ( count($sentence)>0 ){
					$docs[$docstart][] = $sentence;
				}
				$sentence = array();
			}
			$docstart = basename(trim($match[1]));
		}
		else if ( trim($line)=="" ){
			if (count($sentence)>0){
				$docs[$docstart][] = $sentence;
				$sentence = array();
			}
		}else{
			$sentence[] = $line;
		}
	}
	return $docs;	
}

/******************** main function       *********************************************/
// Pricess all files in a folder
function main ($config){
	$count = 0;
	$lines = explode("\n", file_get_contents($config->input));
	foreach ($lines as $line)
		if (strpos($line, " B-")!==false)
			$count++;
	echo "Number of annotations: $count\n";

	if ($config->base)
		split_base($config, $lines);
	else
		split_random($config, $lines);		
}

/******************** main invoke         *********************************************/
main($config);

?>
