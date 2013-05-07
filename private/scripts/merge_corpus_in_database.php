<?php
/**
 * Dla wersji bazy danych z dnia 27.06.2012
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
$opt->addExecute("php export-ccl.php --corpus1 n1 --corpus2 n2 --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx ",null);
$opt->addParameter(new ClioptParameter("corpus1", "c1", "corpus1", "corpus 1 id"));
$opt->addParameter(new ClioptParameter("corpus2", "c2", "corpus2", "corpus 2 id"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));

$config = null;
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
	    			
	$config->corpus_1_id = $opt->getRequired("corpus1");
	$config->corpus_2_id = $opt->getRequired("corpus2");
	
	if (!$config->corpus_1_id && !$config->corpus_2_id)
		throw new Exception("corpus 1 id or corpus 2 id not set");
	
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
		// ----------- flagi
		$flag_in_corpus = array();
		$flag_in_corpus[$config->corpus_1_id] = array();
		$flag_in_corpus[$config->corpus_2_id] = array();
		$corpora_flags = $db->fetch_rows("SELECT * FROM `corpora_flags` WHERE `corpora_id` IN (".$config->corpus_1_id.", ".$config->corpus_2_id.")");
		foreach($corpora_flags as $flag){
			$flag_in_corpus[$flag['corpora_id']][$flag['corpora_flag_id']] = $flag['name'];
		}
		foreach($flag_in_corpus[$config->corpus_1_id] as $flag_id=>$flag_name){
			if(!in_array($flag_name, $flag_in_corpus[$config->corpus_2_id])){
				throw new Exception("Brak flagi {$flag_name} w korpusie {$config->corpus_2_id}");
			}
		}
		$flag_merge = array();
		foreach($flag_in_corpus[$config->corpus_2_id] as $flag_id=>$flag_name){
			if(in_array($flag_name, $flag_in_corpus[$config->corpus_1_id])){
				$flag_merge[$flag_id] = array_search($flag_name,$flag_in_corpus[$config->corpus_1_id]);
			}
			else{
				throw new Exception("Brak flagi {$flag_name} w korpusie {$config->corpus_1_id}");
			}
			
		}
		
		// ----------- podkorpusy 
		$subcorpus_in_corpus = array();
		$subcorpus_in_corpus[$config->corpus_1_id] = array();
		$subcorpus_in_corpus[$config->corpus_2_id] = array();
		$corpus_subcorpora = $db->fetch_rows("SELECT * FROM `corpus_subcorpora` WHERE `corpus_id` IN (".$config->corpus_1_id.", ".$config->corpus_2_id.")");
		foreach($corpus_subcorpora as $subcorpus){
			$subcorpus_in_corpus[$subcorpus['corpus_id']][$subcorpus['subcorpus_id']] = $subcorpus['name'];
		}
		$subcorpus_merge = array();
		$subcorpus_update = array();
		foreach($subcorpus_in_corpus[$config->corpus_2_id] as $subcorpus_id=>$subcorpus_name){
			if(in_array($subcorpus_name, $subcorpus_in_corpus[$config->corpus_1_id])){
				$subcorpus_merge[$subcorpus_id] = array_search($subcorpus_name,$subcorpus_in_corpus[$config->corpus_1_id]);
			}
			else{
				$subcorpus_update[] = $subcorpus_id;
			}
			
		}

		// ----------- poziomy anotacji 
		$annotation_sets_in_corpus = array();
		$annotation_sets_in_corpus[$config->corpus_1_id] = array();
		$annotation_sets_in_corpus[$config->corpus_2_id] = array();
		$annotation_sets_corpora = $db->fetch_rows("SELECT * FROM `annotation_sets_corpora` WHERE `corpus_id` IN (".$config->corpus_1_id.", ".$config->corpus_2_id.")");
		foreach($annotation_sets_corpora as $annotation){
			$annotation_sets_in_corpus[$annotation['corpus_id']][] = $annotation['annotation_set_id'];
		}
		$annotation_sets_corpora_update = array();
		foreach($annotation_sets_in_corpus[$config->corpus_2_id] as $annotation_set_id){
			if(!in_array($annotation_set_id, $annotation_sets_in_corpus[$config->corpus_1_id])){
				$annotation_sets_corpora_update[] = $annotation_set_id;
			}
			
		}

	  	execute_sql($db, "START TRANSACTION");
	  	execute_sql($db, "BEGIN");
	  	
	  	foreach($flag_merge as $from=>$to){
	  		execute_sql($db, "UPDATE `reports_flags` SET `corpora_flag_id` = ".$to." WHERE `corpora_flag_id` = ".$from."");
	  		execute_sql($db, "DELETE FROM `corpora_flags` WHERE `corpora_flag_id` = ".$from."");
	  	}
	  	
	  	execute_sql($db, "UPDATE `reports` SET `corpora` = ".$config->corpus_1_id." WHERE `corpora` = ".$config->corpus_2_id."");

	  	foreach($subcorpus_merge as $from=>$to){
	  		execute_sql($db, "UPDATE `reports` SET `subcorpus_id` = ".$to." WHERE `subcorpus_id` = ".$from."");
			execute_sql($db, "DELETE FROM `corpus_subcorpora` WHERE `subcorpus_id` = ".$from."");
	  	}

	  	foreach($subcorpus_update as $subcorpus){
	  		execute_sql($db, "UPDATE `corpus_subcorpora` SET `corpus_id` = ".$config->corpus_1_id." WHERE `subcorpus_id` = ".$subcorpus."");
	  	}
	  	
	  	foreach($annotation_sets_corpora_update as $annotation_set){
	  		execute_sql($db, "UPDATE `annotation_sets_corpora` SET `corpus_id` = ".$config->corpus_1_id." WHERE (`annotation_set_id` = ".$annotation_set." AND `corpus_id` = ".$config->corpus_2_id.")");
	  	}
	  	
	  	execute_sql($db, "DELETE FROM `annotation_sets_corpora` WHERE `corpus_id` = ".$config->corpus_2_id."");
	  	execute_sql($db, "DELETE FROM `users_corpus_roles` WHERE `corpus_id` = ".$config->corpus_2_id."");
		execute_sql($db, "DELETE FROM `corpus_perspective_roles` WHERE `corpus_id` = ".$config->corpus_2_id."");
		execute_sql($db, "DELETE FROM `corpus_and_report_perspectives` WHERE `corpus_id` = ".$config->corpus_2_id."");
		execute_sql($db, "DELETE FROM `corpora` WHERE `id` = ".$config->corpus_2_id."");

	  	execute_sql($db, "COMMIT");
	}
	catch(Exception $ex){
		$db->execute("ROLLBACK");
		echo "\n---------------------------\n";
		echo "!! Exception !! \n";
		echo $ex->getMessage();
		echo "\n---------------------------\n";
	}
}

function execute_sql($db, $sql){
	print $sql."\n";
  	$db->execute($sql);
  	$error = $db->mdb2->errorInfo();
  	if(isset($error[0]))
		throw new Exception("Błąd SQL: {$error[2]}");
}

/******************** main invoke         *********************************************/
main($config);
?>