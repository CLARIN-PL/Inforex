<?php
/* 
 * ---
 * Urównolegla pliki txt i tag
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
include("../engine/include/anntakipi/ixtTakipiAligner.php"); 
include("../engine/include/anntakipi/ixtTakipiDocument.php"); 
 
$location = "/home/czuk/nlp/corpora/gpw2004";

$what = $argv[1];

if ($what != "all" && intval($what)==0) die ("Incorrect argument. Expected 'all' or raport id.\n");  

if ($what == "all"){
	if ($handle = opendir($location."/text")){
		while ( false !== ($file = readdir($handle))){
			if ($file != "." && $file != ".."){
				$textfile = $location . "/annotated/" . $file;
				$tagfile = $location . "/tag/" . $file . ".tag";
				
				if (!file_exists($tagfile))
					throw new Exception("File '$tagfile' does not exist\n");
					
				echo sprintf("%-30s ", $textfile);
	
				$takipiDoc = TakipiDocument::createFromFile($tagfile);
				
				//foreach ($takipiDoc->tokens as $t)
				//	echo $t->orth . "\n";
				//print_r($takipiDoc);
				
				$annDoc = TakipiAligner::align(file_get_contents($textfile), $takipiDoc);
				//print_r($annDoc);
				echo sprintf("%3d\n", count($annDoc->annotations));
				
				//die();
			}
		}
	}
} else {
	$file = str_pad($what, 7, "0", STR_PAD_LEFT) . ".txt";	
	$textfile = $location . "/annotated/" . $file;
	$tagfile = $location . "/tag/" . $file . ".tag";
		
	echo sprintf("%-30s ", $textfile);
	
	$takipiDoc = TakipiDocument::createFromFile($tagfile);
	//foreach ($takipiDoc->tokens as $t){
	//	echo $t->orth . "\n";
	//	if (in_array($i++, $takipiDoc->sentenceEnds)) echo "-----------\n";
	//}
	//print_r($takipiDoc);
	
	$annDoc = TakipiAligner::align(file_get_contents($textfile), $takipiDoc);
	echo sprintf("%3d\n", count($annDoc->annotations));
}
 
?>
