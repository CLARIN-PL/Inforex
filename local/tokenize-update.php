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
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");
require_once($enginePath . "/cliopt.php");

mb_internal_encoding("utf-8");

 
/******************** set configuration   *********************************************/

$tools = array("wcrft2");

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("analyzer", "a", implode("|", $tools), "name of tokenizer or tager"));
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
	$dsn['phptype'] = 'mysqli';
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
	
	if ( !in_array(Config::Config()->get_analyzer(), $tools))
		throw new Exception("Unrecognized tool. ".Config::Config()->get_analyzer()." not in [".implode(", ", $tools)."]");
	if (!Config::Config()->get_corpus() && !Config::Config()->get_subcorpus() && !Config::Config()->get_documents())
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
function main ($config){

	try{
		$db = new Database($config->get_dsn());
	}catch(Exception $ex){
		echo "Error: 'Database connection failed'\n";
		echo "in: ".$ex->getFile().", line: ". $ex->getLine()." (tokenize.php:110)\n";
		exit();
	}
	$GLOBALS["db"] = $db;

	$ids = array();
	$formats = array();
	$reports = DbReport::getReports($config->get_corpus(),$config->get_subcorpus(),$config->get_documents(), $config->get_flags());
	echo sprintf("%d document(s) loaded\n", count($reports));
	
	foreach($reports as $row){
		$ids[$row['id']] = 1;
		$formats[$row['id']] = $row["format"];
	}

    $tagset_id = DbTagset::getTagsetId($config->get_tagsetName());

    if(!$tagset_id){
        echo "Error: Tagset '".$config->get_tagsetName()."' not found in table 'tagsets'\n";
        echo "in: (tokenize-update.php:129)\n";
        exit();
    }

	tag_documents($config, $db, $ids, $formats, $tagset_id);
} 

/**
 * 
 */
function tag_documents($config, $db, $ids, $formats, $tagset_id ){

	$useSentencer = false;
	$reportFormat = "premorph";
	
	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		$documentFormat = $formats[$report_id];
		$db = new Database($config->get_dsn());
		echo sprintf("[%d z %d] id=%d ", ++$n, count($ids), $report_id);

		$doc = $db->fetch("SELECT * FROM reports WHERE id=?",array($report_id));		
		$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = ?";
		$corpora_flag_id = $db->fetch_one($sql, array($doc['corpora'], "Tokens"));
		
		echo sprintf("; 'Tokens' corpus_flag_id=%d", $corpora_flag_id);
		
		try{
			
			$flag_id = $db->fetch_one("SELECT flag_id FROM reports_flags WHERE corpora_flag_id = ? AND report_id = ?",
								array($corpora_flag_id, $report_id) );
			echo sprintf("; 'Tokens' flag_id=%d", intval($flag_id));
			if ( intval($flag_id) && ($flag_id == 3 || $flag_id == 4) ){
				echo " already processed\n";
				continue;
			}
			else
				echo " processing...\n";
											
			$text = $doc['content'];
			
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

			$db->fetch_rows("SELECT * FROM bases");
	  		
	  		$takipiText="";
            $bases="INSERT IGNORE INTO `bases` (`text`) VALUES ";
            $ctags="INSERT IGNORE INTO `tokens_tags_ctags` (`ctag`, `tagset_id`) VALUES ";
	  		$tokensTags="INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag_id`,`disamb`,`pos`) VALUES ";

			$useSentencer =  strpos($text, "<sentence>") === false;
			 
			if ( $config->get_analyzer() == "wcrft2" ){
				if ( $documentFormat == "premorph" ){
					$text_tagged = HelperTokenize::tagPremorphWithWcrft2($text, $useSentencer);
					$tokenization = 'wcrft2:' . $config->get_wcrft2_config();
				}
				else if($documentFormat == "plain"){
					$text_tagged = 	HelperTokenize::tagPlainWithWcrft2($text, $useSentencer);
					$tokenization = 'wcrft2:' . $config->get_wcrft2_config();
				}
				else{
					die("Error: [report_id={$doc['id']}] {$config->get_analyzer()} cannot be used for '$documentFormat' format\n");					
				}
			}
			else
				die("Error: Unknown -a {$config->get_analyzer()}");
			
			if ( strpos($text_tagged, "<tok>") === false ){
				echo "Input:\n";
				echo "------\n";
				print_r($text);
				echo "-------\n";
				echo "Output:\n";
				echo "-------\n";
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
				  		$token_id = intval($db->last_id());
				  		
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
				  			$ctags .= "(\"$ctag\", $tagset_id),";
				  			$tokensTags .= "($token_id, (SELECT id FROM bases WHERE text=\"$base\"), (SELECT id FROM tokens_tags_ctags WHERE ctag=\"$ctag\"), $disamb, \"$pos\"),";
				  		}				
					}
				}
			
			/* Wstawienie tagów morflogicznych */	
			$db->execute(substr($bases,0,-1));
			$db->execute(substr($ctags,0,-1));
			$db->execute(substr($tokensTags,0,-1));

			// Aktualizacja flag i znaczników		
			$sql = "UPDATE reports SET tokenization = ? WHERE id = ?";
			$db->execute($sql, array($tokenization, $report_id));
			
			if ($corpora_flag_id){
				$args = array($corpora_flag_id, $report_id); 
				$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)", $args);
			}	
	
	  		/** Sentences */
			if( $config->get_insertSentenceTags() && $useSentencer )
				Premorph::set_sentence_tag($report_id,$config->get_user());
			
			$db->execute("COMMIT");
			
		}
		catch(Exception $ex){
			echo "\n";
			echo "-------------------------------------------------------------\n";
			echo "!! Exception @ id = {$doc['id']}\n";
			echo "   " . $ex->getMessage() . "\n";
			echo "-------------------------------------------------------------\n";			
			$db->execute("ROLLBACK");
		}
			  				
	}
	echo "Done\n";
}

/******************** main invoke         *********************************************/
main(Config::Config());
?>
