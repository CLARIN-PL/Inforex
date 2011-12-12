<?php
require_once("../cliopt.php");
require_once("PEAR.php");
require_once("MDB2.php");
mb_internal_encoding("UTF-8");
$opt = new Cliopt();
$opt->addExecute("php wsd-annotate.php --corpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx",null);
$opt->addExecute("php wsd-annotate.php --subcorpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx",null);
$opt->addParameter(new ClioptParameter("report", "r", "report_id", "report id"));
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus_id", "corpus id"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "subcorpus_id", "subcorpus id"));
$opt->addParameter(new ClioptParameter("disamb", "d", null, "consider disamb only"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("user", null, "userid", "user id"));
$config = null;
try {
	$opt->parseCli($argv);
	
	$dbHost = "localhost";
	$dbUser = "root";
	$dbPass = null;
	$dbName = "gpw";
	$dbPort = "3306";

	if ( $opt->exists("db-uri")){
		$uri = $opt->getRequired("db-uri");
		if ( preg_match("/(.+):(.+)@(.*):(.*)\/(.*)/", $uri, $m)){
			$dbUser = $m[1];
			$dbPass = $m[2];
			$dbHost = $m[3];
			$dbPort = $m[4];
			$dbName = $m[5];
		}else{
			throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
		}
	}
	
	$dbHost = $opt->getOptional("db-host", $dbHost);
	$dbUser = $opt->getOptional("db-user", $dbUser);
	$dbPass = $opt->getOptional("db-pass", $dbPass);
	$dbName = $opt->getOptional("db-name", $dbName);
	$dbPort = $opt->getOptional("db-port", $dbPort);

	$config->dsn['phptype'] = 'mysql';
	$config->dsn['username'] = $dbUser;
	$config->dsn['password'] = $dbPass;
	$config->dsn['hostspec'] = $dbHost . ":" . $dbPort;
	$config->dsn['database'] = $dbName;
		    				
	$user_id = $opt->getOptional("user", "1");
	$report_id = $opt->getOptional("report", "-1");
	$corpus_id = $opt->getOptional("corpus", "-1");
	$subcorpus_id = $opt->getOptional("subcorpus", "-1");
	$config->disamb = $opt->exists("disamb");
	if (!$corpus_id && !$subcorpus_id && !$report_id)
		throw new Exception("No corpus, subcorpus nor report id set");	
//	else if ($corpus_id && $subcorpus_id)
//		throw new Exception("Set only one parameter: corpus or subcorpus");
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}
include("../../engine/database.php");

$stats = array();

$wsdTypes = db_fetch_rows("SELECT * FROM `annotation_types` WHERE name LIKE 'wsd_%'");
$reportArray = array();
foreach ($wsdTypes as $wsdType){
	$base = substr($wsdType['name'],4);	
	$sql = "SELECT r.id, r.content, t.from, t.to " . 
			"FROM reports r " .
			"JOIN tokens t " .
				"ON (" .
					"(r.corpora=$corpus_id " .
					"OR r.id=$report_id " .
					"OR r.subcorpus_id=$subcorpus_id) " .
					"AND r.id=t.report_id" .
				") " .
			"JOIN tokens_tags tt " .
				"ON (" .
					"tt.base='$base' " .
					( $config->disamb ? "AND tt.disamb=1 " : "" ) .
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
			
			$stats[$wsdType['name']]++;
		}
	}	
}
print_r($stats);

$ids = array();

$sql = sprintf("SELECT * FROM reports WHERE corpora = %d", $corpus_id);
foreach ( db_fetch_rows($sql) as $r ){
	$ids[$r['id']] = 1;			
}		

$sql = sprintf("SELECT * FROM reports WHERE subcorpus_id = %d", $subcorpus_id);
foreach ( db_fetch_rows($sql) as $r ){
	$ids[$r['id']] = 1;			
}		

$ids[$report_id] = 1;

foreach ( array_keys($ids) as $report_id){
	$doc = db_fetch("SELECT * FROM reports WHERE id=?",array($report_id));	
	set_status_if_not_ready($doc['corpora'], $report_id, "WSD", 1);	
}


/*** aux functions */

function set_status_if_not_ready($corpora_id, $report_id, $flag_name, $status){
	$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = ?";
	$corpora_flag_id = db_fetch_one($sql, array($corpora_id, $flag_name));

	if ($corpora_flag_id){
		if ( !db_fetch_one("SELECT flag_id FROM reports_flags WHERE corpora_flag_id = ? AND report_id = ?",
							array($corpora_flag_id, $report_id) ) > 0 ){
			db_execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, ?)",
				array($corpora_flag_id, $report_id, $status));
		}	
	}	
	
}
?>
