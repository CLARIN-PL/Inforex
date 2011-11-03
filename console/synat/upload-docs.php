<?php
/* 
 * ---
 * Uploads parts of InfiKorp into database
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
include("../../engine/include/anntakipi/ixtTakipiAligner.php"); 
include("../../engine/include/anntakipi/ixtTakipiStruct.php"); 
include("../../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../../engine/include/anntakipi/ixtTakipiHelper.php"); 

include("../cliopt.php");
mb_internal_encoding("utf-8");
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to a folder with documents"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("update", null, null, "update files content and insert new one"));
$opt->addParameter(new ClioptParameter("insert", null, null, "insert files into empty subcorpus"));
$opt->addParameter(new ClioptParameter("cleaned", null, null, "mark as cleaned"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));

$config = null;

/******************** parse cli *********************************************/

try{
	$opt->parseCli($argv);
	
	$db_host = $opt->getOptional("db-host", "localhost");
	$db_user = $opt->getOptional("db-user", "root");
	$db_pass = $opt->getOptional("db-pass", "krasnal");
	$db_name = $opt->getOptional("db-name", "gpw");
	$db_port = $opt->getOptional("db-port", "3306");
		
	$config->folder = $opt->getRequired("folder");
	$config->subcorpus = $opt->getRequired("subcorpus");
	$config->update = $opt->exists("update");
	$config->insert = $opt->exists("insert");
	$config->cleaned = $opt->exists("cleaned");
	
	mysql_connect("$db_host:$db_port", $db_user, $db_pass);
	mysql_select_db($db_name);
	mysql_query("SET CHARACTER SET utf8;");
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$sql = sprintf("SELECT * FROM corpus_subcorpora WHERE subcorpus_id = %d", $config->subcorpus);
	$corpus = mysql_fetch_array(mysql_query($sql));
	print_r($corpus);
	$corpus_id = intval($corpus[corpus_id]);
			
	if ( $corpus_id == 0 )
		die("Unrecognized subcorpus id {$config->cobcorpus}\n\n");

	/** Get Clean flag */	
	$sql = sprintf("SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = %d AND short = 'Clean'", $corpus_id);
	$corpora_flag_id = array_pop(mysql_fetch_array(mysql_query($sql)));
			
	/** Fetch files assigned to the subcorpus present in the database. */
	$sql = sprintf("SELECT * FROM reports WHERE subcorpus_id = %d", $config->subcorpus);
	$result = mysql_query($sql);
	$rows = array();
	while ( ($row = mysql_fetch_array($result) ) != null ){
		$rows[$row[link]] = $row;
	}

	if ( $config->insert && count($rows) > 0 )
		die("There are some documents added to this subcorpus\n\n");
	
	$documents = array();
		
	if ($handle = opendir($config->folder)){
		while ( false !== ($file = readdir($handle))){
			if ($file != "."
					&& $file != ".."
					&& mb_substr($file, mb_strlen($file) - 11) != ".header.xml" 
					&& mb_substr($file, mb_strlen($file) - 14) != ".xmlheader.xml" 
					&& mb_substr($file, mb_strlen($file) - 4) != ".old" 
					&& mb_strpos($file, "_backup.") === false){
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
	
	foreach ($documents as $path=>$file){		
		
		$present = isset($rows[$file]) ? $rows[$file] : false;
		$content = stripslashes(file_get_contents($path));
		
		if ($present){
			
			$report_id = $present['id'];
			
			if ( $content !== $present[content]){
			
				if ( $config->update ){
					$sql = sprintf("UPDATE reports SET content = '%s' WHERE id = %d",
								mysql_real_escape_string($content),
								$present[id]);
					mysql_query($sql) or die(mysql_error());					
				}
				
				$stats['update'][] = $file;
			}
			else{
				$stats['nochange'][] = $file;
			}
		}
		else{
			if ( $config->update || $config->insert ) {
				$sql = sprintf("INSERT INTO reports (`corpora`, `subcorpus_id`, `title`, `link`, `date`, `user_id`, `status`, `content`)" .
									" VALUES(%d, %d, '%s', '%s', '%s', %d, %d, '%s')",
									$corpus_id,
									$config->subcorpus,
									mysql_real_escape_string($file),
									mysql_real_escape_string($file),
									date('Y-m-d'),
									1,
									2,
									mysql_real_escape_string($content));
									
				mysql_query($sql) or die(mysql_error());
				$report_id = mysql_insert_id();
			}
			$stats['insert'][] = $file;
		}

				
		/** Set flag if required */
		if ($config->cleaned && $corpora_flag_id){
			$sql = sprintf("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(%d, %d, 3)",
						$corpora_flag_id, $report_id);
			mysql_query($sql);		
		}elseif ($config->insert){
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
} 

/******************** main invoke         *********************************************/
main($config);
?>
