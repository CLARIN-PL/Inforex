<?
/**
 * 
 */
 
include("../../engine/include/anntakipi/ixtTakipiAligner.php"); 
include("../../engine/include/anntakipi/ixtTakipiStruct.php"); 
include("../../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../../engine/include/anntakipi/ixtTakipiHelper.php"); 
require_once("PEAR.php");
require_once("MDB2.php");
$config = null;
$config->dsn = array(
    			'phptype'  => 'mysql',
    			'username' => 'root',
    			'password' => 'sql',
    			'hostspec' => 'localhost',
    			'database' => 'gpw',
				);
include("../../engine/database.php");
$chunks = array();
$reader = new TakipiReader();
$documents = array();
$c = 0;
$s = 0;
$t = 0;
$p = 0;

/**INPUT**/
$reader->loadFile("ccl-wiki3.xml");

$annTypeMap = array(
	"AdjP"=>"chunk_adjp",
	"AgP"=>"chunk_agp",
	"APP"=>"chunk_app",
	"NP"=>"chunk_np",
	"NumOrd"=>"chunk_numord",
	"QP"=>"chunk_qp",
	"Qub"=>"chunk_qub",
	"VP"=>"chunk_vp");
$stageParam = "new";
$sourceParam = "user";
$userIdParam = "1";


while ($reader->nextChunk()){
	$chunks[] = $reader->readChunk();
}
$chunkNum = 0;
foreach ($chunks as $chunk){
	$s += count($chunk->sentences);
	foreach ($chunk->sentences as $sentence){
		$tokensNum = count($sentence->tokens); 
		$t += $tokensNum;
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
//create annotation map
$annotationMap = array();
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
					if ($intvalue>0){
						if (!array_key_exists($channel, $annotationMap[$document][$sentenceNum])){
							$annotationMap[$document][$sentenceNum][$channel] = array();
							$annotationMap[$document][$sentenceNum][$channel]['lastval']=$intvalue;
							$annotationMap[$document][$sentenceNum][$channel][$intvalue] = array();
							$annotationMap[$document][$sentenceNum][$channel][$intvalue][0] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth);
						}								
						else if (!array_key_exists($intvalue, $annotationMap[$document][$sentenceNum][$channel])){
							$annotationMap[$document][$sentenceNum][$channel]['lastval']=$intvalue;
							$annotationMap[$document][$sentenceNum][$channel][$intvalue] = array();
							$annotationMap[$document][$sentenceNum][$channel][$intvalue][0] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth);
						}								
						else if (array_key_exists($channel, $annotationMap[$document][$sentenceNum]) && 
							array_key_exists($intvalue, $annotationMap[$document][$sentenceNum][$channel])){
							$lastVal = $annotationMap[$document][$sentenceNum][$channel]['lastval'];
							if ($intvalue==$lastVal){
							 	$lastElem = array_pop($annotationMap[$document][$sentenceNum][$channel][$lastVal]);
								if ($token->ns) $lastElem["text"].=$token->orth;
								else $lastElem["text"].= " ".$token->orth;
								array_push($annotationMap[$document][$sentenceNum][$channel][$lastVal], $lastElem);
							}
							else array_push($annotationMap[$document][$sentenceNum][$channel][$intvalue], array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth));
							$annotationMap[$document][$sentenceNum][$channel]['lastval']=$intvalue;
						}
					}
				} 	
				$takipiText .= $token->orth;	
			}
 			$sentenceNum++;
		}
	}
}

//get report id/link from database
$reportLinks = db_fetch_rows('SELECT id, REPLACE(link,".xml","") as link FROM `reports` WHERE link LIKE "%.xml"');
$reportMap = array();
foreach ($reportLinks as $reportLink){
	$reportMap[$reportLink['link']]=$reportLink['id'];	
}
//var_dump($reportMap);

//fill database
foreach ($annotationMap as $documentId=>$sentences){
	$sql = "DELETE FROM `reports_annotations` " .
			"WHERE report_id=".$reportMap[$documentId]." ".
			"AND type IN ('".implode("','",$annTypeMap)."')";
	db_execute($sql);
	foreach ($sentences as $sentence){
		foreach ($sentence as $channelId=>$channel){
			foreach ($channel as $annotations){				
				if (is_array($annotations)){
					$annId = array();
					foreach ($annotations as $annotation){
						$sql = "INSERT INTO `reports_annotations` (`report_id`,`type`,`from`,`to`,`text`,`user_id`,`creation_time`,`stage`,`source`) " .
								"VALUES (".$reportMap[$documentId]."," .
										"'".$annTypeMap[$channelId]."'," .
										$annotation['from']."," .
										($annotation['from'] + mb_strlen(preg_replace("/\n+|\r+|\s+/","",$annotation['text']), 'utf-8') -1) .",'" .
										addslashes($annotation['text'])."'," .
										"$userIdParam, now(), '$stageParam', '$sourceParam')";
						db_execute($sql);
						array_push($annId, $mdb2->lastInsertID());
						if (count($annId)==2){
							$sql = "INSERT INTO `relations` (`relation_type_id`,`source_id`,`target_id`,`date`,`user_id`) " .
									"VALUES (1,".$annId[0].",".$annId[1].",now(), '$userIdParam')";
							db_execute($sql);
							$annId = array($annId[1]);		
							//print "$documentId  \n";
						}						
					}
				}
			}
		}
	}
}


?>