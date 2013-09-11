<?php
/*
 * Jan Kocoń <janek.kocon@gmail.com>
 */
global $config;
include("../cliopt.php");
include("../../engine/config.php");
//include("../../engine/config.local.php");
include("../../engine/include.php");

mb_internal_encoding("UTF-8");

//--------------------------------------------------------
//configure parameters
$opt = new Cliopt();
$opt->addExecute("php export-ccl.php --corpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx --annotation_layer n --annotation_name xxx --flag xxx=yy",null);
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus", "corpus id (reports.corpora)"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "subcorpus", "subcorpus id (reports.subcorpus_id)"));
$opt->addParameter(new ClioptParameter("document", "d", "document", "document id (reports.id)"));
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to folder where generated CCL files will be saved"));
$opt->addParameter(new ClioptParameter("annotation_layer", "l", "id", "export annotations assigned to layer 'id' |(multiple; ref: annotation_types.group_id)"));
$opt->addParameter(new ClioptParameter("annotation_name", "a", "name", "export annotations assigned to type 'name' |(multiple; ref: reports_annotations.type)"));
$opt->addParameter(new ClioptParameter("export-metadata", "M", null, "export document metadata"));
$opt->addParameter(new ClioptParameter("export-content", "C", null, "export document contents"));
$opt->addParameter(new ClioptParameter("flag", "F", "flag", "export using flag \"flag name\"=flag_value or \"flag name\"=flag_value1,flag_value2,..."));
$opt->addParameter(new ClioptParameter("iob", null, "iob_file_name", "save documents to iob_file_name in iob format"));
$opt->addParameter(new ClioptParameter("index", "i", "flag", "create files index_FLAG.txt with relative paths to exported ccl files| (flag can be corpora_flags.corpora_flag_id or corpora_flags.short)"));
$opt->addParameter(new ClioptParameter("no-disamb", null, null, "do not export the disamb information"));
$opt->addParameter(new ClioptParameter("one-by-one", "o", null, "export document one by one"));
$opt->addParameter(new ClioptParameter("relation", "r", "id", "export relations assigned to type 'id' |(parameter can be set many times) (relation_types.id)"));
$opt->addParameter(new ClioptParameter("relation_set", "R", "id", "export relations assigned to relation_set 'id' |(parameter can be set many times) (relation_sets.relation_set_id)"));
$opt->addParameter(new ClioptParameter("seprel", null, null, "save relations in separated files"));
$opt->addParameter(new ClioptParameter("split", null, null, "store documents in subcorpus folders"));
$opt->addParameter(new ClioptParameter("stage", null, "type", "export annotations assigned to stage 'type' |(parameter can be set many times)"));


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
	$index_flags = null;
	if ( $opt->exists("index"))
		$index_flags = $opt->getParameters("index");	
	$folder = $opt->getRequired("folder");
	$annotation_layers = $opt->getOptionalParameters("annotation_layer");
	$annotation_names = $opt->getOptionalParameters("annotation_name");
	$stages = $opt->getOptionalParameters("stage");
	$relation_set_ids = $opt->getOptionalParameters("relation_set");	
	$relation_type_ids = $opt->getOptionalParameters("relation");
			
	//force continuous relations
	if (!$relation_type_ids || (!empty($relation_type_ids) && !in_array(1,$relation_type_ids) ))
		$relation_type_ids[] = 1;
	
	$split_documents = $opt->exists("split");	
	$separate_relations = $opt->exists("seprel");
	$metadata = $opt->exists("export-metadata");
	$content = $opt->exists("export-content");
	$no_disamb = $opt->exists("no-disamb");
	
	$iob_file_name = $opt->getOptionalParameters("iob");
	if (count($iob_file_name))
		$iob_file_name = $iob_file_name[0];
	else 	
		$iob_file_name = null;

	
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

//--------------------------------------------------------
$db = new Database($config->dsn);


if ( $opt->exists("one-by-one") ){
	$reports = DbReport::getReports($corpus_ids, $subcorpus_ids, $document_ids, $flags);
	$i = 1;
	foreach ($reports as $r){
		echo sprintf("Processing %d z %d (id=%d)\n", $i++, count($reports), $r['id']);
		
		$exporter = new ExportManager();
		$exporter->setVerbose(false);
		$exporter->setDb($db);
		$exporter->setDocumentIds(array($r['id']));
		$exporter->setAnnotationLayers($annotation_layers);
		$exporter->setAnnotationNames($annotation_names);
		$exporter->setRelationSetIds($relation_set_ids);
		$exporter->setRelationTypeIds($relation_type_ids);
		
		$exporter->setFolder($folder);
		$exporter->setSplit($split_documents);
		$exporter->setNoDisamb($no_disamb);
		$exporter->setSeparateRelations($separate_relations);
		
		$exporter->setIob($iob_file_name);

		$exporter->readDocuments();
		
		if ( $content ){
			$exporter->readContent();
			$exporter->setVerbose(true);
			$exporter->processContent();
			$exporter->setVerbose(false);		
			$exporter->writeContent();
		}
		
		if ( $metadata ){
			$exporter->readMetadata();
			$exporter->writeMetadata();
		}
	}
}
else{
	ob_end_flush();
	$exporter = new ExportManager();
	$exporter->setVerbose(true);
	$exporter->setDb($db);
	$exporter->setCorpusIds($corpus_ids);
	$exporter->setSubcorpusIds($subcorpus_ids);
	$exporter->setDocumentIds($document_ids);
	$exporter->setAnnotationLayers($annotation_layers);
	$exporter->setAnnotationNames($annotation_names);
	$exporter->setRelationSetIds($relation_set_ids);
	$exporter->setRelationTypeIds($relation_type_ids);
	
	$exporter->setFolder($folder);
	$exporter->setFlags($flags);
	$exporter->setSplit($split_documents);
	$exporter->setNoDisamb($no_disamb);
	$exporter->setSeparateRelations($separate_relations);
	
	$exporter->setIob($iob_file_name);
	$exporter->setIndexFlags($index_flags);
	
	$exporter->readDocuments();
	
	if ( $content ){
		$exporter->readContent();
		$exporter->processContent();
		$exporter->writeContent();
	}
	if ( $metadata ){
		$exporter->readMetadata();
		$exporter->writeMetadata();
	}
	if ( $index_flags ){
		$exporter->processIndexes();
		$exporter->writeIndexes();				
	}
	
}
?>
