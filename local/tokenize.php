<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
$engine = "../engine/";
include($engine . "config.php");
include($engine . "config.local.php");
include($engine . "include.php");
include($engine . "cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("analyzer", "a", "(takipi|maca|wmbt|wcrft)", "tool to use"));
//$opt->addParameter(new ClioptParameter("input-format", "i", "(premorph|html|plain)", "input format type"));
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("document", "d", "id", "id of the document"));
$opt->addParameter(new ClioptParameter("user", "u", "id", "id of the user"));
$opt->addParameter(new ClioptParameter("discard-tag-sentence", null, null, "discard add sentence tag process after tokenize"));
$opt->addParameter(new ClioptParameter("insert-sentence-tags", "S", null, "adds <sentence> tags into document content"));
$opt->addParameter(new ClioptParameter("flag", "F", "flag", "tokenize using flag \"flag name\"=flag_value or \"flag name\"=flag_value1,flag_value2,..."));


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
	
	$config->dsn['phptype'] = 'mysql';
	$config->dsn['username'] = $dbUser;
	$config->dsn['password'] = $dbPass;
	$config->dsn['hostspec'] = $dbHost . ":" . $dbPort;
	$config->dsn['database'] = $dbName;
	
	$config->addSentenceTag = !$opt->exists("discard-tag-sentence");
	$config->insertSentenceTags = $opt->exists("insert-sentence-tags");
	$config->analyzer = $opt->getRequired("analyzer");
	$config->corpus = $opt->getParameters("corpus");
	$config->documents = $opt->getParameters("document");
	$config->flags = null;
	$config->subcorpus = $opt->getParameters("subcorpus");
	$config->user = $opt->getOptional("user","1");
	//$config->inputFormat = $opt->getOptional("input-format","premorph");
	
	if ( !in_array($config->analyzer, array("takipi", "maca", "wmbt", "wcrft")))
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

	try{
		$db = new Database($config->dsn);
	}catch(Exception $ex){
		throw new Exception("Database connection failed");
	}
	$GLOBALS['db'] = $db;

	$ids = array();
	$formats = array();
	$reports = DbReport::getReports($config->corpus,$config->subcorpus,$config->documents, $config->flags);
	
	foreach($reports as $row){
		$ids[$row['id']] = 1;
		$formats[$row['id']] = $row["format"];
	}
	
	if ($config->batch && $config->analyzer == 'wmbt')	
		tag_documents_batch($config, $db, $ids);	
	else if ($config->batch && $config->analyzer != 'wmbt')
		throw new Exception("Batch mode not avaiable for analyzer {$config->analyzer}");
	else
		tag_documents($config, $db, $ids, $formats);		
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
function tag_documents($config, $db, $ids, $formats){

	$chunkTag = false; // Nazwa tagu, która zostanie użyta to tagowania tekstu mniejszymi fragmentami, false --- taguje cały dokument.
	$useSentencer = false;
	$reportFormat = "premorph";
	
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		$documentFormat = $formats[$report_id];
		$db = new Database($config->dsn);
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
	  		DbToken::deleteReportTokens($report_id);
	  		//$db->execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
	  		
	  		$takipiText="";
            $bases="INSERT IGNORE INTO `bases` (`text`) VALUES ";
            $ctags="INSERT IGNORE INTO `tokens_tags_ctags` (`ctag`) VALUES ";
	  		$tokensTags="INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag_id`,`disamb`,`pos`) VALUES ";
	  		
			/* Chunk while document at once */		
			if ( $chunkTag === false ){
				
				$useSentencer =  strpos($text, "<sentence>") === false;
				 
				if ( $documentFormat == "xml" ){				
					$text = '<cesAna xmlns:xlink="http://www.w3.org/1999/xlink" type="pre_morph" version="WROC-1.0"> <chunkList xml:base="text.xml"> <chunk type="p">'
							. strip_tags($text, "<sentence>") . '</chunk> </chunkList> </cesAna>';
				}
				if ( $config->analyzer == "wcrft"){
					if($documentFormat == "plain"){
						$text_tagged = 	HelperTokenize::tagPlainWithWcrft($text);
					}
					else{
						$text_tagged = HelperTokenize::tagPremorphWithMacaWcrft($text, $useSentencer);
					}					
					$tokenization = 'wcrft:' . $config->get_wcrft_config();
				}
				else
					die("Unknown -a {$config->analyzer}");
				
				if ( strpos($text_tagged, "<tok>") === false ){
					print_r($text_tagged);
					throw new Exception("Failed to tokenize the document.");
				}
					
				$ccl = WcclReader::createFromString($text_tagged);

				if ( count($ccl->chunks) == 0 ){
					throw new Exception("Failed to load the document.");
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
					  		
					  		$tags = $token->lex;
					  		
					  		/** W przypadku ignów zostaw tylko ign i disamb */
							$ign = null;
							$tags_ign_disamb = array();							
							foreach ($tags as $i_tag=>$tag){
								if ($tag->ctag == "ign")
									$ign = $tag;
								if ($tag->ctag == "ign" || $tag->disamb)
									$tags_ign_disamb[] = $tag; 
							}
							/** Jeżeli jedną z interpretacji jest ign, to podmień na ign i disamb */
							if ($ign){
								$tags = $tags_ign_disamb;
							}
													  							  		
					  		foreach ($tags as $lex){
					  			$base = addslashes(strval($lex->base));
					  			$ctag = addslashes(strval($lex->ctag));
					  			$cts = explode(":",$ctag);
					  			$pos = $cts[0]; 
					  			$disamb = $lex->disamb ? "true" : "false";
					  			$bases .= "(\"$base\"),";
					  			$ctags .= "(\"$ctag\"),";
					  			$tokensTags .= "($token_id, (SELECT id FROM bases WHERE text=\"$base\"), (SELECT id FROM tokens_tags_ctags WHERE ctag=\"$ctag\"), $disamb, \"$pos\"),";
					  		}				
						}
					}
			}
				
			/* Wstawienie tagów morflogicznych */	
			$db->execute(substr($bases,0,-1));
			$db->execute(substr($tokensTags,0,-1));
		}
		catch(Exception $ex){
			echo "\n";
			echo "-------------------------------------------------------------\n";
			echo "!! Exception @ id = {$doc['id']}\n";
			echo "   " . $ex->getMessage() . "\n";
			echo "-------------------------------------------------------------\n";
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
		if( $config->insertSentenceTags && $useSentencer )
			Premorph::set_sentence_tag($report_id,$config->user);
		
	}
	echo "\r End tokenize " . ($n) . " z " . count($ids) ;
	progress(($n),count($ids));
	echo "\n";
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

/** Print progress information in %:  
 * $act_num - actual element, 
 * $all - count all elements. */
function progress($act_num,$all){
	echo " " . number_format(($act_num/$all)*100, 2)."%    ";	
}

/******************** main invoke         *********************************************/
main($config);
?>
