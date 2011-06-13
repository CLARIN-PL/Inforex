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

foreach ($chunks as $chunk){
	$s += count($chunk->sentences);
	foreach ($chunk->sentences as $sentence)
		$t += count($sentence->tokens);
}

echo "Liczba chunków: " . count($chunks) . " (218)\n";
echo "Liczba zdań   : " . $s . " (351)\n";
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

/* Odczytaj chunki */
foreach ($documents as $document=>$parts){
	foreach ($parts as $no=>$chunk){
		foreach ($chunk->sentences as $sentence){
			if ( count($sentece->chunks) == 0 )
				throw new Exception("Puste zdanie?");
		}
	}
}

?>