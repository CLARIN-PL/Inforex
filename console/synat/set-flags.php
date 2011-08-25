<?php
require_once("../cliopt.php");
require_once("PEAR.php");
require_once("MDB2.php");
mb_internal_encoding("UTF-8");

$opt = new Cliopt();
$opt->addExecute("php set-flags.php --document n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx",null);
$opt->addExecute("php set-flags.php --subcorpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx",null);
$opt->addExecute("php set-flags.php --corpus n --user u --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx",null);
$opt->addParameter(new ClioptParameter("doument", "d", "report_id", "report id"));
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus_id", "corpus id"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "subcorpus_id", "subcorpus id"));
$opt->addParameter(new ClioptParameter("db-host", "h", "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", "P", "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", "u", "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", "p", "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", "n", "name", "database name"));
$opt->addParameter(new ClioptParameter("flag", "f", "flag name", "flag name"));
$opt->addParameter(new ClioptParameter("status", "v", "id", "flag status id"));
$opt->addParameter(new ClioptParameter("init", null, null, "init only not set flags"));
$config = null;
try {
	$opt->parseCli($argv);
	$config->dsn = array(
	    			'phptype'  => 'mysql',
	    			'username' => $opt->getOptional("db-user", "root"),
	    			'password' => $opt->getOptional("db-pass", "krasnal"),
	    			'hostspec' => $opt->getOptional("db-host", "localhost") . ":" . $opt->getOptional("db-port", "3306"),
	    			'database' => $opt->getOptional("db-name", "gpw"));	
	$config->corpus = $opt->getParameters("corpus");
	$config->subcorpus = $opt->getParameters("subcorpus");
	$config->documents = $opt->getParameters("document");
	$config->flag = $opt->getOptional("flag", null);
	$config->status = $opt->getOptional("status", null);
	$config->init = $opt->exists("init");
	
	if ( count($config->corpus) == 0 && count($config->subcorpus) == 0 && count($config->documents) == 0 )
		throw new Exception("No corpus, subcorpus nor report id set");
		
	if ( !$config->init )
		throw new Exception("Use -init");
		
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}
include("../../engine/database.php");
ob_end_clean();

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$ids = array();
	$n = 0;
	
	foreach ($config->corpus as $c){
		$sql = sprintf("SELECT * FROM reports WHERE corpora = %d", $c);
		foreach ( db_fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}

	foreach ($config->subcorpus as $s){
		$sql = sprintf("SELECT * FROM reports WHERE subcorpus_id = %d", $s);
		foreach ( db_fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}
	
	foreach ($config->documents as $d){
		$ids[$d] = 1;
	}
	
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id     ";
			
		$doc = db_fetch("SELECT * FROM reports WHERE id=?",array($report_id));
		
		if ( $config->init )
			init_flag_status($doc['corpora'], $report_id, $config->flag, $config->status);
	}
	
} 


/******************** aux function        *********************************************/
/**
 * Set status if not initiated
 */
function init_flag_status($corpora_id, $report_id, $flag_name, $status){
	$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = ?";
	$corpora_flag_id = db_fetch_one($sql, array($corpora_id, $flag_name));

	if ($corpora_flag_id){
		$value = intval(db_fetch_one("SELECT flag_id FROM reports_flags WHERE corpora_flag_id = ? AND report_id = ?",
							array($corpora_flag_id, $report_id) ) ); 
		if ( $value == -1 || $value == 0 ){
			db_execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, ?)",
				array($corpora_flag_id, $report_id, $status));
		}	
	}	
	
}

/******************** main invoke         *********************************************/
main($config);

echo "done ■\n";
	
?>
