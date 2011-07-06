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
	
	foreach ($results as $result){
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
	foreach ($annotations as $annotation){
		$channels[$annotation['type']]=array();
		$annotationIdMap[$annotation['id']][]=$annotation;
		
	}
	$sql = "SELECT * " .
			"FROM relations " .
			"WHERE source_id " .
			"IN (".implode(",",array_keys($annotationIdMap)).") " .
			"AND relation_type_id=1";
	$continuousRelations = db_fetch_rows($sql);
	foreach ($continuousRelations as $relation){
		$annotationIdMap[$relation['source_id']]['target']=$annotationIdMap[$relation['target_id']];
		$annotationIdMap[$relation['target_id']]['source']=$annotationIdMap[$relation['source_id']];
	}			
	
	//var_dump($annotationIdMap);	
	
	
	$htmlStr = new HtmlStr($report['content']);
	$chunkNumber = 1;
	$reportLink = str_replace(".xml","",$report['link']);
	$xml = "<chunkList><chunk id=\"$reportLink-$chunkNumber:$chunkNumber\"><sentence>"; 
	$ns = false;
	$lastId = count($tokens)-1;
	foreach ($tokens as $index => $token){
		$id = $token['token_id'];
		$from = $token['from'];
		$to = $token['to'];
		$xml .= "<tok>";
		$xml .= "<orth>{$htmlStr->getText($from,$to)}</orth>";
		//insert lex
		foreach ($tokens_tags[$id] as $token_tag){
			if ($token_tag['disamb']==1)
				$xml .= "<lex disamb=\"1\">";
			else 
				$xml .= "<lex>";
			$xml .= "<base>{$token_tag['base']}</base>" .
					"<ctag>{$token_tag['ctag']}</ctag>" .
					"</lex>";
		}
		
		//insert channels
		if ($annotations){
		}
		
		//close tag and/or sentence and/or chunk
		if ($index<$lastId){
			$nextChar = $htmlStr->consumeCharacter();
			if ($nextChar!=" " && $nextChar!="<") $xml .= "</tok><ns/>";
			else {
				$xml .= "</tok>";	
				if ($nextChar=="<"){
					$text = mb_substr($htmlStr->content, $htmlStr->n, 6);
					if (preg_match("/\/chunk/", $text)){
						$chunkNumber++;
						$xml .= "</sentence></chunk><chunk id=\"$reportLink-$chunkNumber:$chunkNumber\"><sentence>";
					}
				} 
				else if ($token['eos']){
					$xml .= "</sentence><sentence>";
				}
			}
		}
		else $xml .= "</tok>";
	}
	$xml .= "</sentence></chunk></chunkList>";
	fwrite($handle, $xml);
	fclose($handle);
	//var_dump($htmlStr);
	break;
}

?>
