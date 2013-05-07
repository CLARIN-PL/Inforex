<?php
/* Upper Words Frequency
 * ---
 * Calculates a frequency of upper words in a TaKIPI corpus.
 * ---
 * Created on 2009-11-26
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

// Include TaKIPI reader. 
include("../../engine/include/anntakipi/ixtTakipiReader.php");

$config_path_corpus = "/home/czuk/nlp/corpora/gpw2004/tag";

$r = new TakipiReader();

$docs = 0;
$sentences = 0;
$words = 0;

$words_freq = array();
print strtoupper("ążśźęćńół")."\n";
if ($handle = opendir($config_path_corpus)){
    while (false !== ($file = readdir($handle))) {
    	$filepath = $config_path_corpus . "/" . $file;
    	if($file!="." && $file!=".." && is_file($filepath)){
    		$content = file_get_contents($filepath);
    		$content = "<doc>$content</doc>";
//    		echo $filepath."\n";
	        $r->loadText($content);
	        $docs++;
			while ($r->nextSentence()){	
				//print "-> sentence\n";
				$sentences++;
				while ( ($token = $r->readToken()) !== false){
					$words++;
					$orth = $token->orth;
					if ($orth[0]>='A' && $orth[0]<='Z'){						
						//print $orth;
						if ($words_freq["$orth"])
							$words_freq["$orth"]++;
						else
							$words_freq["$orth"] = 1;
					}
				}
				//print "\n";
			}         
			//die();
    	}
    }	
	print "$docs/$sentences/$words\n";       
}

arsort($words_freq);
$keys = array_keys($words_freq);

foreach ($words_freq as $k=>$v){
	if ($v>10)
		print str_pad($k, 20) . $v . "\n";
}

//for ($i=0; $i<10; $i++)
//	print $keys[$i]."\t".$words_freq[$keys[$i]]."\n";

//$r = new TakipiReader();
//
//
//$r->loadText($text);
//while ($r->nextSentence()){	
//	print "-> sentence\n";
//	while ( ($token = $r->readToken()) !== false)
//		print $token->orth . " ";
//	print "\n";
//}
?>
