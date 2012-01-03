<?php
include("../cliopt.php");
include("../../engine/include/lib_htmlstr.php");
require_once("PEAR.php");
require_once("MDB2.php");

mb_internal_encoding("UTF-8");


class Channel {
	public $name = null; 
	public $value = null;
	
	function __construct($name, $value){
		$this->name = $name;
		$this->value = $value;
	}
	
	function getXml(){
		return "    <ann chan=\"{$this->name}\">{$this->value}</ann>\n";		
	}
}

class Lexem {
	public $disamb = null;
	public $base = null;
	public $ctag = null;	
	function __construct($disamb, $base, $ctag){
		$this->disamb = $disamb;
		$this->base = htmlspecialchars($base);
		$this->ctag = $ctag;
	}
	
	function getXml(){
		$xml = $this->disamb ? "    <lex disamb=\"1\">\n" : "    <lex>\n";
		$xml .= "     <base>{$this->base}</base>\n";
		$xml .= "     <ctag>{$this->ctag}</ctag>\n";
		return $xml . "    </lex>\n";
	}
}

class Token {
	public $orth = null;
	public $lexemes = null;
	public $channels = null;
	public $ns = null;
	
	function __construct($orth){
		$this->orth = htmlspecialchars($orth);
		$this->lexemes = array();
		$this->channels = array();
		$this->ns = false;
	}
	
	function getXml($channelTypes){
		$xml =  "   <tok>\n";
		$xml .= "    <orth>{$this->orth}</orth>\n";
		foreach ($this->lexemes as $lexeme)
			$xml .= $lexeme->getXml();
		
		foreach ($channelTypes as $annType)
			$xml .= $this->channels[$annType]->getXml();
		if ($this->ns) return $xml . "   </tok>\n   <ns/>\n";	
		return $xml . "   </tok>\n";		
			
	}
}

class Sentence {
	public $tokens = null;
	public $channelTypes = null;
	public $id = null;
	
	function __construct($id){
		$this->tokens = array();
		$this->channelTypes = array();
		$this->id = $id;
	}
		
	function getXml(){
		$usedTypes = array_keys($this->channelTypes);
		$xml = "  <sentence id=\"s{$this->id}\">\n";
		foreach ($this->tokens as $token)
			$xml .= $token->getXml($usedTypes);
		return $xml . "  </sentence>\n";
	}
}

class Chunk {
	public $id = null;
	public $sentences = null;
	function __construct($id){
		$this->id = $id;
		$this->sentences = array();
	}
	
	function getXml(){
		$xml = " <chunk id=\"{$this->id}\">\n";
		foreach ($this->sentences as $sentence)
			$xml .= $sentence->getXml();		
		return $xml . " </chunk>\n";
	}
} 

class ChunkList {
	public $chunks = null;
	
	function __construct(){
		$this->chunks = array();
	}
	
