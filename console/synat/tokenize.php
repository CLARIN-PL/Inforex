<?php
/* 
 * ---
 * Uploads parts of InfiKorp into database
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
include("../cliopt.php");
mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("analyzer", "a", "(takipi|maca)", "tool to use"));
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("document", "d", "id", "id of the document"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));

/******************** parse cli *********************************************/

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
	
	$config->analyzer = $opt->getRequired("analyzer");
	$config->corpus = $opt->getParameters("corpus");
	$config->subcorpus = $opt->getParameters("subcorpus");
	$config->documents = $opt->getParameters("document");
	
	mysql_connect("$db_host:$db_port", $db_user, $db_pass);
	mysql_select_db($db_name);
	mysql_query("SET CHARACTER SET utf8;");
	
	if ( !in_array($config->analyzer, array("takipi", "maca")))
		throw new Exception("Unrecognized analyzer. {$config->analyzer} not in ['takipi','maca']");
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$db = new Database($config->dsn);

	$ids = array();
	
	foreach ($config->corpus as $c){
		$sql = sprintf("SELECT * FROM reports WHERE corpora = %d", $c);
		foreach ( $db->fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}

	foreach ($config->subcorpus as $s){
		$sql = sprintf("SELECT * FROM reports WHERE subcorpus_id = %d", $s);
		foreach ( $db->fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}
	
	foreach ($config->documents as $d){
		$ids[$d] = 1;
	}
	
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id     ";

		try{
			$doc = $db->fetch("SELECT * FROM reports WHERE id=?",array($report_id));
			$text = $doc['content'];

	  		$db->execute("START TRANSACTION");
	  		$db->execute("BEGIN");
	  		$db->execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
	  		
	  		$takipiText="";
	  		$tokensTags="INSERT INTO `tokens_tags` (`token_id`,`base`,`ctag`,`disamb`) VALUES ";
			$reader = new XMLReader();
			$reader->xml($text);
			do {
				$read = $reader->read();
				if ($reader->localName == "chunk" && $reader->nodeType == XMLReader::ELEMENT){
					$text = trim($reader->readString());
					if ($text == "")
						continue;
						
					$text = strip_tags($text);
					$text = html_entity_decode($text);
					$tokenization = 'none';
					
					if ($config->analyzer == 'maca'){
						$text_tagged = HelperTokenize::tagWithMaca($text);
						$tokenization = 'maca:morfeusz-nkjp';
					}
					elseif ($config->analyzer == 'takipi'){
						$text_tagged = tag_with_takipiws($config, $text);
						$tokenization = 'takipi';
					}
					else
						throw new Exception("Unrecognized analyzer. {$config->analyzer} not in ['takipi','maca']");
					//echo $text_tagged;
				  	try {
				  		$takipiDoc = TakipiReader::createDocumentFromText($text_tagged);
				  	}
				  	catch (Exception $e){
						echo json_encode(array("error"=>"TakipiReader error", "exception"=>$e->getMessage()));
						die("Exception");
				  	}		
			  		foreach ($takipiDoc->sentences as $sentence){
	  					$lastId = count($sentence->tokens)-1;
			  			foreach ($sentence->tokens as $index=>$token){
					  		$from =  mb_strlen($takipiText);
					  		$takipiText = $takipiText . html_entity_decode($token->orth);
					  		$to = mb_strlen($takipiText)-1;
					  		$lastToken = $index==$lastId ? 1 : 0;
					  		
					  		$args = array($report_id, $from, $to, $lastToken);
					  		$db->execute("INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`) VALUES (?, ?, ?, ?)", $args);
					  		$token_id = mysql_insert_id();
					  		
					  		foreach ($token->lex as $lex){
					  			$base = addslashes(strval($lex->base));
					  			$ctag = addslashes(strval($lex->ctag));
					  			$disamb = $lex->disamb ? "true" : "false";
					  			$tokensTags .= "($token_id, \"$base\", \"$ctag\", $disamb),";
					  		}
			  			}
			  		}
									
				}				
			}
			while ( $read );
			$db->execute(substr($tokensTags,0,-1));
			
			$sql = "UPDATE reports SET tokenization = ? WHERE id = ?";
			$db->execute($sql, array($tokenization, $report_id));
			
			/** Tokens */
			$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = 'Tokens'";
			$corpora_flag_id = db_fetch_one($sql, array($doc['corpora']));
	
			if ($corpora_flag_id){
				$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)",
					array($corpora_flag_id, $report_id));	
			}	
	
			/** Names */
			set_status_if_not_ready($db, $doc['corpora'], $report_id, "Names", 1);
			set_status_if_not_ready($db, $doc['corpora'], $report_id, "Chunks", 1);

	  		$db->execute("COMMIT");			
		}
		catch(Exception $ex){
			echo "---------------------------\n";
			echo "!! Exception !! id = {$doc['id']}";
			echo $ex->getMessage();
			echo "---------------------------\n";
		}
	}
} 

/******************** aux function        *********************************************/
function tag_with_takipiws($config, $text){
	$tagger = new WSTagger($config->takipi_wsdl);
	$tagger->tag($text);
	$text_tagged = "<doc>".$tagger->tagged."</doc>"; 
	return $text_tagged;
}

function set_status_if_not_ready($db, $corpora_id, $report_id, $flag_name, $status){
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

/******************** main invoke         *********************************************/
main($config);
?>
