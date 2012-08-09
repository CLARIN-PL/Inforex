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
$opt->addParameter(new ClioptParameter("batch", "b", null, "use batch mode (for wmbt only)"));
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("document", "d", "id", "id of the document"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("user", "user", "id", "id of the user"));
$opt->addParameter(new ClioptParameter("discard-tag-sentence", null, null, "discard add sentence tag process after tokenize"));
$opt->addParameter(new ClioptParameter("insert-sentence-tags", null, null, "adds <sentence> tags into document content"));
$opt->addParameter(new ClioptParameter("flag", "flag", "flag", "tokenize using flag \"flag name\"=flag_value or \"flag name\"=flag_value1,flag_value2,..."));

/******************** parse cli *********************************************/
//$config = null;
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
	
	$config->addSentenceTag = !$opt->exists("discard-tag-sentence");
	$config->insertSentenceTags = $opt->exists("insert-sentence-tags");
	$config->analyzer = $opt->getRequired("analyzer");
	$config->batch = $opt->exists("batch");
	$config->corpus = $opt->getParameters("corpus");
	$config->documents = $opt->getParameters("document");
	$config->flags = null;
	$config->subcorpus = $opt->getParameters("subcorpus");
	$config->user = $opt->getOptional("user","1");
	
	if ( !in_array($config->analyzer, array("takipi", "maca", "wmbt")))
		throw new Exception("Unrecognized analyzer. {$config->analyzer} not in ['takipi','maca']");
	if (!$config->corpus && !$config->subcorpus && !$config->documents)
		throw new Exception("No corpus, subcorpus nor document id set");
	
	$flags = null;
	if ( $opt->exists("flag")){
		$flags = array();
		$flag = $opt->getParameters("flag");
		foreach($flag as $f){
			if ( preg_match("/(.+)=(.+)/", $f, $n)){
				$flag_name = $n[1];
				if (!array_key_exists($flag_name, $flags)){
					$flags[$flag_name]=array();
				}
				if ( preg_match_all("/(?P<digit>\d+)/", $n[2], $v)){
					foreach($v['digit'] as $key => $digit)
						$flags[$flag_name][]=$digit;
				}						
			}else{
				throw new Exception("Flag is incorrect. Given '$flag', but exptected 'name=value'");
			}	
		}		
		$config->flags=$flags;
	}		
		
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$db = new Database($config->dsn);
	$GLOBALS['db'] = $db;

	$ids = array();
	$reports = DbReport::getReports2($config->corpus,$config->subcorpus,$config->documents, $config->flags);
	foreach($reports as $row){
		$ids[$row['id']] = 1;
	}
	
	if ($config->batch && $config->analyzer == 'wmbt')	
		tag_documents_batch($config, $db, $ids);	
	else if ($config->batch && $config->analyzer != 'wmbt')
		throw new Exception("Batch mode not avaiable for analyzer {$config->analyzer}");
	else
		tag_documents($config, $db, $ids);		
} 

/******************** aux function        *********************************************/

/**
 * 
 */
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

/**
 * 
 */