	function getXml(){
		$xml = "<chunkList>\n";
		foreach ($this->chunks as $chunk)
			$xml .= $chunk->getXml();
		return $xml . "</chunkList>\n";
		
	}
	
}

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
	$corpus_id = $opt->getParameters("corpus");
	$subcorpus_id = $opt->getParameters("subcorpus");
	$document_id = $opt->getParameters("document");
	if (!$corpus_id && !$subcorpus_id && !$document_id)
		throw new Exception("No corpus, subcorpus nor document set");
	$flag_names = array();
	$flag_value = array();
	if ( $opt->exists("flag")){
		$flag = $opt->getParameters("flag");
		foreach($flag as $f){
			if ( preg_match("/(.+)=(.+)/", $f, $n)){
				$flag_names[] = $n[1];
				if ( preg_match_all("/(?P<digit>\d+)/", $n[2], $v)){
					foreach($v['digit'] as $key => $digit)
						$flag_value[$n[1]][]=$digit;
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
	$relation_set = $opt->getOptionalParameters("relation_set");
	$relations = $opt->getOptionalParameters("relation");		
	$relationForce = $opt->getOptional("relation-force","none");
	$relationForce = $relationForce != "none";
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

include("../../engine/database.php");
ob_end_clean();

/* Pobierz id raportów do przetworzenia */
$reports = array();

//Jeżeli jest podany parametr flag pobiera id raportów o podanych flagach
if ( $opt->exists("flag")){
	foreach($flag_names as $flag_name){
		$flag_name_s = 'AND cf.short=\'' . $flag_name . '\' ';
		$sql = "SELECT rf.report_id AS id " .
				"FROM reports_flags rf " .
				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
				"WHERE cf.corpora_id=? " .
				$flag_name_s .	
				"AND rf.flag_id=? ";
		foreach ($corpus_id as $id){
			foreach ($flag_value[$flag_name] as $flag_v){
				foreach (db_fetch_rows($sql, array($id,$flag_v)) as $report){
					if ( intval($report['id'])){
						$reports[$report['id']] = 1;				
					}
				}
			}
		}
	
		$sql = "SELECT r.id " .
				"FROM reports r " .
				"LEFT JOIN reports_flags rf ON r.id=rf.report_id " .
				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
				"WHERE r.subcorpus_id=? " .
				$flag_name_s .
				"AND rf.flag_id=? ";
		foreach ($subcorpus_id as $id){
			foreach ($flag_value[$flag_name] as $flag_v){
				foreach (db_fetch_rows($sql, array($id,$flag_v)) as $report){
					if ( intval($report['id'])){
						$reports[$report['id']] = 1;				
					}
				}
			}
		}
	
		$sql = "SELECT rf.report_id AS id " .
				"FROM reports_flags rf " .
				"LEFT JOIN corpora_flags cf " .
				"ON cf.corpora_flag_id=rf.corpora_flag_id " .
				"WHERE rf.report_id=? " .
				$flag_name_s .
				"AND rf.flag_id=? ";	
		foreach ($document_id as $id){
			foreach ($flag_value[$flag_name] as $flag_v){
				foreach (db_fetch_rows($sql, array($id,$flag_v)) as $report){
					if ( intval($report['id'])){
						$reports[$report['id']] = 1;				
					}
				}
			}
		}
	}	
}else{//else if $opt->exists("flag")
	$sql = "SELECT id FROM reports WHERE corpora = ?";
	foreach ($corpus_id as $id)
		foreach (db_fetch_rows($sql, array($id)) as $report)
			if ( intval($report['id']))
				$reports[$report['id']] = 1;
	
	$sql = "SELECT id FROM reports WHERE subcorpus_id = ?";
	foreach ($subcorpus_id as $id){
		foreach (db_fetch_rows($sql, array($id)) as $report){
			if ( intval($report['id']))
			$reports[$report['id']] = 1;
		}
	}
		
	foreach ($document_id as $report_id)
		$reports[$report_id] = 1;	
}//end if $opt->exists("flag")

// Jeżeli jest podany parametr relation_set dodaje do relations dodatkowe typy
$relationsTypes = array();
$relationsTypes_names = array();
if($opt->exists("relation_set")){
	$sql = "SELECT rs.name AS name, rel.relation_type_id AS type " .
			"FROM relation_sets rs " .
			"LEFT JOIN relation_types rty ON rs.relation_set_id=rty.relation_set_id " .
			"LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) " .
			"WHERE rty.relation_set_id IN ('". implode("','",$relation_set) ."') " .
			"GROUP BY rel.relation_type_id ";
	$relationsTypes = db_fetch_rows($sql);
	foreach ($relationsTypes as $result){
		$relations[] = $result['type'];
		$relationsTypes_names[$result['type']] = $result['name'];
	}
}

$errors = array();
$count = 0;
ob_start();
foreach (array_keys($reports) as $id){
	$warningCount = 0;
	$warningMessage = "";
	$report = db_fetch("SELECT * FROM reports WHERE id = ?", array($id));
	//print "Processing report [report_id={$report['id']}]\n";

	//get tokens
	$sql = "SELECT * FROM tokens WHERE report_id=? ORDER BY report_id, `from`";
	$tokens = db_fetch_rows($sql, array($report['id']));
	
	if (empty($tokens)){
		$warningMessage .= "\n error: no tokens";
		$errors["tokens"][]=$report['id'];
		$warningCount++;
	}	
	
	//get tokens_tags
	$sql = "SELECT * " .
			"FROM tokens_tags " .
			"WHERE token_id " .
			"IN (" .
				"SELECT token_id " .
				"FROM tokens " .
				"WHERE report_id={$report['id']}" .
			")";
	$results = db_fetch_rows($sql);
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
		$sql = "SELECT rel.id, rel.relation_type_id, rel.source_id, rel.target_id, relation_types.name " .
				"FROM " .
					"(SELECT * " .
					"FROM relations " .
					"WHERE source_id IN " .
						"(SELECT id " .
						"FROM reports_annotations " .
						"WHERE report_id={$report['id']}) " .
					"AND relation_type_id " .
					"IN (".implode(",",$relations).")) rel " .
				"LEFT JOIN relation_types " .
				"ON rel.relation_type_id=relation_types.id ";
				
		$relationMap = db_fetch_rows($sql);
		
		$sql = "SELECT DISTINCT type " .
				"FROM reports_annotations " .
				"WHERE report_id={$report['id']} " .
				"AND " .
					"(id IN " .
						"(SELECT source_id " .
						"FROM relations " .
						"WHERE relation_type_id " .
						"IN " .
							"(".implode(",",$relations).") ) " .
					"OR id " .
					"IN " .
						"(SELECT target_id " .
						"FROM relations " .
						"WHERE relation_type_id " .
						"IN " .
							"(".implode(",",$relations).") ) )";
		$addAnnTypes = db_fetch_rows($sql);
							
		//force extra types					
		if ($relationForce){
			foreach ($addAnnTypes as $result)
				$annotation_types[] = $result['type'];
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
		
	if ($sql) 
		$annotations = db_fetch_rows($sql);	




	//create maps
	$channels = array();
	$annotationIdMap = array();
	$annotationChannelMap = array();
	foreach ($annotations as &$annotation){
		$channels[$annotation['type']]=array("counter"=>0, "elements"=>array(), "globalcounter"=>0);
		$annotationIdMap[$annotation['id']]=$annotation;
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
	$ns = false;			"(SELECT * " .
			"FROM reports " .
			"WHERE corpora=$corpus_id " .
			"OR subcorpus_id=$subcorpus_id) rep " .
			"LEFT JOIN corpus_subcorpora " .
				"ON (rep.subcorpus_id=corpus_subcorpora.subcorpus_id)";
	
	$lastId = count($tokens)-1;
	$countTokens=1;
	$countSentences=1;
	
	//NEW
	$currentChunkList = new ChunkList();
	$currentChunk = new Chunk("$reportLink-$chunkNumber:$chunkNumber");
	$currentSentence = new Sentence($countSentences);
	
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
		$id = $token['token_id'];
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
		
		$currentToken = new Token(
			mb_substr($chunks[$chunkNumber-1]['nospace'], 
					  $from-$chunks[$chunkNumber-1]['from'], 
					  $to - $from + 1));
		$chunks[$chunkNumber-1]['notags'] = mb_substr ($chunks[$chunkNumber-1]['notags'], mb_strlen($currentToken->orth));
		
		//insert lex
		foreach ($tokens_tags[$id] as $token_tag)
			$currentToken->lexemes[]=new Lexem($token_tag['disamb'], $token_tag['base'], $token_tag['ctag']);
		
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
					if (array_key_exists("target",$annotation)) 
						$annotationIdMap[$annotation["target"]]["num"]=1;
				}
			}
			else {
				if($annotation["from"]<=$from && $annotation["to"]>=$to){
					$lastElem = end($channel["elements"]);
					if ($annotation["id"]==$lastElem["id"]){
						$channel["elements"][]=array("num"=>$channel["counter"],"id"=>$annotation["id"]);
						$annotation['channelNum']=$channel["counter"];
						$annotation['sentenceNum']=$countSentences;						
						$channel["globalcounter"]++;
					}
					else {
						//check continuous relation
						if (array_key_exists("num",$annotation)) {
							$channel["elements"][]=array("num"=>$annotation["num"],"id"=>$annotation["id"]);
							$annotation['channelNum']=$annotation["num"];
							$annotation['sentenceNum']=$countSentences;						
							$channel["globalcounter"]++;							
						}
						else {
							$channel["counter"]++;
							$channel["elements"][]=array("num"=>$channel["counter"],"id"=>$annotation["id"]);
							$annotation['channelNum']=$channel["counter"];
							$annotation['sentenceNum']=$countSentences;						
							$channel["globalcounter"]++;							
						}	
					}
					if (array_key_exists("target",$annotation)){ 
						//$lastElem = end($channel["elements"]);
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
			$currentToken->channels[$annType] = new Channel($annType,$lastElem['num']);
			//update "used channels" dict
			if ($lastElem['num'])
				$currentSentence->channelTypes[$annType]=1;
		}

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
					$currentChunkList->chunks[]=$currentChunk;
					$currentChunk = new Chunk("$reportLink-$chunkNumber:$chunkNumber");
					$countSentences++;
					$currentSentence = new Sentence($countSentences);
					foreach ($channels as $annType=>&$channel){						
						$channel['counter']=0;
						$channel['elements']=array();
					}
				}
				else if ($token['eos']){
					$currentChunk->sentences[] = $currentSentence;
					$countSentences++;
					$currentSentence = new Sentence($countSentences);
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
	$currentChunkList->chunks[]=$currentChunk;
	
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
	fwrite($handle, $currentChunkList->getXml() . $xml);
	fclose($handle);
	
	$count++;
	echo "\r$count z " . count($reports);
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

?>
