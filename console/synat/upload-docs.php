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
		
	$sql = sprintf("SELECT COUNT(*) FROM reports WHERE subcorpus_id = %d", $config->subcorpus);
	$count = mysql_fetch_array(mysql_query($sql));
	if ( intval($count[0]) > 0 )
		die("There are some documents added to this subcorpus\n\n");
	
	$documents = array();
	
	
	if ($handle = opendir($config->folder)){
		while ( false !== ($file = readdir($handle))){
			if ($file != "." && $file != ".."){
				$path = $config->folder . "/" . $file;
				$documents[$path] = $file;
			}
		}
	}	
	
	ksort($documents);
	
	foreach ($documents as $path=>$file){		
		
		echo "\nFILE: " . $path . "\n";
		
		if ( mb_substr($file, mb_strlen($file) - 11) == ".header.xml" 
			|| mb_substr($file, mb_strlen($file) - 4) == ".old" ){
				echo "  ignore\n";
		}
		else{
			
			$content = stripslashes(file_get_contents($path));
			
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
			echo "  inserted\n";
		}
	}
	
} 

/******************** main invoke         *********************************************/
main($config);
?>
