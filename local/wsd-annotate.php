<?php
/**
 * Wersja 2.0
 * Znakowanie słów po formach bazowych i ortograficznych 
 */
 
$engine = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
include($engine . DIRECTORY_SEPARATOR . "config.php");
include($engine . DIRECTORY_SEPARATOR . "config.local.php");
include($engine . DIRECTORY_SEPARATOR . "include.php");
include($engine . DIRECTORY_SEPARATOR . "cliopt.php");

mb_internal_encoding("UTF-8");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addExecute("php wsd-annotate.php -c n -user u -U user:password@host:port/database", "Process documents from corpus n");
$opt->addExecute("php wsd-annotate.php -s n -U user:password@host:port/database", "Process documents from subcorpus n");
$opt->addExecute("php wsd-annotate.php -c n -d -U user:password@host:port/database", "Process documents from corpus n and use only disambiguated bases");
$opt->addParameter(new ClioptParameter("report", "r", "report_id", "report id"));
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus_id", "corpus id"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "subcorpus_id", "subcorpus id"));
$opt->addParameter(new ClioptParameter("disamb", "d", null, "consider disamb only"));
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("user", "u", "userid", "user id"));

/******************** parse cli *********************************************/

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
	
	$config->dsn['phptype'] = 'mysql';
	$config->dsn['username'] = $dbUser;
	$config->dsn['password'] = $dbPass;
	$config->dsn['hostspec'] = $dbHost . ":" . $dbPort;
	$config->dsn['database'] = $dbName;
		    				
	$config->user_id = $opt->getOptional("user", "1");
	$config->report_id = $opt->getOptionalParameters("report");
	$config->corpus_id = $opt->getOptionalParameters("corpus");
	$config->subcorpus_id = $opt->getOptionalParameters("subcorpus");
	$config->disamb = $opt->exists("disamb");
	if (!$config->corpus_id && !$config->subcorpus_id && !$config->report_id)
		throw new Exception("No corpus, subcorpus nor report id set");	
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}
$db = new Database($config->dsn);
ob_start();

