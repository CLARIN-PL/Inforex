<?php
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
include("../cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("report", "r", "id", "id of the report"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));

/******************** parse cli *********************************************/

try{
	$opt->parseCli($argv);
	
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
	$mdb2 =& MDB2::singleton($config->dsn, $options);
	db_execute("SET CHARACTER SET utf8");
		
	$config->corpus = $opt->getOptionalParameters("corpus");
	$config->subcorpus = $opt->getOptionalParameters("subcorpus");
	$config->report = $opt->getOptionalParameters("report");
	if (!$config->corpus && !$config->subcorpus && !$config->report)
		throw new Exception("No corpus, subcorpus nor report id set");
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

$db = new Database($config->dsn);

/******************** main function       *********************************************/

function main ($config){
	global $db;
	$ids = array();
	
	foreach(DbReport::getReports($config->corpus,$config->subcorpus,$config->report, null) as $row){
		$ids[$row['id']] = $row;
	}
	
	
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id     \n";
		ob_flush();
		
		$chunkList = explode('</chunk>', $ids[$report_id]['content']);
		$chunks = array();
	
		$from = 0;
		$to = 0;
		foreach ($chunkList as $chunk){
			$chunk = str_replace("<"," <",$chunk);
			$chunk = str_replace(">","> ",$chunk);
			$tmpStr = trim(preg_replace("/\s\s+/"," ",custom_html_entity_decode(strip_tags($chunk))));
			$tmpStr2 = preg_replace("/\n+|\r+|\s+/","",$tmpStr);
			$to = $from + mb_strlen($tmpStr2)-1;
			$chunks[]=array(
				"notags" => $tmpStr,
				"nospace" => $tmpStr2,
				"from" => $from,
				"to" => $to
			);
			$sql = "UPDATE tokens t SET t.eos=1 WHERE t.report_id=" . $report_id . " AND t.to=" . $to;
			$db->execute($sql);
//			$sql = "SELECT * FROM tokens t WHERE t.report_id=" . $report_id . " AND t.to=" . $to;
//			$doc = $db->fetch_rows($sql);
//			print_r($doc);
//			echo " ";
//			ob_flush();
			$from = $to+1;		
		}
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>
