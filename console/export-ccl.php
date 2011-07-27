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
		$this->base = $base;
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
		$this->orth = $orth;
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
		return $xml . "   </tok>\n";		
			
	}
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
		$xml = "  <sentence>\n";
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


//get parameters & set db configuration
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
//get reports
$sql = "SELECT * FROM reports WHERE corpora=$corpus_id OR subcorpus_id=$subcorpus_id ";
$reports = db_fetch_rows($sql);

foreach ($reports as $report){
	//print $report['id'] . "\n";
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

	//create maps
	$channels = array();
	$annotationIdMap = array();
	$annotationChannelMap = array();
	foreach ($annotations as &$annotation){
		$channels[$annotation['type']]=array("counter"=>0, "elements"=>array(), "globalcounter"=>0);
		$annotationIdMap[$annotation['id']]=$annotation;
	}
	
	//var_dump($annotationIdMap);
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
	$htmlStr = new HtmlStr($report['content']);
	$chunkNumber = 1;
	$reportLink = str_replace(".xml","",$report['link']);
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
		$currentToken = new Token($htmlStr->getText($from,$to));

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
						$channel["globalcounter"]++;
					}
					else {
						//check continuous relation
						if (array_key_exists("num",$annotation)) {
							$channel["elements"][]=array("num"=>$annotation["num"],"id"=>$annotation["id"]);
							$channel["globalcounter"]++;							
						}
						else {
							$channel["counter"]++;
							$channel["elements"][]=array("num"=>$channel["counter"],"id"=>$annotation["id"]);
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
			$nextChar = $htmlStr->consumeCharacter();
			if ($nextChar!=" " && $nextChar!="<") {
				$currentToken->ns = true;
				$currentSentence->tokens[]=$currentToken;
			}
			else {
				$currentSentence->tokens[]=$currentToken;
				if ($nextChar=="<"){
					$text = mb_substr($htmlStr->content, $htmlStr->n, 6);
					if (preg_match("/\/chunk/", $text)){
						$chunkNumber++;
						$currentChunk->sentences[] = $currentSentence;
						$currentChunkList->chunks[]=$currentChunk;
						$currentChunk = new Chunk("$reportLink-$chunkNumber:$chunkNumber");
						$currentSentence = new Sentence();
						foreach ($channels as $annType=>&$channel){						
							$channel['counter']=0;
							$channel['elements']=array();
						}
					}
				} 
				else if ($token['eos']){
					$currentChunk->sentences[] = $currentSentence;
					$currentSentence = new Sentence();
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
	
	//save to file
	$fileName = preg_replace("/\W/","_",$report['title'])."_".$report['id'] . ".xml"; 
	//$fileName = $report['link'];
	$handle = fopen($folder . "/".$fileName ,"w");
	fwrite($handle, $currentChunkList->getXml());
	fclose($handle);
	//}
	//break;
}

?>
