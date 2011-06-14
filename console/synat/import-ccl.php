<?
/**
 * 
 */
 
include("../../engine/include/anntakipi/ixtTakipiAligner.php"); 
include("../../engine/include/anntakipi/ixtTakipiStruct.php"); 
include("../../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../../engine/include/anntakipi/ixtTakipiHelper.php"); 

//$takipi = TakipiReader::createDocument("ccl-wiki3.xml");

$chunks = array();
$reader = new TakipiReader();
$documents = array();
$c = 0;
$s = 0;
$t = 0;
$p = 0;

$reader->loadFile("ccl-wiki3.xml");

while ($reader->nextChunk()){
	$chunks[] = $reader->readChunk();
}
//var_dump($chunks);

$chunkNum = 0;
foreach ($chunks as $chunk){
	//$chunkNum++;
	$s += count($chunk->sentences);
	//$sentenceNum=0;
	
	foreach ($chunk->sentences as $sentence){
		//$sentenceNum++;
		$tokensNum = count($sentence->tokens); 
		//print "$chunkNum $sentenceNum $tokensNum\n"; 
		
		$t += $tokensNum;
		//echo count($sentence->tokens) . "\n";
	}
}

echo "Liczba chunków: " . count($chunks) . " (218)\n";
echo "Liczba zdań   : " . $s . " (391)\n";
echo "Liczba tokenów: " . $t . " (5191)\n";

/* Podziel na dokumenty */
foreach ($chunks as $chunk){
	if (preg_match("/([0-9]+)-([0-9]+):[0-9]+/", $chunk->id, $match)){
		$document_name = $match[1];
		$document_part = $match[2];
		
		$documents[$document_name][$document_part] = $chunk;
	}
	else
		throw new Exception("Malformed ID = {$chunk->id}\n");
}

echo "Liczba dokumentów: " . count($documents) . " (46)\n";

/* Sprawdź kolejność części */
foreach ($documents as $document=>$parts){
	$i=1;
	foreach ($parts as $no=>$part){
		$p++;
		if ( $no != $i++)
			throw new Exception("Missing part for $document");
	}
}
echo "Liczba części : " . $p . " (218)\n";

$annotationMap = array();
/* Odczytaj chunki */
foreach ($documents as $document=>$parts){
	$annotationMap[$document]=array();
	$sentenceNum = 0;
	$takipiText = "";
	foreach ($parts as $chunk){
		foreach ($chunk->sentences as $sentence){
			$annotationMap[$document][$sentenceNum]=array();
			foreach ($sentence->tokens as $token){
				foreach ($token->channels as $channel=>$value){
					$intvalue = intval($value);
					if ($intvalue>0 && !array_key_exists($channel, $annotationMap[$document][$sentenceNum])){
						$annotationMap[$document][$sentenceNum][$channel] = array();
						$annotationMap[$document][$sentenceNum][$channel]['lastval']=$intvalue;
						$annotationMap[$document][$sentenceNum][$channel][$intvalue] = array(array("report_id"=>0, "from"=>mb_strlen($takipiText), "text"=>$token->orth));
					}								
					else if (array_key_exists($channel, $annotationMap[$document][$sentenceNum])){
						/*if ($intvalue==0){
							$lastVal = $annotationMap[$document][$sentenceNum][$channel]['lastval'];
							if ($lastVal!=0){
								print "\n" . $lastVal . "\n";
								$lastPos = count($annotationMap[$document][$sentenceNum][$channel][$lastVal])-1;
								$annotationMap[$document][$sentenceNum][$channel][$lastVal][$lastPos]["to"]=mb_strlen($takipiText)-1;
							}
						}*/
						/*else if ($intvalue!=$lastVal){
							if ($lastVal!=0){
								$lastPos = count($annotationMap[$document][$sentenceNum][$channel][$lastVal])-1;
								$annotationMap[$document][$sentenceNum][$channel][$lastVal][$lastPos]["to"]=mb_strlen($takipiText)-1;
							}
							//$annotationMap[$document][$sentenceNum][$channel][$lastVal][$lastPos]["to"]=mb_strlen($takipiText)-1;
							array_push($annotationMap[$document][$sentenceNum][$channel][$intvalue], array("report_id"=>0, "from"=>mb_strlen($takipiText), "text"=>$token->orth));
						}*/
						$lastVal = $annotationMap[$document][$sentenceNum][$channel]['lastval'];
						
						if ($intvalue>0 && $intvalue==$lastVal){
							$lastPos = count($annotationMap[$document][$sentenceNum][$channel][$lastVal])-1;
							//print "\n" . $lastPos . "\n";
							if ($token->ns){
								$annotationMap[$document][$sentenceNum][$channel][$lastVal][$lastPos]["text"].=$token->orth;
							}else{
								$annotationMap[$document][$sentenceNum][$channel][$lastVal][$lastPos]["text"].= " ".$token->orth;
							}														
						}
						$annotationMap[$document][$sentenceNum][$channel]['lastval']=$intvalue;
					}
				} 	
				$takipiText .= $token->orth;	
			 	
			}
 			$sentenceNum++;
		}
	}
}

var_dump($annotationMap);

?>