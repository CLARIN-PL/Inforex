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
require_once("../../engine/include/database/CDbReportAnnotationLemma.php");
$config = null;
$config->dsn = array(
    			'phptype'  => 'mysql',
    			'username' => 'root',
    			'password' => 'root',
    			'hostspec' => 'localhost',
    			'database' => 'inforex',
				);
$mdb2 =& MDB2::singleton($config->dsn);

include("../../engine/database.php");
$db = new Database($config->dsn);

$chunks = array();
$reader = new TakipiReader();
$documents = array();
$c = 0;
$s = 0;
$t = 0;
$p = 0;

/**INPUT**/
//$reader->loadFile("380852.xml");
//$reader->loadFile("/home/adam/Desktop/00102482.xml");
$reader->loadFile("/home/adam/Downloads/kpwr-lemma/dev/blogi/00100598.xml");


$annTypeMap = array(
	"AdjP"=>"chunk_adjp",
	"AgP"=>"chunk_agp",
	"APP"=>"chunk_app",
	"NP"=>"chunk_np",
	"NumOrd"=>"chunk_numord",
	"QP"=>"chunk_qp",
	"Qub"=>"chunk_qub",
	"VP"=>"chunk_vp");
$annTypeMap = array();

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
	//if (preg_match("/([0-9]+)-([0-9]+):[0-9]+/", $chunk->id, $match)){
		$document_name = "100598";//$match[1];
		$document_part = $chunk->id;//$match[2];
		
		$documents[$document_name][$document_part] = $chunk;
	//}
	//else
		//throw new Exception("Malformed ID = {$chunk->id}\n");
}

echo "Liczba dokumentów: " . count($documents) . " (46)\n";

/* Sprawdź kolejność części */
foreach ($documents as $document=>$parts){
	$i=1;
	foreach ($parts as $no=>$part){
		$p++;
		if ( $no != "ch".$i++)
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
					$annTypeMap[$channel] = $channel; 
					$lemma = array_key_exists($channel,$token->lemmas)?$token->lemmas[$channel]:"";
					$intvalue = intval($value);					
					if ($intvalue>0){
						if (!array_key_exists($channel, $annotationMap[$document][$sentenceNum])){
							$annotationMap[$document][$sentenceNum][$channel] = array();
							$annotationMap[$document][$sentenceNum][$channel]['lastval']=$intvalue;
							$annotationMap[$document][$sentenceNum][$channel][$intvalue][] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma);
						}								
						else if (!array_key_exists($intvalue, $annotationMap[$document][$sentenceNum][$channel])){
							$annotationMap[$document][$sentenceNum][$channel]['lastval']=$intvalue;
							$annotationMap[$document][$sentenceNum][$channel][$intvalue][] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma);
						}								
						else if (array_key_exists($channel, $annotationMap[$document][$sentenceNum]) && 
							array_key_exists($intvalue, $annotationMap[$document][$sentenceNum][$channel])){
							$lastVal = $annotationMap[$document][$sentenceNum][$channel]['lastval'];
							if ($intvalue==$lastVal){
							 	$lastElem = array_pop($annotationMap[$document][$sentenceNum][$channel][$lastVal]);
								if ($token->ns) {
									$lastElem["text"].=$token->orth;
								}
								else {
									$lastElem["text"].= " ".$token->orth;
								}
								array_push($annotationMap[$document][$sentenceNum][$channel][$lastVal], $lastElem);
							}
							else{
								array_push($annotationMap[$document][$sentenceNum][$channel][$intvalue], array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma));
							}
							$annotationMap[$document][$sentenceNum][$channel]['lastval']=$intvalue;
						}
					}
					else {
						//var_dump($annotationMap[$document][$sentenceNum][$channel]);
						if (array_key_exists($channel, $annotationMap[$document][$sentenceNum])){
								$annotationMap[$document][$sentenceNum][$channel]['lastval']=0;
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
$reportLinks = db_fetch_rows('SELECT id, REPLACE(source,".xml","") as link FROM `reports` WHERE source LIKE "%.xml"');
$reportMap = array();
foreach ($reportLinks as $reportLink){
	$reportMap[$reportLink['link']]=$reportLink['id'];	
}
//var_dump($reportMap);
//fill database

foreach ($annotationMap as $documentId=>$sentences){
        $sql = "DELETE reports_annotations_optimized FROM reports_annotations_optimized 
                        LEFT JOIN annotation_types at ON at.annotation_type_id=reports_annotations_optimized.type_id 
			WHERE reports_annotations_optimized.report_id=".$documentId/*."
			AND at.name IN ('".implode("','",$annTypeMap)."')"*/;
	db_execute($sql);
	foreach ($sentences as $sentence){
		foreach ($sentence as $channelId=>$channel){
			foreach ($channel as $annotations){				
				if (is_array($annotations)){
					$annId = array();
					foreach ($annotations as $annotation){
						$sql = "INSERT INTO `reports_annotations_optimized` (`report_id`,`type_id`,`from`,`to`,`text`,`user_id`,`creation_time`,`stage`,`source`) " .
								"VALUES (".$documentId."," .
										" (SELECT annotation_type_id FROM annotation_types WHERE name='".$channelId."')," .
										$annotation['from']."," .
										($annotation['from'] + mb_strlen(preg_replace("/\n+|\r+|\s+/","",$annotation['text']), 'utf-8') -1) .",'" .
										addslashes($annotation['text'])."'," .
										"$userIdParam, now(), '$stageParam', '$sourceParam')";
						db_execute($sql);
//						echo $sql;
//						die;
						$raoIndex = $mdb2->lastInsertID();
						array_push($annId, $raoIndex);
						if($annotation["lemma"]){
							DbReportAnnotationLemma::saveAnnotationLemma($raoIndex, $annotation["lemma"]);
						}
						
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
