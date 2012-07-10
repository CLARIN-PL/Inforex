<?php
/*
 * Created on Jul 6, 2012
 * Skrypt do importu dokumentów w formacie premorph
 * 1) parametr -f określa lokalizację "/path/" wczytywanych dokumentów
 * 2) w lokalizacji "/path/" znajdują się katalogi ("premorph" i "ccl")
 *    "premorph" - zawiera dokumenty w formacie premorph
 *    "ccl" - pliki w formacie ccl (nazwy plików ccl muszą odpowiadać plikom premorph)
 * 3) w czasie działania skryptu powstaje katalog "/path/inforex_ccl/" 
 *    zawierający dokumenty ccl o nazwach odpowiadających identyfikatorom dokumentów 
 *    z bazy inforexa (konieczne do import-chunks.php)
 * 4) wymagane jest określenie numeru korpusu i podkorpusu, oraz podanie id usera 
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
//$opt->addExecute("php export-ccl.php --corpus1 n1 --corpus2 n2 --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx ",null);
$opt->addExecute("php import-premorph.php ",null);
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
	$config->folder = $opt->getRequired("folder");
	$config->corpus = $opt->getRequired("corpus");
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
		if ($handle = opendir($config->folder."/premorph/")){
			while ( false !== ($file = readdir($handle))){
				if ($file != "."
						&& $file != ".."
						&& mb_substr($file, mb_strlen($file) - 11) != ".header.xml" 
						&& mb_substr($file, mb_strlen($file) - 14) != ".xmlheader.xml" 
						&& mb_substr($file, mb_strlen($file) - 4) != ".old" 
						&& mb_strpos($file, "_backup.") === false){
					$path = $config->folder . "/premorph/" . $file;
					$documents[$path] = $file;
				}
			}
		}	
	
		ksort($documents);
		mkdir($config->folder."/inforex_ccl/", 0700);
		$doc_num = 1; 
		// wczytywanie i zapis dokumentów premorph
		foreach ($documents as $path=>$file){		
			print "[".$doc_num++."/".count($documents)."] -> ".$path."\n";
			$r = new CReport();
			$r->title = strval($file);
			$r->date = date("Y-m-d H:i:s");
			$r->source = strval($file);
			$r->corpora = intval($config->corpus);
			$r->subcorpus_id = intval($config->subcorpus);
			$r->user_id = $config->user;
			$r->status = intval($config->status);
			$r->type = 1;  // nieokreślony
			$r->content = stripslashes(file_get_contents($path));
			$r->save();
						
			if (!copy($config->folder."/ccl/".mb_substr($file, 0, mb_strlen($file) -4).".xml", $config->folder."/inforex_ccl/".$r->id.".xml")) {
				echo "failed to copy $file...\n";
			}
			
			$df = new DiffFormatter();
			$diff = $df->diff("", $r->content, true);
			if ( trim($diff) != "" ){
				$deflated = gzdeflate($diff);
				$data = array();
				$data[] = date("Y-m-d H:i:s");
				$data[] = $r->user_id;
				$data[] = $r->id;
				$data[] = $deflated;
				$sql = "INSERT INTO `reports_diffs`(`datetime`, `user_id`, `report_id`, `diff`) VALUES(?, ?, ?, ?)";
				$db->execute($sql, $data);
			}
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
