<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-02-15
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
/******************** set configuration   *********************************************/
$config = null;
$config->folds = 10;
$config->ratio = "2"; // training : testing
$config->input = $argv[1]; 

if ($config->input == "" ) 
	die ("Incorrect argument. Expected one of the following formats:\n" .
			"php split.php <filename>      // split given file name\n\n");
if (!file_exists($config->input))
	die ("File '{$config->input}' does not exist\n");  

/******************** misc function       *********************************************/

function save_fold($config, $folds, $fold_num, $docstart){
	$lines = implode("\n", $docstart)."\n";
	for ($i=0; $i<count($folds); $i++)
		if ($i+1 != $fold_num)
			$lines .= implode("\n", $folds[$i]) . "\n\n";
	file_put_contents($config->input . ".fold-{$fold_num}.train", $lines);
	file_put_contents($config->input . ".fold-{$fold_num}.test", implode("\n", $docstart)."\n".implode("\n", $folds[$fold_num-1]));	
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

	for ($n=1; $n<=10; $n++){
		echo " Saving fold $n\n";
		save_fold($config, $folds, $n, $docstart);
	}
}

/******************** main invoke         *********************************************/
main($config);

?>
