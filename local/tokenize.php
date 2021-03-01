<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath. DIRECTORY_SEPARATOR . 'include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" ). DIRECTORY_SEPARATOR ."config.local.php");
require_once($enginePath . "/cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("analyzer", "a", "(takipi|maca|wmbt|wcrft|wcrft2)", "tool to use"));
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("document", "d", "id", "id of the document"));
$opt->addParameter(new ClioptParameter("user", "u", "id", "id of the user"));
$opt->addParameter(new ClioptParameter("discard-sentence-tags", null, null, "discard add sentence tag process after tokenize"));
$opt->addParameter(new ClioptParameter("insert-sentence-tags", "S", null, "adds <sentence> tags into document content"));
$opt->addParameter(new ClioptParameter("flag", "F", "flag", "tokenize using flag \"flag name\"=flag_value or \"flag name\"=flag_value1,flag_value2,..."));


/******************** parse cli *********************************************/
//$config = null;
try{
	$opt->parseCli($argv);
	
	$dbHost = "localhost";
	$dbUser = "root";
	$dbPass = null;
//	$dbPass = 'pass';
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

    Config::Config()->put_tagsetName('nkjp');

	$dsn = array();
	$dsn['phptype'] = 'mysql';
	$dsn['username'] = $dbUser;
	$dsn['password'] = $dbPass;
	$dsn['hostspec'] = $dbHost . ":" . $dbPort;
	$dsn['database'] = $dbName;
	Config::Config()->put_dsn($dsn);
	
	Config::Config()->put_discardSentenceTags($opt->exists("discard-sentence-tags"));
	Config::Config()->put_insertSentenceTags($opt->exists("insert-sentence-tags"));
	Config::Config()->put_analyzer($opt->getRequired("analyzer"));
	Config::Config()->put_corpus($opt->getParameters("corpus"));
	Config::Config()->put_documents($opt->getParameters("document"));
	Config::Config()->put_flags(null);
	Config::Config()->put_subcorpus($opt->getParameters("subcorpus"));
	Config::Config()->put_user($opt->getOptional("user","1"));
	
	if ( !in_array(Config::Config()->get_analyzer(), array("takipi", "maca", "wcrft", "wcrft2", "morphodita")))
		throw new Exception("Unrecognized analyzer. ".Config::Config()->get_analyzer());
	if (!Config::Config()->gut_corpus() && !Config::Config()->get_subcorpus() && !Config::Config()->get_documents())
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
		Config::Config()->put_flags($flags);
	}		
		
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	print("\n");
	return;
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	try{
		$db = new Database($config->get_dsn());
	}catch(Exception $ex){
		echo "Error: 'Database connection failed'\n";
		echo "in: ".$ex->getFile().", line: ". $ex->getLine()." (tokenize.php:110)\n";
		exit();
	}
	$GLOBALS['db'] = $db;

	$ids = array();
	$formats = array();
	$reports = DbReport::getReports($config->get_corpus(),$config->get_subcorpus(),$config->get_documents(), $config->get_flags());
	
	foreach($reports as $row){
		$ids[$row['id']] = 1;
		$formats[$row['id']] = $row["format"];
	}

	$tagset_id = DbTagset::getTagsetId($config->get_tagsetName());

	if(!$tagset_id){
        echo "Error: Tagset '".$config->get_tagsetName()."' not found in table 'tagsets'\n";
        echo "in: (tokenize.php:127)\n";
        exit();
	}

	tag_documents($config, $db, $ids, $formats, $tagset_id);
} 

/******************** aux function        *********************************************/

/**
 * 
 */
