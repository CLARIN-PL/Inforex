<?php
/* 
 * ---
 * Insert tag <sentence> into document
 * ---
 * Created on 2012-02-13 
 */
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
	
	$comment = "Dodanie znaczników <sentence>";
	
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id     \n";
		ob_flush();
		$content_before = $ids[$report_id]['content'];
		
		$sql = "SELECT * FROM tokens t WHERE t.report_id=" . $report_id . " AND t.eos=1" ;
		$tokens = $db->fetch_rows($sql);

		$remove_sentence_tag = preg_replace("[</sentence>]","", $content_before);
		$remove_sentence_tag = preg_replace("[<sentence>]","", $remove_sentence_tag);
		
		$htmlStr =  new HtmlStr($remove_sentence_tag, true);
		$tag_from = 0;
		foreach($tokens as $token){
			$htmlStr->insertTag($tag_from, "<sentence>", $token['to']+1, "</sentence>");
			$tag_from = $token['to']+1;
		}
		
		$df = new DiffFormatter();
		$diff = $df->diff($content_before, $htmlStr->getContent(), true);
		if ( trim($diff) != "" ){
			$report = new CReport($report_id);			
			$report->content = $htmlStr->getContent();
			$report->save();
			$deflated = gzdeflate($diff);
			$data = array(date("Y-m-d H:i:s"), 1 , $report_id, $deflated, $comment);
			$sql = "INSERT INTO reports_diffs (`datetime`, `user_id`, `report_id`, `diff`, `comment`) VALUES(?, ?, ?, ?, ?)";
			$db->execute($sql,$data);
		}
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>
