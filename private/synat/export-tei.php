<?php
/*
 * Created on Apr 30, 2012
 * 
 */
 
global $config;
include("../cliopt.php");
//include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
ob_end_clean();

mb_internal_encoding("UTF-8");

//--------------------------------------------------------



//configure parameters
$opt = new Cliopt();
$opt->addExecute("php export-ccl.php --corpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx --annotation_layer n --annotation_name xxx --flag xxx=yy",null);
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus", "corpus id (reports.corpora)"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "subcorpus", "subcorpus id (reports.subcorpus_id)"));
$opt->addParameter(new ClioptParameter("document", "d", "document", "document id (reports.id)"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to folder where generated TEI files will be saved"));
$opt->addParameter(new ClioptParameter("annotation_layer", "l", "id", "export annotations assigned to layer 'id' (parameter can be set many times) (annotation_types.group_id)"));
$opt->addParameter(new ClioptParameter("annotation_name", "a", "name", "export annotations assigned to type 'name' (parameter can be set many times) (reports_annotations.type)"));
$opt->addParameter(new ClioptParameter("stage", null, "type", "export annotations assigned to stage 'type' (parameter can be set many times)"));
$opt->addParameter(new ClioptParameter("relation", "r", "id", "export relations assigned to type 'id' (parameter can be set many times) (relation_types.id)"));
$opt->addParameter(new ClioptParameter("relation_set", "relation_set", "id", "export relations assigned to relation_set 'id' (parameter can be set many times) (relation_sets.relation_set_id)"));
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
		
	$flags = null;
	if ( $opt->exists("flag")){
		$flags = array();
		$flag = $opt->getParameters("flag");
		foreach($flag as $f){
			if ( preg_match("/(.+)=(.+)/", $f, $n)){
				$flag_name = $n[1];
				if (!array_key_exists($flag_name, $flags)){
					$flags[$flag_name]=array();
				}
				if ( preg_match_all("/(?P<digit>\d+)/", $n[2], $v)){
					foreach($v['digit'] as $key => $digit)
						$flags[$flag_name][]=$digit;
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
	//force continuous relations
	if (!$relation_type_ids || (!empty($relation_type_ids) && !in_array(1,$relation_type_ids) ))
		$relation_type_ids[] = 1;
	
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

//--------------------------------------------------------
$db = new Database($config->dsn);

$cclSetFactory = new CclSetFactory();
$cclSetFactory->setDb($db);
$cclSetFactory->setCorpusIds($corpus_ids);
$cclSetFactory->setSubcorpusIds($subcorpus_ids);
$cclSetFactory->setDocumentIds($document_ids);
$cclSetFactory->setAnnotationLayers($annotation_layers);
$cclSetFactory->setAnnotationNames($annotation_names);
$cclSetFactory->setRelationSetIds($relation_set_ids);
$cclSetFactory->setRelationTypeIds($relation_type_ids);

$cclSetFactory->setFolder($folder);
$cclSetFactory->setFlags($flags);

$cclSetFactory->acquireData();
$cclSetFactory->create();

if (!is_dir($folder)) mkdir($folder, 0777);
$tei = new TeiWriter();
foreach($cclSetFactory->cclDocuments as $docName=>$cclDocument){
	$tei->ccl2teiWrite($cclDocument, $folder, $cclDocument->getFileName(), $cclSetFactory->reports[$docName]['tokenization']);
}

?>
