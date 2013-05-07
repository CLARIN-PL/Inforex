<?php
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
$opt->addExecute("php export-plain.php --corpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx",null);
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus", "corpus id (reports.corpora)"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "subcorpus", "subcorpus id (reports.subcorpus_id)"));
$opt->addParameter(new ClioptParameter("document", "d", "document", "document id (reports.id)"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to folder where generated CCL files will be saved"));

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
	    			
	$config->corpus = $opt->getParameters("corpus");
	$config->subcorpus = $opt->getParameters("subcorpus");
	$config->documents = $opt->getParameters("document");
	
	if (!$config->corpus && !$config->subcorpus && !$config->documents)
		throw new Exception("No corpus, subcorpus nor document set");
		
	$config->folder = $opt->getRequired("folder");	
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

//--------------------------------------------------------
$db = new Database($config->dsn);

$reports = DbReport::getReports($config->corpus,$config->subcorpus,$config->documents);
foreach ($reports as $r){
  $filename = $config->folder . "/" . str_pad($r['id'],8,"0", STR_PAD_LEFT) . ".txt";
  $content = $r['content'];
  $content = trim(strip_tags($content));
  file_put_contents($filename, $content);
  echo $filename . "\n";
}

?>
