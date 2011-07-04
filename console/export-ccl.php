<?php
include("cliopt.php");
include("../engine/include/lib_htmlstr.php");
require_once("PEAR.php");
require_once("MDB2.php");
mb_internal_encoding("UTF-8");
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
	$sql = "SELECT * " .
			"FROM tokens " .
			"WHERE report_id={$report['id']}";
	$tokens = db_fetch_rows($sql);
	
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
	
	foreach ($results as $result){
		$tokens_tags[$result['token_id']][]=$result;
	}
	$htmlStr = new HtmlStr($report['content']);
	$xml = '<chunkList>';
	$openChunk = true;
	$chunkNumber = 0;
	$openSentence = true;
	$reportLink = str_replace(".xml","",$report['link']);
	$ns = false;
	$lastId = count($tokens)-1;
	foreach ($tokens as $index => $token){
		$chunkNumber++;
		$id = $token['token_id'];
		$from = $token['from'];
		$to = $token['to'];
		if ($openChunk) {
			$xml .= "<chunk id=\"$reportLink-$chunkNumber:$chunkNumber\">";
			$openChunk=false;	
		}
		if ($openSentence){
			$xml .= "<sentence>";
			$openSentence = false;
		}
		$xml .= "<tok>";
		$xml .= "<orth>{$htmlStr->getText($from,$to)}</orth>";
		$htmlStr->moveTo($from);		
		$prevTo = $htmlStr->n;		
		foreach ($tokens_tags[$id] as $token_tag){
			if ($token_tag['disamb']==1)
				$xml .= "<lex disamb=\"1\">";
			else 
				$xml .= "<lex>";
			$xml .= "<base>{$token_tag['base']}</base>";
			$xml .= "<ctag>{$token_tag['ctag']}</ctag>";
			$xml .= "</lex>";
		}
		if ($index<$lastId){
			$nextChar = $htmlStr->consumeCharacter();
			if ($nextChar!=" " && $nextChar!="<") $xml .= "</tok><ns/>";
			else $xml .= "</tok>";
			$htmlStr->moveTo($tokens[$index+1]['to']);
			$nextTo = $htmlStr->n;
			$text = mb_substr($htmlStr->content, $prevTo, $nextTo-$prevTo+1);
		/*	if (preg_match("/\<chunk\/\>/", $text)){
				$xml .= "</sentence></chunk><chunk><sentence>";
			}*/
			echo $text . "\n";		
		}
		else $xml .= "</tok>";
		
	}
	$xml .= "</sentence></chunk></chunkList>";
	fwrite($handle, $xml);
	fclose($handle);
	


	echo $report['id'] . "\n";
	$htmlStr->getText(7,14);	
	echo "|" .$htmlStr->consumeCharacter(). "|";
	//var_dump($content);
	break;
}



/*$wsdTypes = db_fetch_rows("SELECT * FROM `annotation_types` WHERE name LIKE 'wsd_%'");
$reportArray = array();
foreach ($wsdTypes as $wsdType){
	$base = substr($wsdType['name'],4);	
	$sql = "SELECT r.id, r.content, t.from, t.to " . 
			"FROM reports r " .
			"JOIN tokens t " .
				"ON (" .
					"(r.corpora=$corpus_id " .
					"OR r.subcorpus_id=$subcorpus_id) " .
					"AND r.id=t.report_id" .
				") " .
			"JOIN tokens_tags tt " .
				"ON (" .
					"tt.base='$base' " .
					"AND tt.disamb=1 " .
					"AND t.token_id=tt.token_id" .
				")";
	$tokens = db_fetch_rows($sql);
	foreach ($tokens as $token){
		$text = preg_replace("/\n+|\r+|\s+/","",html_entity_decode(strip_tags($token['content'])));
		$annText = mb_substr($text, intval($token['from']), intval($token['to'])-intval($token['from'])+1);
		$sql = "SELECT id " .
				"FROM reports_annotations " .
				"WHERE `report_id`=" .$token['id'].
				"  AND `type`='" .$wsdType['name'].
				"' AND `from`=" .$token['from'].
				"  AND `to`=" .$token['to'].
				"  LIMIT 1";
		$result = db_fetch_one($sql);
		
		if (!$result){
			$sql = "INSERT INTO reports_annotations " .
					"(`report_id`," .
					"`type`," .
					"`from`," .
					"`to`," .
					"`text`," .
					"`user_id`," .
					"`creation_time`," .
					"`stage`," .
					"`source`) " .
					"VALUES (".$token['id'] .
						  ",'".$wsdType['name'] .
						  "',".$token['from'] .
						   ",".$token['to'] .
						    ",'$annText',$user_id,now(),'final','auto')";
			db_execute($sql);
		}
	}	
}
*/
?>
