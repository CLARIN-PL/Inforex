<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
$engine = realpath(dirname(__FILE__) . "/../engine/");
include($engine . "/config.php");
include($engine . "/config.local.php");
include($engine . "/include.php");
include($engine . "/cliopt.php");

mb_internal_encoding("utf-32");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to a folder with documents"));
$opt->addParameter(new ClioptParameter("format", "F", "format", "document format; one of: plain, xml, premorph"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "subcorpus ID"));
$opt->addParameter(new ClioptParameter("user", "u", "id", "user ID"));
$opt->addParameter(new ClioptParameter("update", null, null, "update files content and insert new one"));
$opt->addParameter(new ClioptParameter("insert", null, null, "insert files into empty subcorpus"));
$opt->addParameter(new ClioptParameter("cleaned", null, null, "mark as cleaned"));

$config = null;

/******************** parse cli *********************************************/

$formats = array();
$formats['xml'] = 1;
$formats['plain'] = 2;
$formats['premorph'] = 3;

try{
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
	
	$config->dsn['phptype'] = 'mysql';
	$config->dsn['username'] = $dbUser;
	$config->dsn['password'] = $dbPass;
	$config->dsn['hostspec'] = $dbHost . ":" . $dbPort;
	$config->dsn['database'] = $dbName;
		
	$config->folder = $opt->getRequired("folder");
	$config->subcorpus = intval($opt->getRequired("subcorpus"));
	$config->update = $opt->exists("update");
	$config->insert = $opt->exists("insert");
	$config->cleaned = $opt->exists("cleaned");
	$config->format = $opt->getRequired("format");
	$config->user = intval($opt->getRequired("user"));
	
	if (!isset($formats[$config->format])){
		throw new Exception("Incorrect document format '{$config->format}'");
	}
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/**
 * Sprawdza zgodność treści raportu w bazie danych z podanym tekstem.
 * @param $report_id -- identyfikator raportu
 * @param $content -- treść raportu
 */
function verify_content($report_id, $content){
	global $db;
	$db_content  = $db->fetch_one("SELECT content FROM reports WHERE id=?", array($report_id));
	return $db_content===$content;
}

function report_set_flag($report_id, $flag_name, $flag_id){
	global $db;
	$db->execute("BEGIN");
	try{
		$corpus_id = $db->fetch_one("SELECT corpora FROM reports WHERE id=?", 
									array($report_id));
		if ( intval($corpus_id) > 0 ){
			$corpus_flag_id = $db->fetch_one(
				"SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND name = ?", 
				array($corpus_id, $flag_name));
			if ( intval($corpus_flag_id) > 0 ){
				$args = array($corpus_flag_id, $report_id, $flag_id);	
				$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, ?)", $args);
			}
		}
	}catch(Exception $ex){
		$db->execute("ROLLBACK");
	}
	$db->execute("COMMIT");
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){
	global $formats;

	$GLOBALS['db'] = new Database($config->dsn,false);
	global $db;
	
	$sql = sprintf("SELECT * FROM corpus_subcorpora WHERE subcorpus_id = %d", $config->subcorpus);
	$corpus = mysql_fetch_array(mysql_query($sql));
	$corpus_id = intval($corpus[corpus_id]);
			
	if ( $corpus_id == 0 )
		die("Unrecognized subcorpus id {$config->cobcorpus}\n\n");

	/** Get Clean flag */	
	$sql = sprintf("SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = %d AND short = 'Clean'", $corpus_id);
	$corpora_flag_id = $db->fetch_one($sql);
			
	/** Fetch files assigned to the subcorpus present in the database. */
	$sql = sprintf("SELECT * FROM reports WHERE subcorpus_id = %d", $config->subcorpus);
	$result = mysql_query($sql);
	$rows = array();
	while ( ($row = mysql_fetch_array($result) ) != null ){
		$rows[$row['source']] = $row;
	}

	if ( $config->insert && count($rows) > 0 )
		die("There are some documents added to this subcorpus\n\n");
	
	$documents = array();
	
	if ($handle = opendir($config->folder)){
		while ( false !== ($file = readdir($handle))){           
			if ($file != "."
					&& substr($file, 0, 1) != "."
					&& $file != ".."
					&& mb_substr($file, mb_strlen($file) - 11) != ".header.xml" 
					&& mb_substr($file, mb_strlen($file) - 14) != ".xmlheader.xml" 
					&& mb_substr($file, mb_strlen($file) - 4) != ".old" 
					&& mb_strpos($file, "_backup.") == 0){
				$path = $config->folder . "/" . $file;
				$documents[$path] = $file;
			}
		}
	}	
	
	ksort($documents);
	
	$stats = array();
	$stats['nochange'] = array();
	$stats['insert'] = array();
	$stats['update'] = array();
	$stats['delete'] = array();
	
	$verification_failed = array();
		
	foreach ($documents as $path=>$file){		
		print $file."\n";
		$present = isset($rows[$file]) ? $rows[$file] : false;
		$content = stripslashes(file_get_contents($path));
		$report_id = null;
		
		if ($present){
			
			$report_id = $present['id'];
			
			if ( $content !== $present[content]){
			
				if ( $config->update ){
					$sql = "UPDATE reports SET content = ? WHERE id = ?";
					$args = array($content, $present[id]);
					$db->execute($sql, $args);					
				}
				
				$stats['update'][] = $file;
			}
			else{
				$stats['nochange'][] = $file;
			}
		}
		else{
			if ( $config->update || $config->insert ) {
				$args = array();
				$args[] = $corpus_id;
				$args[] = $config->subcorpus;
				$args[] = $file;
				$args[] = $file;
				$args[] = date('Y-m-d');
				$args[] = $config->user;
				$args[] = 2;
				$args[] = $content;
				$args[] = $formats[$config->format];
				$sql = "INSERT INTO reports " .
						" (`corpora`, `subcorpus_id`, `title`, `source`, `date`, `user_id`, `status`, `content`, `format_id`)" .
						" VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
						
				$db->execute($sql, $args);
				$report_id = $db->last_id();
				
			}
			$stats['insert'][] = $file;
		}
		
		if ( $config->update || $config->insert ){
			if ( verify_content($report_id, $content) !== true){
				$verification_failed[] = array($report_id, $file);
				report_set_flag($report_id, "Clean", FLAG_ID_ERROR);
			}
			else{
				report_set_flag($report_id, "Clean", FLAG_ID_FINISHED);
			}		
		}
				
		/** Set flag if required */
		if ($config->cleaned && $corpora_flag_id){
			$sql = sprintf("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(%d, %d, 3)",
						$corpora_flag_id, $report_id);
			mysql_query($sql);		
		}elseif ($config->insert && $corpora_flag_id){
			$sql = sprintf("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(%d, %d, 1)",
						$corpora_flag_id, $report_id);
			mysql_query($sql);					
		}
	}
	
	$stats['delete'] = count($rows) - count($stats['nochange']) - count($stats['update']);
	
	print_r($stats);
	
	echo "\nSUMMARY\n";
	echo sprintf("%3d file(s) in the folder\n", count($documents));
	echo sprintf("%3d file(s) already in DB with the same content\n", count($stats['nochange']));
	echo sprintf("%3d file(s) already in DB needed update\n", count($stats['update']));
	echo sprintf("%3d file(s) not present in DB\n", count($stats['insert']));
	echo sprintf("%3d entries from DB not found in the folder\n", $stats['delete']);
	echo "\n";
	
	if ( count($verification_failed) > 0){
		echo "Content verification failed for:\n";
		foreach ($verification_failed as $row)
			echo sprintf(" - id: %7d; filename: %s\n", $row[0], $row[1]);
		echo "\n";
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>
