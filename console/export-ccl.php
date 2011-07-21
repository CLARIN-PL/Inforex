<?php
include("cliopt.php");
include("../engine/include/lib_htmlstr.php");
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
}

class Lexem {
	public $disamb = null;
	public $base = null;
	public $ctag = null;	
	function __construct($disamb, $base, $ctag){
		$this->disamb = $disamb;
		$this->base = $base;
		$this->ctag = $ctag;
	}
}

class Token {
	public $orth = null;
	public $lexemes = null;
	public $channels = null;
	public $ns = null;
	
	function __construct($orth){
		$this->orth = $orth;
		$this->lexemes = array();
		$this->channels = array();
		$this->ns = false;
	}
	
	/*function addLexeme($lexem){
		$this->lexemes[] = $lexem;
	}
	
	function addChannel($channel){
		$this->channels[] = $channel;
	}*/
}

class Sentence {
	public $tokens = null;
	public $channelTypes = null;
	
	function __construct(){
		$this->tokens = array();
		$this->channelTypes = array();
	}
		
	function getXml(){
		$usedTypes = array_keys($this->channelTypes);
		$xml = "    <sentence>";
		foreach ($this->tokens as $token)
			$xml .= $token->getXml($usedTypes);
		return $xml . "    </sentence>\n";
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
		$xml = "  <chunk id=\"{$this->id}\">\n";
		foreach ($this->sentences as $sentence)
			$xml .= $sentence->getXml();		
		return "  </chunk>\n";
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




$opt = new Cliopt();
$opt->addExecute("php export-ccl.php --corpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx --annotation_layer n --annotation_name xxx",null);
$opt->addParameter(new ClioptParameter("corpus", null, "corpus", "corpus id"));
$opt->addParameter(new ClioptParameter("subcorpus", null, "subcorpus", "subcorpus id"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));

$opt->addParameter(new ClioptParameter("folder", null, "path", "path to folder where generated CCL files will be saved"));
$opt->addParameter(new ClioptParameter("annotation_layer", null, "id", "export annotations assigned to layer 'id' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("annotation_name", null, "name", "export annotations assigned to type 'name' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("stage", null, "type", "export annotations assigned to stage 'type' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("relation", null, "id", "export relations assigned to type 'id' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("relation-force", null, null, "insert annotations not set by 'annotation_*' parameters, but exist in 'relation id'"));


$config = null;
try {
	$opt->parseCli($argv);
	$config->dsn = array(
	    			'phptype'  => 'mysql',
	    			'username' => $opt->getOptional("db-user", "root"),
	    			'password' => $opt->getOptional("db-pass", "sql"),
	    			'hostspec' => $opt->getOptional("db-host", "localhost") . ":" . $opt->getOptional("db-port", "3306"),
	    			'database' => $opt->getOptional("db-name", "gpw"));	
	$corpus_id = $opt->getOptional("corpus", "0");
	$subcorpus_id = $opt->getOptional("subcorpus", "0");
	if (!$corpus_id && !$subcorpus_id)
		throw new Exception("No corpus or subcorpus set");	
	else if ($corpus_id && $subcorpus_id)
		throw new Exception("Set only one parameter: corpus or subcorpus");
	$folder = $opt->getRequired("folder");
	$annotation_layers = $opt->getOptionalParameters("annotation_layer");
	$annotation_names = $opt->getOptionalParameters("annotation_name");
	$stages = $opt->getOptionalParameters("stage");
	$relations = $opt->getOptionalParameters("relation");
	$relationForce = $opt->getOptional("relation-force","none");
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}
include("../engine/database.php");


$sql = "SELECT * FROM reports WHERE corpora=$corpus_id OR subcorpus_id=$subcorpus_id";
$reports = db_fetch_rows($sql);


foreach ($reports as $report){
	$fileName = preg_replace("/\W/","_",$report['title'])."_".$report['id'] . ".xml"; 
	$handle = fopen($folder . "/".$fileName ,"w");

	
	//get tokens
	$sql = "SELECT * " .
			"FROM tokens " .
			"WHERE report_id={$report['id']}";
	$tokens = db_fetch_rows($sql);
	
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
	
	foreach ($results as &$result){
		$tokens_tags[$result['token_id']][]=$result;
	}

		
	//get annotations
	$annotations = null;
	$sql = "SELECT `id`,`type`, `from`, `to` " .
			"FROM reports_annotations " .
			"WHERE report_id={$report['id']} ";
	if ($annotation_names && !$annotation_layers)
		$sql .= "AND type " .
				"IN ('". implode("','",$annotation_names) ."') ";
	else if (!$annotation_names && $annotation_layers)
		$sql .= "AND type " .
				"IN (" .
					"SELECT `name` " .
					"FROM annotation_types " .
					"WHERE group_id IN (". implode(",",$annotation_layers) .")" .
				")";	
	else if ($annotation_names && $annotation_layers)
		$sql .= "AND (type " .
				"IN ('". implode("','",$annotation_names) ."') " .
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
			"IN (".implode(",",array_keys($annotationIdMap)).") " .
			"AND relation_type_id=1";
	$continuousRelations = db_fetch_rows($sql);
	foreach ($continuousRelations as &$relation){
		$annotationIdMap[$relation['source_id']]['target']=$annotationIdMap[$relation['target_id']]["id"];
		$annotationIdMap[$relation['target_id']]['source']=$annotationIdMap[$relation['source_id']]["id"];
	}			
	
	$htmlStr = new HtmlStr($report['content']);
	$chunkNumber = 1;
	$reportLink = str_replace(".xml","",$report['link']);
	$xml = "<chunkList><chunk id=\"$reportLink-$chunkNumber:$chunkNumber\"><sentence>"; 
	$ns = false;
	$lastId = count($tokens)-1;
	$countTokens=1;
	
	//NEW
	$currentChunkList = new ChunkList();
	$currentChunk = new Chunk("$reportLink-$chunkNumber:$chunkNumber");
	$currentSentence = new Sentence();
	foreach ($tokens as $index => $token){
		$id = $token['token_id'];
		$from = $token['from'];
		$to = $token['to'];
		$xml .= "<tok num=\"{$countTokens}\" id=\"{$token['token_id']}\" from=\"{$from}\" to=\"{$to}\">";
		$xml .= "<orth>{$htmlStr->getText($from,$to)}</orth>";

		$currentToken = new Token($htmlStr->getText($from,$to));
		//insert lex
		foreach ($tokens_tags[$id] as $token_tag){
			if ($token_tag['disamb']==1)
				$xml .= "<lex disamb=\"1\">";			
			else  
				$xml .= "<lex>";
			$xml .= "<base>{$token_tag['base']}</base>" .
					"<ctag>{$token_tag['ctag']}</ctag>" .
					"</lex>";
			$currentToken->lexemes[]=new Lexem($token_tag['disamb'], $token_tag['base'], $token_tag['ctag']);
		}
		
		//prepare channels
		foreach ($annotationIdMap as &$annotation){
			$channel = &$channels[$annotation['type']];
			if (empty($channel["elements"])){
				if($annotation["from"]<=$from && $annotation["to"]>=$to){
					$channel["elements"][]=array("num"=>1,"id"=>$annotation["id"], "from"=>$annotation["from"], "to"=>$annotation["to"]);
					$channel["counter"]=1;
					$channel["globalcounter"]++;
					if (array_key_exists("target",$annotation)) 
						$annotationIdMap[$annotation["target"]]["num"]=1;
				}
				/*else {
					$channel["elements"][]=array("num"=>0,"id"=>0, "from"=>0, "to"=>0);
					$channel["counter"]=0;
					$channel["globalcounter"]++;
				}*/
			}
			
			else {
				if($annotation["from"]<=$from && $annotation["to"]>=$to){
					$lastElem = end($channel["elements"]);
					if ($annotation["id"]==$lastElem["id"]){
						$channel["elements"][]=array("num"=>$channel["counter"],"id"=>$annotation["id"], "from"=>$annotation["from"], "to"=>$annotation["to"]);
						$channel["globalcounter"]++;
					}
					else {
						if (array_key_exists("num",$annotation)) {
							$channel["elements"][]=array("num"=>$annotation["num"],"id"=>$annotation["id"], "from"=>$annotation["from"], "to"=>$annotation["to"]);
							$channel["globalcounter"]++;							
						}
						else {
							$channel["counter"]++;
							$channel["elements"][]=array("num"=>$channel["counter"],"id"=>$annotation["id"], "from"=>$annotation["from"], "to"=>$annotation["to"]);
							$channel["globalcounter"]++;							
						}	
					}
					if (array_key_exists("target",$annotation)){ 
						$lastElem = end($channel["elements"]);
						$annotationIdMap[$annotation["target"]]["num"]=$lastElem["num"];
					}
				}
				
			}	
					
		}
		
		//echo "TOKEN {$countTokens}:\n";
		//fill with zeros && insert channels
		foreach ($channels as $annType=>&$channel){
			if ($channel["globalcounter"]<$countTokens){
				$channel["elements"][]=array("num"=>0,"id"=>0, "from"=>0, "to"=>0);
				$channel["globalcounter"]++;											
			}
			//echo "  ANNTYPE: {$annType}; COUNT: ".$channel['globalcounter']."\n";
			//echo "  AFTER ANNTYPE: {$annType}; COUNT: ".count($channel['elements'])."\n";
			$lastElem = end($channel["elements"]);
			$xml .= "<ann chan=\"$annType\" id=\"{$lastElem['id']}\" from=\"{$lastElem['from']}\" to=\"{$lastElem['to']}\">{$lastElem['num']}</ann>";
			$currentToken->channels[] = new Channel($annType,$lastElem['num']);
			if ($lastElem['num'])
				$currentSentence->channelTypes[$annType]=1;
			
		}



		//close tag and/or sentence and/or chunk
		if ($index<$lastId){
			$nextChar = $htmlStr->consumeCharacter();
			if ($nextChar!=" " && $nextChar!="<") {
				$xml .= "</tok><ns/>";
				$currentToken->ns = true;
				$currentSentence->tokens[]=$currentToken;
			}
			else {
				$xml .= "</tok>";	
				$currentSentence->tokens[]=$currentToken;
				if ($nextChar=="<"){
					$text = mb_substr($htmlStr->content, $htmlStr->n, 6);
					if (preg_match("/\/chunk/", $text)){
						$currentChunk->sentences[] = $currentSentence;
						
						
						$currentChunkList->chunks[]=$currentChunk;
						$currentChunk = new Chunk("$reportLink-$chunkNumber:$chunkNumber");
						$currentSentence = new Sentence();
						$chunkNumber++;
						$xml .= "</sentence></chunk><chunk id=\"$reportLink-$chunkNumber:$chunkNumber\"><sentence>";
						foreach ($channels as $annType=>&$channel){						
							$channel['counter']=0;
							$channel['elements']=array();
						}
					}
				} 
				else if ($token['eos']){
					$currentChunk->sentences[] = $currentSentence;
					$currentSentence = new Sentence();
					$xml .= "</sentence><sentence>";
					foreach ($channels as $annType=>&$channel){						
						$channel['counter']=0;
						$channel['elements']=array();
					}
				}
			}
		}
		else {
			$xml .= "</tok>";
			$currentSentence->tokens[]=$currentToken;
		}
		
		$countTokens++;
	}
	$currentChunk->sentences[] = $currentSentence;
	$currentChunkList->chunks[]=$currentChunk;
	
	$xml .= "</sentence></chunk></chunkList>";
	
	

	//$fileName = preg_replace("/\W/","_",$report['title'])."_".$report['id'] . ".xml"; 
	//$handle = fopen($folder . "/".$fileName ,"w");
	
	
	
	//var_dump($currentChunkList);
	fwrite($handle, $xml);
	fclose($handle);
	
	
	
	
	
	
	
	
	break;
}

?>
