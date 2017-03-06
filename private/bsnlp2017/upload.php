<?php
/*
 * Created on Jul 6, 2012
 * Skrypt do importu dokumentów TXT zgodnie z formatem z BSNLP 2017 shared task:
 * linia 1 id pliku
 * linia 2 identyfikator języka
 * linia 3 pusta
 * linia 4 URL
 * linie 5+ treść dokumentu
 */

global $config;
include("../cliopt.php");
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
ob_end_clean();

mb_internal_encoding("UTF-8");

$config->log_sql = true;
$config->log_output = "print";


//--------------------------------------------------------

//configure parameters
$opt = new Cliopt();
$opt->addExecute("php upload.php ",null);
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to documents folder (in path ccl and premorph folder)"));
$opt->addParameter(new ClioptParameter("user", "user", "id", "id of the user"));
$opt->addParameter(new ClioptParameter("status", "status", "id", "id of the document status (default 2 - przyjęty)"));

$config = new stdClass();
try {
	$opt->parseCli($argv);
	
	$dbUser = $opt->getOptional("db-user", "sql");
	$dbPass = $opt->getOptional("db-pass", "sql");
	$dbHost = $opt->getOptional("db-host", "localhost") . ":" . $opt->getOptional("db-port", "3306");
	$dbName = $opt->getOptional("db-name", "sql");
	
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
	$config->folder = $opt->getRequired("folder");
	//$config->corpus = $opt->getRequired("corpus");
	$config->subcorpus = $opt->getRequired("subcorpus");
	$config->user = $opt->getRequired("user");
	$config->status = $opt->getOptional("status","2");	
} 

catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}


/******************** main function       *********************************************/
function main ($config){
	$db = new Database($config->dsn);
	$GLOBALS['db'] = $db;	
		
	try{
		// wczytanie nazw dokumentów premorph
		$documents = array();
		if ($handle = opendir($config->folder)){
			while ( false !== ($file = readdir($handle))){
				if ( mb_substr($file, mb_strlen($file) - 4) == ".txt" ){
					$path = $config->folder . "/" . $file;
					$documents[$path] = $file;
				}
			}
		}	
		
		$subcorpus = DbCorpus::getSubcorpusById($config->subcorpus);
		$corpus_id = intval($subcorpus['corpus_id']);
		
		if ( ! $corpus_id > 0 ){
			die("Invalid corpus id: '$corpus_id'");
		}
	
		ksort($documents);
		$doc_num = 1; 
		// wczytywanie i zapis dokumentów premorph
		foreach ($documents as $path=>$file){		
			print "[".$doc_num++."/".count($documents)."] -> ".$path."\n";
			
			$title = mb_substr($file, 0, mb_strlen($file) - 4);
			$lines = file($path);
			$id = $lines[0];
			$lang = $lines[1];
			$url = $lines[3];
			$content = trim(implode("\n", array_slice($lines, 4)));
			
			$r = new CReport();
			$r->title = $title;
			$r->date = date("Y-m-d H:i:s");
			$r->source = $url;
			$r->corpora = $corpus_id;
			$r->subcorpus_id = intval($config->subcorpus);
			$r->user_id = $config->user;
			$r->status = intval($config->status);
			$r->type = 1;
			$r->format_id = 2; // plain
			$r->content = $content;
			$r->lang = $lang;
			$r->author = "";
			$r->save();
			print_r($r);
		}
	}
	catch(Exception $ex){
		echo "\n---------------------------\n";
		echo "!! Exception !! \n";
		echo $ex->getMessage();
		echo "\n---------------------------\n";
	}
}

/******************** main invoke         *********************************************/
main($config);
?>