/******************** main function       *********************************************/
function main ($config){
	global $db;
	
	echo "1. Wczytywanie danych ...\n";
	ob_flush();
	
	$reports_ids = array();
	$reports_data = array();
	foreach(DbReport::getReports($config->corpus_id,$config->subcorpus_id,$config->report_id, null) as $row){
		$reports_ids[] = $row['id'];
		$reports_data[$row['id']] = $row;
	}
		
	$wsdTypes = $db->fetch_rows("SELECT * FROM `annotation_types` WHERE name LIKE 'wsd_%'");
	$reportArray = array();
	$count = 0;
	$stats = array();
	
	$tokens = get_reports_tokens('', $reports_ids, $config->disamb,'');
	$report_tokens=array();
	foreach($tokens as $token){
		$report_tokens[$token['id']][] = $token;
	}
	
	
	echo "2. Znakowanie słów po formach bazowych ...\n";
	ob_flush();
	
	foreach ($wsdTypes as $wsdType){
		$base = substr($wsdType['name'],4);
		$tokens = get_reports_tokens('', $reports_ids, $config->disamb,$base);
		
		$count_token=0;
		foreach ($tokens as $token){
			$text = preg_replace("/\n+|\r+|\s+/","",custom_html_entity_decode(strip_tags($reports_data[$token['id']]['content'])));
			$annText = mb_substr($text, intval($token['from']), intval($token['to'])-intval($token['from'])+1);

			$result = get_reports_annotations($token['id'], $wsdType['name'], $token['from'], $token['to']);

			if (!$result){
				set_reports_annotations($token['id'], $wsdType['name'], $token['from'], $token['to'], $annText, $config->user_id);

				if(!isset($stats[$wsdType['name']]))
					$stats[$wsdType['name']]=0;
				$stats[$wsdType['name']]++;
			}		
			$count_token++;

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

	$count=0;
	$stats = array();
	foreach($report_tokens as $rep_id => $tokens){
		try{
			$htmlStr = new HtmlStr($reports_data[$rep_id]['content']);
			$token_from = -1;
			foreach($tokens as $token_key => $token){
				// Zakłada się, że zasięg tokenów nie przekracza długosci dokumentu.
				// Jeżeli jest to kolejny token to: 
				if($token['from']>$token_from){
					$orth = $htmlStr->getText($token['from'], $token['to']);
					foreach ($wsdTypes as $wsdType){
						$base = mb_strtolower(substr($wsdType['name'],4),'UTF-8');
						if(mb_strtolower($orth,'UTF-8') == $base){
							$result = get_reports_annotations($rep_id, $wsdType['name'], $token['from'], $token['to']);
					
							if (!$result){
								set_reports_annotations($rep_id, $wsdType['name'], $token['from'], $token['to'], $orth, $config->user_id);												
					
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
		}catch(Exception $ex){
			print "\n\n!! #" . $rep_id . " -> " . $ex->getMessage() . " !!\n\n";
		}
	}	

	echo "\nOrth:\n";
	print_r($stats);

	foreach ( DbReport::getReports(null,null,$reports_ids,null) as $report){
		set_status_if_not_ready($db, $report['corpora'], $report['id'], "WSDa", FLAG_ID_READY);
		set_status_if_not_ready($db, $report['corpora'], $report['id'], "WSDnv", FLAG_ID_READY);
	}

	echo "\n4. Gotowe.\n";	
	
}//end function main


/*** aux functions */

// --- set function
/*** set reports annotations: 
 * 	$report_id - report id "reports_annotations.report_id", 
 *  $wsd_name - annotation type "reports_annotations.type", 
 *  $token_from - annotation from "reports_annotations.from", 
 *  $token_to - annotation to "reports_annotations.to",
 *  $annotationText - annotation text "reports_annotations.text", 
 *  $user_id - user id "reports_annotations.user_id"*/

function set_reports_annotations($report_id, $wsd_name, $token_from, $token_to, $annotationText, $user_id){
	global $db;
	$sql = "INSERT INTO reports_annotations_optimized " .
			"(`report_id`," .
			"`type_id`," .
			"`from`," .
			"`to`," .
			"`text`," .
			"`user_id`," .
			"`creation_time`," .
			"`stage`," .
			"`source`) " .
			"VALUES (".$report_id .
					",(SELECT annotation_type_id FROM annotation_types WHERE name='".$wsd_name . "')" .
					",".$token_from .
   					",".$token_to .
				    ",'".$annotationText .
				    "',".$user_id . 
				    ",now(),'final','auto')";
	$db->execute($sql);
}


// --- get functions
/*** get reports annotations id: 
 * 	$report_id - report id "reports_annotations.report_id", 
 *  $wsd_name - annotation type "reports_annotations.type", 
 *  $token_from - annotation from "reports_annotations.from", 
 *  $token_to - annotation to "reports_annotations.to".
 * RETURN: reports_annotations.id*/

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

/*** get reports tokens: 
 * 	$add_to_select - (null) add to select part,
 * 	$corpus_id - corpis id "reports.corpora", 
 * 	$report_id - report id "reports.id", 
 *  $subcorpus_id - subcorpus id "reports.subcorpus_id", 
 *  $disamb - (null) tokens tags disamb "tokens_tags.disamb", 
 *  $tokens_tags_base - (null) tokens tags base "tokens_tags.base".
 * RETURN: reports.id, tokens.from, tokens.to, $add_to_select*/

function get_reports_tokens($add_to_select=null, $report_ids, $disamb=null, $tokens_tags_base=null){
	global $db;
	$sql = "SELECT r.id, t.from, t.to, tt.base" . 
			( $add_to_select ? ",".$add_to_select." " : " ") . 
			"FROM reports r " .
			"JOIN tokens t " .
				"ON (" .
					"r.id IN ('" . implode("','", $report_ids) . "')" .
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

// --- progress function
/*** print progress in %:  
 * $act_num - actual element, 
 * $all - count all elements. */
function progress($act_num,$all){
	echo " " . number_format(($act_num/$all)*100, 2)."%    ";	
}


/******************** main invoke         *********************************************/
main($config);
?>