function tag_documents($config, $db, $ids){

	$chunkTag = false; // Nazwa tagu, która zostanie użyta to tagowania tekstu mniejszymi fragmentami, false --- taguje cały dokument.
	$useSentencer = true;
	$reportFormat = "premorph";
	
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id  ";
		progress(($n-1),count($ids));

		try{
			$doc = $db->fetch("SELECT * FROM reports WHERE id=?",array($report_id));
			$text = $doc['content'];
			
			$text = str_replace("&oacute;", "ó", $text);
			$text = str_replace("&ndash;", "-", $text);
			$text = str_replace("&hellip;", "…", $text);			
			$text = str_replace("&sect;", "§", $text);			
			$text = str_replace("&Oacute;", "Ó", $text);
			$text = str_replace("&sup2;", "²", $text);
			$text = str_replace("&ldquo;", "“", $text);
			$text = str_replace("&bull;", "•", $text);
			$text = str_replace("&middot;", "·", $text);
			$text = str_replace("&rsquo;", "’", $text);
			$text = str_replace("&nbsp;", " ", $text);
			$text = str_replace("&Uuml;", "ü", $text);
			$text = str_replace("<br/>", " ", $text);
			$text = str_replace("& ", "&amp; ", $text);
						 
	  		$db->execute("START TRANSACTION");
	  		$db->execute("BEGIN");
	  		$db->execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
	  		
	  		$takipiText="";
	  		$tokensTags="INSERT INTO `tokens_tags` (`token_id`,`base`,`ctag`,`disamb`) VALUES ";
							
			/* Chunk while document at once */		
			if ( $chunkTag === false ){
				if ( $config->analyzer == "maca" ){
					$text_tagged = HelperTokenize::tagPremorphWithMaca($text);
					 $tokenization = 'maca:morfeusz-nkjp';
				}
				else if ( $config->analyzer == "wmbt"){
					$text_tagged = HelperTokenize::tagWithMacaWmbt($text, $useSentencer);
					$tokenization = 'wmbt:morfeusz-nkjp';
				}
				else
					die("Unknown -a {$config->analyzer}");
				$ccl = WcclReader::createFromString($text_tagged);

				if ( count($ccl->chunks) == 0 ){
					throw new Exception("Failed to tokenize. The CCL object jest empty.");
				}

				foreach ($ccl->chunks as $chunk)
					foreach ($chunk->sentences as $sentence){
	  					$lastId = count($sentence->tokens)-1;
						foreach ($sentence->tokens as $index=>$token){
					  		$from =  mb_strlen($takipiText);
					  		$takipiText = $takipiText . custom_html_entity_decode($token->orth);
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
			/* Chunking by any custom element as a sentence need rewriting
			else{
		  		$tagName = "chunk";
		  		$text = "<meta_root>$text</meta_root>";
		  		if(preg_match("[<sentence>]",$text))
		  			$tagName = "sentence";
		  		else{
					$text = "<txt>$text</txt>";
					$tagName = "txt";	  			
		  		}
				echo $tagName;

		  		$reader = new XMLReader();
				$reader->xml($text);
				$count_read = 0;
				$all_read = (substr_count($text, "<".$tagName.">") ? substr_count($text, "<".$tagName.">") : 1);
			
				do {
					$read = $reader->read();
					if ($reader->localName == $tagName && $reader->nodeType == XMLReader::ELEMENT){
						$text = trim($reader->readInnerXML());
						if ($text == "")
							continue;
						$text = strip_tags($text);
						//$text = html_entity_decode($text);
						$text = custom_html_entity_decode($text);
						$tokenization = 'none';
						if ($config->analyzer == 'maca'){
							$text_tagged = HelperTokenize::tagPremorphWithMaca($text);
							$tokenization = 'maca:morfeusz-nkjp';
						}
						elseif ($config->analyzer == 'wmbt'){
	                                                $text_tagged = HelperTokenize::tagWithMacaWmbt($text);
	                                                $tokenization = 'wmbt:morfeusz-nkjp';
	                                        }
						elseif ($config->analyzer == 'takipi'){
							$text_tagged = HelperTokenize::tagWithTakipiWs($config, $text);
							$tokenization = 'takipi';
						}
						else
							throw new Exception("Unrecognized analyzer. {$config->analyzer} not in ['takipi','maca']");
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
						  		//$takipiText = $takipiText . html_entity_decode($token->orth);
						  		$takipiText = $takipiText . custom_html_entity_decode($token->orth);
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
			}
			*/
				
			// Wstawienie tagów morfloogicznych	
			$db->execute(substr($tokensTags,0,-1));
		}
		catch(Exception $ex){
			echo "\n";
			echo "---------------------------\n";
			echo "!! Exception @ id = {$doc['id']}\n";
			echo $ex->getMessage();
			echo "---------------------------\n";
		}

		// Aktualizacja flag i znaczników
		
		$sql = "UPDATE reports SET tokenization = ? WHERE id = ?";
		$db->execute($sql, array($tokenization, $report_id));
		
		/** Tokens */
		$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = 'Tokens'";
		$corpora_flag_id = $db->fetch_one($sql, array($doc['corpora']));

		if ($corpora_flag_id){
			$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)",
				array($corpora_flag_id, $report_id));	
		}	

		/** Names */
		set_status_if_not_ready($db, $doc['corpora'], $report_id, "Names", 1);
		set_status_if_not_ready($db, $doc['corpora'], $report_id, "Chunks", 1);
		
		$db->execute("COMMIT");
			  		
  		/** Sentences */
		if( $config->insertSentenceTags )
			Premorph::set_sentence_tag($report_id,$config->user);
		
	}
	echo "\r End tokenize " . ($n) . " z " . count($ids) ;
	progress(($n),count($ids));
}

/**
 * 
 */
function tag_documents_batch($config, $db, $ids){
	$n = 0;
	$texts = array();

	foreach ( array_keys($ids) as $report_id){

		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id  ";

		$doc = $db->fetch("SELECT * FROM reports WHERE id=?",array($report_id));
		$texts[$report_id] = $doc['content'];
	}
	
	echo "Tagging ...";
	HelperTokenize::tagWithMacaWmbtBatch($texts);
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
