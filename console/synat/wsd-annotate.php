<?php
mb_internal_encoding("UTF-8");

include("../cliopt.php");
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");

require_once("PEAR.php");
require_once("MDB2.php");

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
	if ($corpus_id=='-1' && $subcorpus_id=='-1' && $report_id=='-1')
		throw new Exception("No corpus, subcorpus nor report id set");	
//	else if ($corpus_id && $subcorpus_id)
//		throw new Exception("Set only one parameter: corpus or subcorpus");
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

$db = new Database($config->dsn);
ob_end_clean();
ob_start();

echo "1. Wczytywanie danych ...\n";
ob_flush();
$stats = array();

$wsdTypes = $db->fetch_rows("SELECT * FROM `annotation_types` WHERE name LIKE 'wsd_%'");
$reportArray = array();
$count = 0;



echo "2. Znakowanie słów po formach bazowych ...\n";
ob_flush();
foreach ($wsdTypes as $wsdType){
	$base = substr($wsdType['name'],4);
	$tokens = get_reports_tokens('r.content',$corpus_id, $report_id, $subcorpus_id, $config->disamb,$base);
	$count_token=0;
	foreach ($tokens as $token){
		$text = preg_replace("/\n+|\r+|\s+/","",html_entity_decode(strip_tags($token['content'])));
		$annText = mb_substr($text, intval($token['from']), intval($token['to'])-intval($token['from'])+1);

		$result = get_reports_annotations($token['id'], $wsdType['name'], $token['from'], $token['to']);

		if (!$result){
			set_reports_annotations($token['id'], $wsdType['name'], $token['from'], $token['to'], $annText, $user_id);

			if(!isset($stats[$wsdType['name']]))
				$stats[$wsdType['name']]=0;
			$stats[$wsdType['name']]++;
		}		
		$count_token++;
//		echo "\rBase: $count z " . count($wsdTypes) . " [" . $count_token ." z " . count($tokens) . "]";
		if($base == 'wieś')
			echo "\n\n".$token['id'] ."\n\n";

		echo "\rBase: $count z " . count($wsdTypes);
		progress($count+($count_token/count($tokens)),count($wsdTypes));	
		ob_flush();	
	}	
	
	$count++;
	echo "\rBase: $count z " . count($wsdTypes);
	progress($count,count($wsdTypes));	
	ob_flush();	
}

echo "\n\nBase:\n";
print_r($stats);

echo "\n";
echo "3. Znakowanie słów po formach ortograficznych ...\n";
ob_flush();

$tokens = get_reports_tokens('',$corpus_id, $report_id, $subcorpus_id, $config->disamb,'');

$report_tokens=array();
foreach($tokens as $token){
	$report_tokens[$token['id']][] = $token;
}

$count=0;
$orths = array();
$stats = array();
foreach($report_tokens as $rep_id => $tokens){
	$sql = "SELECT r.content " . 
		"FROM reports r " .
		"WHERE r.id=$rep_id " ;
	$rep_content = $db->fetch_one($sql);

	$htmlStr = new HtmlStr($rep_content);
	$full_text = $htmlStr->getText(0,false);
	$token_from = -1;
	foreach($tokens as $token){
		// jeżeli zasięg tokenu nie przekracza długosci dokumentu i jest to kolejny token
		if($token['to']<strlen($full_text) && $token['from']>$token_from){
			$orth = $htmlStr->getText($token['from'], $token['to']);
			foreach ($wsdTypes as $wsdType){
				$base = mb_strtolower(substr($wsdType['name'],4),'UTF-8');
				if(mb_strtolower($orth,'UTF-8') == $base){
					$result = get_reports_annotations($rep_id, $wsdType['name'], $token['from'], $token['to']);
					
					if (!$result){
						set_reports_annotations($rep_id, $wsdType['name'], $token['from'], $token['to'], $orth, $user_id);												
						
						if(!isset($stats[$wsdType['name']]))
							$stats[$wsdType['name']]=0;
						$stats[$wsdType['name']]++;
					}
				}				
			}				
		}
		$token_from = $token['from'];			
	}
	$count++;
	echo "\rOrth: $count z " . count($report_tokens) . " #" .$rep_id;
	progress($count,count($report_tokens));
	ob_flush();
}	

echo "\nOrth:\n";
print_r($stats);

$ids = array();

$sql = sprintf("SELECT * FROM reports WHERE corpora = %d", $corpus_id);
foreach ( $db->fetch_rows($sql) as $r ){
	$ids[$r['id']] = 1;			
}		

$sql = sprintf("SELECT * FROM reports WHERE subcorpus_id = %d", $subcorpus_id);
foreach ( $db->fetch_rows($sql) as $r ){
	$ids[$r['id']] = 1;			
}		

$ids[$report_id] = 1;

foreach ( array_keys($ids) as $report_id){
	$doc = $db->fetch("SELECT * FROM reports WHERE id=?",array($report_id));	
	set_status_if_not_ready($doc['corpora'], $report_id, "WSD", 1);	
}

echo "\n4. Gotowe.\n";
/*** aux functions */

function set_status_if_not_ready($corpora_id, $report_id, $flag_name, $status){
	global $db;
	$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = ?";
	$corpora_flag_id = $db->fetch_one($sql, array($corpora_id, $flag_name));

	if ($corpora_flag_id){
		if ( !$db->fetch_one("SELECT flag_id FROM reports_flags WHERE corpora_flag_id = ? AND report_id = ?",
							array($corpora_flag_id, $report_id) ) > 0 ){
			$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, ?)",
				array($corpora_flag_id, $report_id, $status));
		}	
	}		
}


/*** set function */

function set_reports_annotations($report_id, $wsd_name, $token_from, $token_to, $annotationText, $user_id){
	global $db;
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
			"VALUES (".$report_id .
					",'".$wsd_name .
					"',".$token_from .
   					",".$token_to .
				    ",'".$annotationText .
				    "',".$user_id . 
				    ",now(),'final','auto')";
	$db->execute($sql);
}


/*** get functions */

function get_reports_annotations($report_id, $wsd_name, $token_from, $token_to){
	global $db;
	$sql = "SELECT id " .
			"FROM reports_annotations " .
			"WHERE `report_id`=" .$report_id.
			"  AND `type`='" .$wsd_name.
			"' AND `from`=" .$token_from.
			"  AND `to`=" .$token_to.
			"  LIMIT 1";
	return $db->fetch_one($sql);
}

function get_reports_tokens($add_to_select=null,$corpus_id, $report_id, $subcorpus_id, $disamb=null, $tokens_tags_base=null){
	global $db;
	$sql = "SELECT r.id, t.from, t.to, t.eos" . 
			( $add_to_select ? ",".$add_to_select." " : " ") . 
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
					" t.token_id=tt.token_id " .
					( $disamb ? " AND tt.disamb=1 " : "" ) .
					( $tokens_tags_base ? " AND tt.base='".$tokens_tags_base."' " : "").
				")";
	return $db->fetch_rows($sql);
}


/*** show progress */

function progress($act_num,$all){
	echo " " . number_format(($act_num/$all)*100, 2)."%    ";	
}
?>