function tag_documents($config, $db, $ids, $formats, $tagset_id){

	$chunkTag = false; // Nazwa tagu, która zostanie użyta to tagowania tekstu mniejszymi fragmentami, false --- taguje cały dokument.
	$useSentencer = false;
	$reportFormat = "premorph";
	
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		$documentFormat = $formats[$report_id];
		$db = new Database($config->get_dsn());
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
					
			if($config->get_discardSentenceTags() && !$config->get_insertSentenceTags()){
				$text = preg_replace("/(<sentence>)(.*)?(<\/sentence>)/", "$2", $text);
				// Zapis treści w bazie
				$t_report = new TableReport($report_id);
				$t_report->content = $text;
				$t_report->save();
				// Reset flagi Sent
				DbReport::updateFlagByShort($t_report->id, "Sent", "NIE GOTOWY");
			}

	  		$db->execute("BEGIN");
	  		DbToken::deleteReportTokens($report_id);

			$index_bases = array();
			foreach ( $db->fetch_rows("SELECT * FROM bases") as $b){
				$index_bases[$b['text']] = $b['id'];
			} 			

			$index_ctags = array();
			foreach ( $db->fetch_rows("SELECT * FROM tokens_tags_ctags WHERE tagset_id = ".$tagset_id) as $b){
				$index_ctags[$b['ctag']] = $b['id'];
			} 			
	  		
	  		$takipiText="";
	  		$new_bases = array();
	  		$new_ctags = array();
			$tokens = array();
			$tokens_tags = array();
				
			$useSentencer =  strpos($text, "<sentence>") === false;
			 
			if ( $documentFormat == "xml" ){				
				$text = '<cesAna xmlns:xlink="http://www.w3.org/1999/xlink" type="pre_morph" version="WROC-1.0"> <chunkList xml:base="text.xml"> <chunk type="p">'
						. strip_tags($text, "<sentence>") . '</chunk> </chunkList> </cesAna>';
			}

			if ( $config->get_analyzer() == "wcrft"){
				if($documentFormat == "plain"){
					$text_tagged = 	HelperTokenize::tagPlainWithWcrft($text);
				}
				else{
					$text_tagged = HelperTokenize::tagPremorphWithMacaWcrft($text, $useSentencer);
				}					
				$tokenization = 'wcrft:' . $config->get_wcrft_config();
			} else if ( $config->get_analyzer() == "maca" ){
				$text_tagged = HelperTokenize::tagWithMaca($text, "ccl");
				$tokenization = 'maca';					
			} else if ( $config->get_analyzer() == "wcrft2"){
				if($documentFormat == "plain"){
					$text_tagged = 	HelperTokenize::tagPlainWithWcrft2($text, $useSentencer);
					//$text_tagged = "";
				}
				else{
					$text_tagged = HelperTokenize::tagPremorphWithWcrft2($text, $useSentencer);
				}					
				$tokenization = 'wcrft2:' . $config->get_wcrft2_config();
			} else if ( $config->get_analyzer() == "morphodita" ){
                if($documentFormat == "plain") {
                    $nlp = new NlpRest2('morphoDita({"guesser":false,"allforms":true,"model":"XXI"})');
                    $text_tagged = $nlp->processSync($text);
                    $tokenization = "nlprest2:MorphoDita:XXI";
                } else {
                	die("XML is not supported for MorphoDita");
				}
			} else {
                die("Unknown -a ".$config->get_analyzer());
            }
			
			if ( strpos($text_tagged, "<tok>") === false ){
				echo "Input:\n------\n";
				print_r($text);
				echo "-------\n";
				echo "Output:\n-------\n";
				print_r($text_tagged);
				throw new Exception("Failed to tokenize the document.");
			}
				
			$ccl = WcclReader::createFromString($text_tagged);

			if ( count($ccl->chunks) == 0 ){
				throw new Exception("Failed to load the document.");
			}
			
			foreach ($ccl->chunks as $chunk){
				foreach ($chunk->sentences as $sentence){
  					$lastId = count($sentence->tokens)-1;
					foreach ($sentence->tokens as $index=>$token){
				  		$from =  mb_strlen($takipiText);
				  		$takipiText = $takipiText . custom_html_entity_decode($token->orth);
				  		$to = mb_strlen($takipiText)-1;
				  		$lastToken = $index==$lastId ? 1 : 0;
				  		
				  		$args = array($report_id, $from, $to, $lastToken);
				  		$tokens[] = $args;
				  		
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

						$tags_args = array();								
				  		foreach ($tags as $lex){
				  			$base = addslashes(strval($lex->base));
				  			$ctag = addslashes(strval($lex->ctag));
				  			$cts = explode(":",$ctag);
				  			$pos = $cts[0]; 
				  			$disamb = $lex->disamb ? "true" : "false";
				  			if (isset($index_bases[$base]))
				  				$base_sql = $index_bases[$base]; 
				  			else{
					  			if ( !isset($new_bases[$base]) ) $new_bases[$base] = 1;
								$base_sql = '(SELECT id FROM bases WHERE text="' . $base . '")';					  				
				  			}
				  			if (isset($index_ctags[$ctag]))
				  				$ctag_sql = $index_ctags[$ctag]; 
				  			else{
					  			if ( !isset($new_ctags[$ctag]) ) $new_ctags[$ctag] = 1;
								$ctag_sql = '(SELECT id FROM tokens_tags_ctags '.
									'WHERE ctag="' . $ctag .'"'.
									' AND tagset_id = '.$tagset_id. ')';
				  			}				  			
				  			$tags_args[] = array($base_sql, $ctag_sql, $disamb, $pos);
				  		}				
				  		$tokens_tags[] = $tags_args;					  		
					}
				}
			}
			
			/* Wstawienie tagów morflogicznych */
			if ( count ($new_bases) > 0 ){
				$sql_new_bases = 'INSERT IGNORE INTO `bases` (`text`) VALUES ("';
				$sql_new_bases .= implode('"),("', array_keys($new_bases)) . '");';
				$db->execute($sql_new_bases); 
			}
			if ( count ($new_ctags) > 0 ){
				$sql_new_ctags = 'INSERT IGNORE INTO `tokens_tags_ctags` (`ctag`, `tagset_id`) VALUES ("';
                $sql_new_ctags .= implode('",'. $tagset_id .'),("', array_keys($new_ctags)) . '",'. $tagset_id .');';
				$db->execute($sql_new_ctags); 
			}			
			
			$sql_tokens = "INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`) VALUES";
			$sql_tokens_values = array();
			foreach ($tokens as $t){
				$sql_tokens_values[] ="({$t[0]}, {$t[1]}, {$t[2]}, {$t[3]})";
			}
			$sql_tokens .= implode(",", $sql_tokens_values);
			$db->execute($sql_tokens);
			
			$tokens_id = array();
			foreach ($db->fetch_rows("SELECT token_id FROM tokens WHERE report_id = ? ORDER BY token_id ASC", array($report_id)) as $t){
				$tokens_id[] = $t['token_id'];				
			}
			echo "Tokens: " . count($tokens_id) . "\n";

			$sql_tokens_tags = "INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag_id`,`disamb`,`pos`) VALUES ";
			$sql_tokens_tags_values = array();
			for ($i=0; $i<count($tokens_id); $i++){
				$token_id = $tokens_id[$i];
				if ( !isset($tokens_tags[$i]) || count($tokens_tags[$i]) == 0 ){
					die("Bład spójności danych: brak tagów dla $i");
				}
				foreach ($tokens_tags[$i] as $t)
					$sql_tokens_tags_values[] ="($token_id, {$t[0]}, {$t[1]}, {$t[2]}, \"{$t[3]}\")";
			}
			$sql_tokens_tags .= implode(",", $sql_tokens_tags_values);
			$db->execute($sql_tokens_tags);

			// Aktualizacja flag i znaczników		
			$sql = "UPDATE reports SET tokenization = ? WHERE id = ?";
			$db->execute($sql, array($tokenization, $report_id));
			
			/** Tokens */
			$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND LOWER(short) = 'tokens'";
			$corpora_flag_id = $db->fetch_one($sql, array($doc['corpora']));
	
			if ($corpora_flag_id){
				$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)",
					array($corpora_flag_id, $report_id));	
			}	
	
			/** Names */
			//set_status_if_not_ready($db, $doc['corpora'], $report_id, "Names", 1);
			//set_status_if_not_ready($db, $doc['corpora'], $report_id, "Chunks", 1);
			
	  		/** Sentences */
			if( $config->get_insertSentenceTags() && $useSentencer )
				Premorph::set_sentence_tag($report_id,$config->get_user());

			$db->execute("COMMIT");
			  		
		}
		catch(Exception $ex){
			$db->execute("ROLLBACK");
			echo "\n";
			echo "-------------------------------------------------------------\n";
			echo "!! Exception @ id = {$doc['id']}\n";
			echo "   " . $ex->getMessage() . "\n";
			echo "-------------------------------------------------------------\n";
		}
	}
	echo "\r End tokenize " . ($n) . " z " . count($ids) ;
	progress(($n),count($ids));
	echo "\n";
}

/** Print progress information in %:  
 * $act_num - actual element, 
 * $all - count all elements. */
function progress($act_num,$all){
	echo " " . number_format(($act_num/$all)*100, 2)."%    ";	
}

/******************** main invoke         *********************************************/
main(Config::Config());
?>
