<?php
global $config;
include("../cliopt.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
ob_end_clean();

mb_internal_encoding("UTF-8");

//--------------------------------------------------------



//configure parameters
$opt = new Cliopt();
$opt->addExecute("php export-ccl.php --corpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx --annotation_layer n --annotation_name xxx --flag xxx=yy",null);
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus", "corpus id"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "subcorpus", "subcorpus id"));
$opt->addParameter(new ClioptParameter("document", "d", "document", "document id"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));

$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to folder where generated CCL files will be saved"));
$opt->addParameter(new ClioptParameter("annotation_layer", "l", "id", "export annotations assigned to layer 'id' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("annotation_name", null, "name", "export annotations assigned to type 'name' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("stage", null, "type", "export annotations assigned to stage 'type' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("relation", "r", "id", "export relations assigned to type 'id' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("relation_set", "relation_set", "id", "export relations assigned to relation_set 'id' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("relation-force", null, null, "insert annotations not set by 'annotation_*' parameters, but exist in 'relation id'"));
$opt->addParameter(new ClioptParameter("flag", "flag", "flag", "export using flag \"flag name\"=flag_value or \"flag name\"=flag_value1,flag_value2,..."));

//get parameters & set db configuration
$config = null;
try {
	$opt->parseCli($argv);
	
	$dbUser = $opt->getOptional("db-user", "root");
	$dbPass = $opt->getOptional("db-pass", "sql");
	$dbHost = $opt->getOptional("db-host", "localhost") . ":" . $opt->getOptional("db-port", "3306");
	$dbName = $opt->getOptional("db-name", "gpw");
	
	if ( $opt->exists("db-uri")){
		$uri = $opt->getRequired("db-uri");
		if ( preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)){
			$dbUser = $m[1];
			$dbPass = $m[2];
			$dbHost = $m[3];
			$dbName = $m[4];
		}else{
			throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
		}
	}
		
	$config->dsn = array(
	    			'phptype'  => 'mysql',
	    			'username' => $dbUser,
	    			'password' => $dbPass,
	    			'hostspec' => $dbHost,
	    			'database' => $dbName);		    			
	    			
	$corpus_ids = $opt->getParameters("corpus");
	$subcorpus_ids = $opt->getParameters("subcorpus");
	$document_ids = $opt->getParameters("document");
	
	if (!$corpus_ids && !$subcorpus_ids && !$document_ids)
		throw new Exception("No corpus, subcorpus nor document set");
		
	$flag_names = array();
	$flag_values = array();
	if ( $opt->exists("flag")){
		$flag = $opt->getParameters("flag");
		foreach($flag as $f){
			if ( preg_match("/(.+)=(.+)/", $f, $n)){
				$flag_names[] = $n[1];
				if ( preg_match_all("/(?P<digit>\d+)/", $n[2], $v)){
					foreach($v['digit'] as $key => $digit)
						$flag_values[$n[1]][]=$digit;
				}						
			}else{
				throw new Exception("Flag is incorrect. Given '$flag', but exptected 'name=value'");
			}	
		}		
	}	
	$folder = $opt->getRequired("folder");
	$annotation_layers = $opt->getOptionalParameters("annotation_layer");
	$annotation_names = $opt->getOptionalParameters("annotation_name");
	$stages = $opt->getOptionalParameters("stage");
	$relation_set_ids = $opt->getOptionalParameters("relation_set");	
	$relation_type_ids = $opt->getOptionalParameters("relation");		
	$relationForce = $opt->getOptional("relation-force","none");
	$relationForce = $relationForce != "none";
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

//--------------------------------------------------------
$db = new Database($config->dsn);

$report_ids = array();
$all_reports = array();
$relationsTypes = array();
$all_tokens = array();
$all_tokens_tags = array();
$all_relations = array();
$all_ann_types = array();
$relations = array();
if (!empty($relation_type_ids)) $relations = $relation_type_ids;



$report_ids = DbReport::getReportIds($corpus_ids, $subcorpus_ids, $document_ids, $flag_names, $flag_values);
$all_reports = DbReport::getReports(null, null, $report_ids, null);

if($opt->exists("relation_set") || $opt->exists("relation")){
	//possible bug, take also relations passed as parameters to add to name?
	$relationsTypes = DBCorpusRelation::getRelationsByRelationSetIds($relation_set_ids);
}
$all_tokens = DbToken::getTokensByReportIds($report_ids);
$all_tokens_tags = DbTag::getTagsByReportIds($report_ids);


$relationsTypes_names = array();

foreach ($relationsTypes as &$relationType){
	$relations[] = &$relationType['type'];
	$relationsTypes_names[$relationType['type']] = &$relationType['name'];
}

$all_relations_id = array();
$all_ann_types_id = array();
if (!empty($relations)){
	$all_relations = DbCorpusRelation::getRelationsBySets($report_ids, $relations);
	$all_ann_types = DbAnnotation::getAnnotationTypesBySets($report_ids, $relations);
	
	
    foreach ($all_relations as &$relation_item){
        if (array_key_exists($relation_item['report_id'],$all_relations_id)){
            $all_relations_id[$relation_item['report_id']][] = &$relation_item;
        }
        else {
            $all_relations_id[$relation_item['report_id']] = array(&$relation_item);
        }
    }		
    
    foreach ($all_ann_types as &$ann_type_item){
        if (array_key_exists($ann_type_item['report_id'],$all_ann_types_id)){
            $all_ann_types_id[$ann_type_item['report_id']][] = &$ann_type_item;
        }
        else {
            $all_ann_types_id[$ann_type_item['report_id']] = array(&$ann_type_item);
        }
    }    
}

$errors = array();
$count = 0;

$all_reports_id = array();

$all_tokens_id = array();
$all_tokens_tags_id = array();

foreach ($all_reports as &$report_item){
    $all_reports_id[$report_item['id']] = &$report_item;
}



foreach ($all_tokens as &$token_item){
    if (array_key_exists($token_item['report_id'],$all_tokens_id)){
        $all_tokens_id[$token_item['report_id']][] = &$token_item;
    }
    else {
        $all_tokens_id[$token_item['report_id']] = array(&$token_item);
    }
}

foreach ($all_tokens_tags as &$token_tag_item){
    if (array_key_exists($token_tag_item['report_id'],$all_tokens_tags_id)){
        $all_tokens_tags_id[$token_tag_item['report_id']][] = &$token_tag_item;
    }
    else {
        $all_tokens_tags_id[$token_tag_item['report_id']] = array(&$token_tag_item);
    }
}

ob_start();
foreach ($report_ids as $id){
    //if ($id!=101214) continue;
	$warningCount = 0;
	$warningMessage = "";

    //all_reports_id
    $report = &$all_reports_id[$id];

	//get tokens
    $tokens = &$all_tokens_id[$id];
	
	if (empty($tokens)){
		$warningMessage .= "\n error: no tokens";
		$errors["tokens"][]=$report['id'];
		$warningCount++;
	}	
	
	//get tokens_tags
    $results = &$all_tokens_tags_id[$id];
	$tokens_tags = array();
	
	if (empty($results)){
		$warningMessage .=  " \n error: no tags";
		$errors["tags"][]=$report['id'];
		$warningCount++;
	}
	
	if ($warningCount){
		$warningMessage .= "\n";
		echo $warningMessage;
		$count++;
		echo "\r$count z " . count($reports);
		ob_flush();
		continue;
	}
	
	foreach ($results as &$result){
		$tokens_tags[$result['token_id']][]=$result;
	}

	//copy types 
	$annotation_types = $annotation_names;
	
	//get relations
	$addAnnTypes = null;
	$relationMap = array();
	if (!empty($relations)){
        $relationMap = &$all_relations_id[$id];
        $addAnnTypes = &$all_ann_types_id[$id];			
		//force extra types					
		if ($relationForce && $addAnnTypes){
			foreach ($addAnnTypes as &$result)
				$annotation_types[] = &$result['type'];
		}
	}
	
	//get annotations
	$annotations = array();
	$sql = "SELECT `id`,`type`, `from`, `to` " .
			"FROM reports_annotations " .
			"WHERE report_id={$report['id']} ";
	if ($annotation_types && !$annotation_layers)
		$sql .= "AND type " .
				"IN ('". implode("','",$annotation_types) ."') ";
	else if (!$annotation_types && $annotation_layers)
		$sql .= "AND type " .
				"IN (" .
					"SELECT `name` " .
					"FROM annotation_types " .
					"WHERE group_id IN (". implode(",",$annotation_layers) .")" .
				")";	
	else if ($annotation_types && $annotation_layers)
		$sql .= "AND (type " .
				"IN ('". implode("','",$annotation_types) ."') " .
				"OR type " .
				"IN (" .
					"SELECT `name` " .
					"FROM annotation_types " .
					"WHERE group_id IN (". implode(",",$annotation_layers) .")" .
				"))";
	else 
		$sql = null;
		
	if ($sql) {
		$annotations = db_fetch_rows($sql);	
    }

	//create maps
	$channels = array();
	$annotationIdMap = array();
	$annotationChannelMap = array();
	foreach ($annotations as &$annotation){
		$channels[$annotation['type']]=array("counter"=>0, "elements"=>array(), "globalcounter"=>0);
		$annotationIdMap[$annotation['id']]=&$annotation;
	}
	
	//get continuous relations
	$sql = "SELECT * " .
			"FROM relations " .
			"WHERE source_id " .
			"IN (". (count($annotationIdMap) ? implode(",",array_keys($annotationIdMap)) : "0")  .") " .
			"AND relation_type_id=1";
	$continuousRelations = db_fetch_rows($sql);
	foreach ($continuousRelations as &$relation){
		$annotationIdMap[$relation['source_id']]['target']=$annotationIdMap[$relation['target_id']]["id"];
		$annotationIdMap[$relation['target_id']]['source']=$annotationIdMap[$relation['source_id']]["id"];
	}			
	//init 
	$chunkNumber = 1;
	$reportLink = str_replace(".xml","",$report['link']);
	$ns = false;		
	
	$lastId = count($tokens)-1;
	$countTokens=1;
	$countSentences=1;
	
	//NEW
	$currentDocument = new CclDocument();
	$currentChunk = new CclChunk("$reportLink-$chunkNumber:$chunkNumber");
	$currentSentence = new CclSentence($countSentences);
	
	//split text by chunks
	$chunkList = explode('</chunk>', $report['content']);
	$chunks = array();
	
	$from = 0;
	$to = 0;
	foreach ($chunkList as $chunk){
		$chunk = str_replace("<"," <",$chunk);
		$chunk = str_replace(">","> ",$chunk);
		$tmpStr = trim(preg_replace("/\s\s+/"," ",html_entity_decode(strip_tags($chunk),ENT_COMPAT, 'UTF-8')));
		$tmpStr2 = preg_replace("/\n+|\r+|\s+/","",$tmpStr);
		$to = $from + mb_strlen($tmpStr2)-1;
		$chunks[]=array(
			"notags" => $tmpStr,
			"nospace" => $tmpStr2,
			"from" => $from,
			"to" => $to
		);
		$from = $to+1;		
	}	
	$max_chunk = $to;
	$token_error = 0;	
	foreach ($tokens as $index => $token){
		$tid = $token['token_id'];
		$from = $token['from'];
		$to = $token['to'];
		// Jeżeli indeksy tokenów przekraczają indeks dokumentu
		if($from > $max_chunk){
			$token_error++;
			if($token_error == 1){
				print "\n error: Tokens out of scale\n";
				ob_flush();				
			}
			$errors["tokens_out"][$report['id']]=$token_error;
			continue;
		}
		
		$currentToken = new CclToken(
			mb_substr($chunks[$chunkNumber-1]['nospace'], 
					  $from-$chunks[$chunkNumber-1]['from'], 
					  $to - $from + 1));
		$chunks[$chunkNumber-1]['notags'] = mb_substr ($chunks[$chunkNumber-1]['notags'], mb_strlen($currentToken->orth));
		
		//insert lex
		foreach ($tokens_tags[$tid] as $token_tag)
			$currentToken->lexemes[]=new CclLexem($token_tag['disamb'], $token_tag['base'], $token_tag['ctag']);
		
		//prepare channels
		foreach ($annotationIdMap as &$annotation){
			$channel = &$channels[$annotation['type']];
			if (empty($channel["elements"])){
				if($annotation["from"]<=$from && $annotation["to"]>=$to){
					$channel["elements"][]=array("num"=>1,"id"=>$annotation["id"]);
					$channel["counter"]=1;
					$channel["globalcounter"]++;
					$annotation['channelNum']=1;
					$annotation['sentenceNum']=$countSentences;
					//check continuous relation
					if (array_key_exists("target",$annotation)) {
						$annotationIdMap[$annotation["target"]]["num"]=1;
                    }
				}
			}
			else {
				if($annotation["from"]<=$from && $annotation["to"]>=$to){
					$lastElem = end($channel["elements"]);
                    //var_dump($currentToken);
                    //var_dump($annotation);                        
					if ($annotation["id"]==$lastElem["id"] && !array_key_exists("num",$annotation)){
                        //echo "\n++1\n";
                        //echo "\n" . $channel["counter"] . "\n";
						$channel["elements"][]=array("num"=>$channel["counter"],"id"=>$annotation["id"]);
						$annotation['channelNum']=$channel["counter"];
						$annotation['sentenceNum']=$countSentences;						
						$channel["globalcounter"]++;
					}
					else {
						//check continuous relation
						if (array_key_exists("num",$annotation)) {
                        //echo "\n++2\n";
							$channel["elements"][]=array("num"=>$annotation["num"],"id"=>$annotation["id"]);
							$annotation['channelNum']=$annotation["num"];
							$annotation['sentenceNum']=$countSentences;						
							$channel["globalcounter"]++;							
						}
						else {
                        //echo "\n++3\n";
                        //echo "\n!!!CHANNEL ++\n";
							$channel["counter"]++;
							$channel["elements"][]=array("num"=>$channel["counter"],"id"=>$annotation["id"]);
							$annotation['channelNum']=$channel["counter"];
							$annotation['sentenceNum']=$countSentences;						
							$channel["globalcounter"]++;							
                        //var_dump($channel);
						}	
					}
					if (array_key_exists("target",$annotation)){ 
                        //echo "\n++4\n";

						$lastElem = end($channel["elements"]);
						$annotationIdMap[$annotation["target"]]["num"]=$lastElem["num"];
					}
				}
			}	
		}
		
		//fill with zeros && insert channels
		foreach ($channels as $annType=>&$channel){
			if ($channel["globalcounter"]<$countTokens){
				$channel["elements"][]=array("num"=>0,"id"=>0);
				$channel["globalcounter"]++;											
			}
			$lastElem = end($channel["elements"]);
			$currentToken->channels[$annType] = new CclChannel($annType,$lastElem['num']);
			//update "used channels" dict
			if ($lastElem['num'])
				$currentSentence->channelTypes[$annType]=1;
		}
        //echo "\n after: \n";
        //var_dump($currentToken);
		//close tag and/or sentence and/or chunk
		if ($index<$lastId){
			$nextChar = empty($chunks[$chunkNumber-1]['notags']) ? " " : $chunks[$chunkNumber-1]['notags'][0];
			if ($nextChar!=" ") {
				$currentToken->ns = true;
				$currentSentence->tokens[]=$currentToken;
			}
			else {
				$chunks[$chunkNumber-1]['notags'] = trim($chunks[$chunkNumber-1]['notags']);
				$currentSentence->tokens[]=$currentToken;
				if ($tokens[$index+1]['from']>=$chunks[$chunkNumber-1]['to']){
					$chunkNumber++;
					$currentChunk->sentences[] = $currentSentence;
					$currentDocument->chunks[]=$currentChunk;
					$currentChunk = new CclChunk("$reportLink-$chunkNumber:$chunkNumber");
					$countSentences++;
					$currentSentence = new CclSentence($countSentences);
					foreach ($channels as $annType=>&$channel){						
						$channel['counter']=0;
						$channel['elements']=array();
					}
				}
				else if ($token['eos']){
					$currentChunk->sentences[] = $currentSentence;
					$countSentences++;
					$currentSentence = new CclSentence($countSentences);
					foreach ($channels as $annType=>&$channel){						
						$channel['counter']=0;
						$channel['elements']=array();
					}
				}
			}
		}
		else 
			$currentSentence->tokens[]=$currentToken;
		
		$countTokens++;
	}
	$currentChunk->sentences[] = $currentSentence;
	$currentDocument->chunks[]=$currentChunk;
	
	//make relations
	$xml = "";
	if (!empty($relationMap)){
		$xml = "<relations>\n";
		foreach ($relationMap as $rel){
			if (array_key_exists($rel['source_id'],$annotationIdMap) && array_key_exists($rel['target_id'],$annotationIdMap)){
				$xml .= " <rel name=\"{$rel['name']}\"" . (array_key_exists($rel['relation_type_id'],$relationsTypes_names) ? " set=\"" .$relationsTypes_names[$rel['relation_type_id']]. "\"" : '') . ">\n";
				$xml .= "  <from sent=\"s{$annotationIdMap[$rel['source_id']]['sentenceNum']}\" chan=\"{$annotationIdMap[$rel['source_id']]['type']}\">{$annotationIdMap[$rel['source_id']]['channelNum']}</from>\n";
				$xml .= "  <to sent=\"s{$annotationIdMap[$rel['target_id']]['sentenceNum']}\" chan=\"{$annotationIdMap[$rel['target_id']]['type']}\">{$annotationIdMap[$rel['target_id']]['channelNum']}</to>\n";
				$xml .= " </rel>\n";
			}
			else {
				print "  warning: no annotation to export relation [id={$rel['id']}] (use --relation-force parameter)\n";
				ob_flush();		
				$errors["anns"][]=$rel['id'];				
			}
		} 
		$xml .= "</relations>\n";	
	}
	
	$subfolder = $folder . "/";
	// W tabeli reports nie ma kolumny name
	// . ($report['name'] ?  preg_replace("/[^\p{L}|\p{N}]+/u","_",html_entity_decode($report['name'],ENT_COMPAT, 'UTF-8')) . "/" : "" );
	if (!is_dir($subfolder)) mkdir($subfolder, 0777);
	
	//save to file .
	$fileName = preg_replace("/[^\p{L}|\p{N}]+/u","_",$report['title']); 
	$fileName .= (mb_substr($fileName, -1)=="_" ? "" : "_") . $report['id'] . ".xml";
	$handle = fopen($subfolder . $fileName ,"w");
	fwrite($handle, $currentDocument->getXml() . $xml);
	fclose($handle);
	
	$count++;
	echo "\r$count z " . count($reports) . " #" .$id;
	ob_flush();	
}

if (!empty($errors)){
	print "\n*******ERROR SUMMARY*********\n";
	if (array_key_exists('tokens',$errors)){
		print "\n* No tokenization (reports.id): ";
		foreach ($errors['tokens'] as $id)
			print $id . " ";
	}
	if (array_key_exists('tags',$errors)){
		print "\n* No tags (reports.id): ";
		foreach ($errors['tags'] as $id)
			print $id . " ";
	}
	if (array_key_exists('anns',$errors)){
		print "\n* No annotations (relations.id): ";
		foreach ($errors['anns'] as $id)
			print $id . " ";
	}
	if (array_key_exists('tokens_out',$errors)){
		print "\n* Tokens out of scale: ";
		foreach ($errors['tokens_out'] as $id => $count)
			print "\n\t(relations.id): " . $id . " => " . $count . ($count==1 ? " time " : " times ");
	}
	print "\n*****************************\n";
}










/*foreach ($reports as $r){
	
	$tokens = DbToken::getTokenByReportId($r['id']);
	$ccl = CclFactory::createFromPremorphAndTokens($r['content'], $tokens);
		
	// Wstawienie anotacji do kanałów
	
	// Wstawienie relacji między anotacjami
		
	CclWriter::write($ccl, $filename);
}*/


?>
