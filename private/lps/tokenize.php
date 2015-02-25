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

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("analyzer", "a", "(takipi|maca|wmbt|wcrft)", "tool to use"));
$opt->addParameter(new ClioptParameter("document", "d", "id", "document id"));
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
	$config->document = $opt->getParameters("document", null);
	$config->verbose = false;
	
	if ( !in_array($config->analyzer, array("takipi", "maca", "wcrft")))
		throw new Exception("Unrecognized analyzer. {$config->analyzer} not in ['takipi','maca']");
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$ids = array();
	
	$db = new Database($config->dsn);
	
	if ( $config->document ){
		foreach ($config->document as $docid)
			$ids[$docid] = 1;
	}else{			
		$sql = "SELECT * FROM reports WHERE corpora = 3 ORDER BY id ASC";
		foreach ( $db->fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}
	
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		if ( $config->verbose ){
			echo "\n";
		}
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id     ";
		if ( $config->verbose ){
			echo "\n";
		}

		try{
			$doc = $db->fetch("SELECT * FROM reports WHERE id=?",array($report_id));
			$text = trim($doc['content']);
	  		$takipiText="";
	  		$bases="";
	  		$tokensTags="";
	  		$i = 1;
			
			if ( $text == "" )
				continue;
			
			$text_count = count_characters($text);
			$sum_count = 0;

	  		$db->execute("START TRANSACTION");
	  		$db->execute("BEGIN");
	  		DbToken::deleteReportTokens($report_id);
	  		//$db->execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
	  		
			$reader = new XMLReader();								
			$reader->xml($text);
			$chunk_offset = 0;	// Przesunięcie bieżącego chunku względem początku dokumentu
			do {
				$read = $reader->read();
				$chunk_text = "";
				if ( in_array( $reader->localName, array("p", "dateline", "head"))
						&& $reader->nodeType == XMLReader::ELEMENT){

					$textOriginal = trim($reader->readString());

					$tr = new LpsTextTransformer($reader->readOuterXml());
					$text = $tr->getCleanText();
					
					$sum_count += count_characters($text);
					$sum_count += $tr->cutoffLength; 
					
					if ($text == ""){
				  		$chunk_offset += count_characters($textOriginal);
						continue;
					}
												
					$text = strip_tags($text);
					$text = custom_html_entity_decode($text);
					$tokenization = 'none';
					
					if ($config->verbose){
						echo " [TEXT#$i] $text\n";
					}
										
					if ($config->analyzer == 'maca'){
						$text_tagged = HelperTokenize::tagWithMaca($text);
						$tokenization = 'maca:morfeusz-nkjp-official';
					}
					elseif ($config->analyzer == 'takipi'){
						$text_tagged = HelperTokenize::tagWithTakipiWs($text, true);
						$tokenization = 'takipi:guesser';
					}
					else if ( $config->analyzer == 'wcrft'){
						$text_tagged = HelperTokenize::tagPlainWithWcrft($text);
						$tokenization = 'wcrft:' . $config->get_wcrft_config();
					}
					else
						throw new Exception("Unrecognized analyzer. {$config->analyzer} not in ['takipi','maca']");
																					
				  	try {
				  		$ccl = WcclReader::createFromString($text_tagged);
				  	}
				  	catch (Exception $e){
						echo json_encode(array("error"=>"Tokenizer error", "exception"=>$e->getMessage()));
						die("Exception");
				  	}		

					foreach ($ccl->chunks as $chunk){
						foreach ($chunk->sentences as $sentence){
		  					$lastId = count($sentence->tokens)-1;
							foreach ($sentence->tokens as $index=>$token){
								$from = $chunk_offset + $tr->mapToBaseIndes(count_characters($chunk_text));
						  		$chunk_text .= custom_html_entity_decode($token->orth);
						  		$to = $chunk_offset + $tr->mapToBaseIndes(count_characters($chunk_text)-1);
						  		$lastToken = $index==$lastId ? 1 : 0;
						  		
						  		$args = array($report_id, $from, $to, $lastToken);
						  		$db->execute("INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`) VALUES (?, ?, ?, ?)", $args);
						  		$token_id = mysql_insert_id();
						  		
						  		foreach ($token->lex as $lex){
						  			if ( $lex->disamb ){
							  			$base = addslashes(strval($lex->base));
							  			$ctag = addslashes(strval($lex->ctag));
							  			$disamb = $lex->disamb ? "true" : "false";
							  			$tokensTags .= "($token_id, \"$base\", \"$ctag\", $disamb),";
						  			}
						  		}							
							}
						}
					}
				  	
			  		$chunk_offset += count_characters($textOriginal);
					$i++;				
				}								
			}
			while ( $read );
			
			if (strlen($tokensTags)>1) {
				$db->execute("INSERT IGNORE INTO `bases` (`base`) VALUES " . substr($bases,0,-1));
				$db->execute("INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag`,`disamb`) VALUES " . substr($tokensTags,0,-1));
                        }

			$sql = "UPDATE reports SET tokenization = ? WHERE id = ?";
			$db->execute($sql, array($tokenization, $report_id));


			if ( $text_count != $sum_count)
				echo " !! $report_id $text_count != $sum_count \n";

	  		$db->execute("COMMIT");

            /** Tokens */
        	$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = 'Tokens'";
	        $corpora_flag_id = $db->fetch_one($sql, array($doc['corpora']));

            if ($corpora_flag_id){
        	        $db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)",
	                        array($corpora_flag_id, $report_id));
            }

			$db = new Database($config->dsn);					
		}
		catch(Exception $ex){
			echo "---------------------------\n";
			echo "!! Exception !! id = {$doc['id']}";
			echo $ex->getMessage();
			echo "---------------------------\n";
		}
		echo "\ndone\n";
	}
} 

/******************** aux function        *********************************************/
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

function count_characters($text, $ignore_whitechars=true, $ignore_tags=true, $encode_entities=true){
	$text = str_replace("\n", " ", $text);
	$text = str_replace("\r", " ", $text);
	if ($ignore_tags)
		$text = strip_tags($text);
	if ($ignore_whitechars)
		$text = preg_replace("/\p{Z}/m", "", $text);
	if ($encode_entities)
		$text = custom_html_entity_decode($text);
	return mb_strlen($text);
}

/******************** main invoke         *********************************************/
main($config);
?>
