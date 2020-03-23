<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

$enginePath = realpath(dirname(__FILE__) . "/../engine/");
$configPath = realpath(dirname(__FILE__) . "/../config/");
include($enginePath . "/config.php");
include($configPath . "/config.local.php");
include($enginePath . "/include.php");
include($enginePath . "/cliopt.php");
include($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));

/******************** parse cli *********************************************/

$formats = array();
$formats['xml'] = 1;
$formats['plain'] = 2;
$formats['premorph'] = 3;

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
			$config->dsn['phptype'] = 'mysql';
			$config->dsn['username'] = $dbUser;
			$config->dsn['password'] = $dbPass;
			$config->dsn['hostspec'] = $dbHost . ":" . $dbPort;
			$config->dsn['database'] = $dbName;
		}else{
			throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
		}
	}
	
	$config->verbose = $opt->exists("verbose");
			
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

try{
	$daemon = new TaskDaemon($config->dsn, $config->verbose);
	while ($daemon->tick()){};
}
catch(Exception $ex){
	print "Error: " . $ex->getMessage() . "\n";
	print_r($ex);
}
sleep(2);


/******************** main function       *********************************************/
// Process all files in a folder
function tick ($config){

} 

/**
 * Handle single request from tasks_documents.
 */
class TaskDaemon{

    //var $taskTypes = array('liner2', 'update-ccl', 'export', 'nlprest2-tagger');

	function __construct($dsn, $verbose){
		$this->db = new Database($dsn, false);
		$GLOBALS['db'] = $this->db;
		$this->verbose = $verbose;
	}
	
	/**
	 * Print message if verbose mode is on.
	 */
	function info($message){
		if ( $this->verbose ){
			echo $message . "\n";
		}
	}
	
	/**
	 * Check the queue for new request.
	 */
	function tick(){
		$this->db->execute("BEGIN");

		$types = array('liner2', 'update-ccl', 'export', 'nlprest2-tagger', 'upload-zip-txt');
		$types = "'" . implode("','", $types) . "'";

		$sql = "SELECT t.*, tr.report_id" .
				" FROM tasks t" .
				" LEFT JOIN tasks_reports tr ON (tr.task_id=t.task_id AND tr.status = 'new')" .
				" WHERE t.type IN ($types) AND t.status <> 'done' AND t.status <> 'error'" .
				" ORDER BY datetime ASC LIMIT 1";
		$task = $this->db->fetch($sql);
		$this->info($task);
			
		if ( $task === null ){
			return false;
		}
	
		if ( $task['status'] == "new" ){
			/* Change task status: new => process */
			$this->db->update("tasks", array("status"=>"process"), array("task_id"=>$task['task_id']));
		}
		
		/* Change document status if given */
		if ( $task['report_id'] ){	
			$this->db->update("tasks_reports", 
					array("status"=>"process"), 
					array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
		}				
		$this->db->execute("COMMIT");
		
		//print_r($task);
		
		$params = json_decode($task['parameters'], true);
		try{
			$task_type = $task['type']; 
			
			/* Document-level task */
			if ( in_array($task_type, array("liner2", "update-ccl", "nlprest2-tagger")) ){
				if ( $task['report_id'] ){
					switch ($task_type){
						case "liner2":
                            $message = $this->processLiner2($task['report_id'], $task['user_id'], $params);
							break;
						case "update-ccl":
                            $message = $this->processUpdateCcl($task['report_id']);
                            break;
						case "nlprest2-tagger":
                            $message = $this->processNlprest2Tagger($task['report_id'], $params);
                            break;
					}
					$this->db->update("tasks_reports",
							array("status"=>"done", "message"=>$message),
							array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
					$this->db->execute("UPDATE tasks SET current_step=current_step+1 WHERE task_id = ?",
							array($task['task_id']));
				}
				else if ($task['status'] == "process") {
					/* Jeżeli nie ma dokumentów do przetworzenia w ramach tasku to ustaw status tasku na zakończony */
					$this->db->update("tasks", array("status"=>"done"), array("task_id"=>$task['task_id']));
				}
			}
			/* Corpus-level task */
			else{
				if ( $task_type == "export" ){
					$message = $this->processExport($task, $params);
					$this->db->update("tasks", array("status"=>"done"), array("task_id"=>$task['task_id']));
				} else if ( $task_type == "upload-zip-txt" ){
                    $oTask = new TableTask($task['task_id']);
				    $taskProcessor = new TaskProcessorUploadZipTxt($oTask);
                    $taskProcessor->run();

                    print_r("done");
                    $oTask->setStatus("done");
                    $oTask->update();
                    //$this->db->update("tasks", array("status"=>"done"), array("task_id"=>$task['task_id']));
                }
			}
		}
		catch(Exception $ex){
			$this->info("Exception: " . $ex->getMessage());
			
			if ( $ex->getMessage() == "TIMEOUT" ){
				$this->db->update("tasks_reports", 
						array("status"=>"new"), 
						array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
			}
			else{
				$this->db->update("tasks_reports", 
						array("status"=>"error"), 
						array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));				
			}
			return false;
		}			
			
		return true;
	}

	function processNlprest2Tagger($report_id, $params){
		echo "Starting document " . $report_id . "\n";
        $doc = $this->db->fetch("SELECT * FROM reports WHERE id=?",array($report_id));
        $text = $doc[DB_COLUMN_REPORTS__CONTENT];
        $tagset_id = $params['tagset_id'];

        if ( $doc[DB_COLUMN_REPORTS__FORMAT_ID] != DB_REPORT_FORMATS_PLAIN ){
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
            
            $text = strip_tags($text);
            $text = html_entity_decode($text);
        }

        $this->db->execute("BEGIN");

        echo "Processing " . $report_id . " ...\n";

        DbToken::deleteReportTokens($report_id);

        $index_bases = DbBase::getBasesMap();
        $index_ctags = DbTag::getTagsetTagsMap($tagset_id);
        $index_orths = DbOrth::getOrthsMap();

        $takipiText="";
        $new_bases = array();
        $new_ctags = array();
        $new_orths = array();
        $tokens = array();
        $tokens_tags = array();

        $lpmn = sprintf("%s(%s)", $params['nlprest2_task'], json_encode($params['nlprest2_params']));
        $nlp = new NlpRest2($lpmn);
        $text_tagged = $nlp->processSync($text);
        $tokenization = "nlprest2:$lpmn";

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
                    $orth = custom_html_entity_decode($token->orth);
                    $from =  mb_strlen($takipiText);
                    $takipiText = $takipiText . $orth;
                    $to = mb_strlen($takipiText)-1;
                    $lastToken = $index==$lastId ? 1 : 0;

                    if (isset($index_orths[$orth])) {
                        $orth_sql = $index_orths[$orth];
                    } else {
                        $new_orths[$orth] = 1;
                        $orth_sql = "(SELECT orth_id FROM orths WHERE orth='" . mysql_escape_string($orth) . "')";
                    }

                    $args = array($report_id, $from, $to, $lastToken, $orth_sql);
                    $tokens[] = $args;

                    $tags = $token->lex;

                    /** W przypadku ignów zostaw tylko ign i disamb */
                    $ign = null;
                    $tags_ign_disamb = array();
                    foreach ($tags as $i_tag=>$tag){
                        if ($tag->ctag == "ign") {
                            $ign = $tag;
                        }
                        if ($tag->ctag == "ign" || $tag->disamb) {
                            $tags_ign_disamb[] = $tag;
                        }
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
                        if (isset($index_bases[$base])) {
                            $base_sql = $index_bases[$base];
                        } else {
                            if ( !isset($new_bases[$base]) ) $new_bases[$base] = 1;
                            $base_sql = "(SELECT id FROM bases WHERE text='" . $base . "')";
                        }
                        if (isset($index_ctags[$ctag])) {
                            $ctag_sql = $index_ctags[$ctag];
                        } else {
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
            $this->db->execute($sql_new_bases);
            echo "New bases: " . count($new_bases) . "\n";
        }
        if ( count ($new_ctags) > 0 ){
            $sql_new_ctags = 'INSERT IGNORE INTO `tokens_tags_ctags` (`ctag`, `tagset_id`) VALUES ("';
            $sql_new_ctags .= implode('",'. $tagset_id .'),("', array_keys($new_ctags)) . '",'. $tagset_id .');';
            $this->db->execute($sql_new_ctags);
            echo "New ctags: " . count($new_ctags) . "\n";
        }
        if ( count ($new_orths) > 0 ){
            $new_orths = array_keys($new_orths);
            $new_orths = array_map("mysql_escape_string", $new_orths);
            $sql_new_orths = 'INSERT IGNORE INTO `orths` (`orth`) VALUES ("' . implode('"),("', $new_orths) . '");';
            $this->db->execute($sql_new_orths);
            echo "New orths: " . count($new_orths) . "\n";
            echo $sql_new_orths . "\n";
        }

        $sql_tokens = "INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`, `orth_id`) VALUES";
        $sql_tokens_values = array();
        foreach ($tokens as $t){
            $sql_tokens_values[] ="({$t[0]}, {$t[1]}, {$t[2]}, {$t[3]}, {$t[4]})";
        }
        $sql_tokens .= implode(",", $sql_tokens_values);
        $this->db->execute($sql_tokens);

        $tokens_id = array();
        foreach ($this->db->fetch_rows("SELECT token_id FROM tokens WHERE report_id = ? ORDER BY token_id ASC", array($report_id)) as $t){
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
            foreach ($tokens_tags[$i] as $t) {
                $sql_tokens_tags_values[] = "($token_id, {$t[0]}, {$t[1]}, {$t[2]}, \"{$t[3]}\")";
            }
        }
        $sql_tokens_tags .= implode(",", $sql_tokens_tags_values);
        $this->db->execute($sql_tokens_tags);

        // Aktualizacja flag i znaczników
        $sql = "UPDATE reports SET tokenization = ? WHERE id = ?";
        $this->db->execute($sql, array($tokenization, $report_id));

        /** Tokens */
        $sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND (LOWER(short) = 'tokens' OR LOWER(short) = 'token')";
        $corpora_flag_id = $this->db->fetch_one($sql, array($doc['corpora']));
        if ($corpora_flag_id){
            $this->db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)", array($corpora_flag_id, $report_id));
        }
        $this->db->execute("COMMIT");

        return "Document tagged.";
	}

	/**
	 * Przetworzenie dokumentów przy pomocy wybranego modelu Liner2.
	 */
	function processLiner2($report_id, $user_id, $params){
		$model = $params['model'];
		$annotation_set_id = $params['annotation_set_id'];
		
		$content = $this->db->fetch_one("SELECT content FROM reports WHERE id = ?", array($report_id));
		$content = strip_tags($content);
		$content = custom_html_entity_decode($content);
		
		$wsdl = "http://kotu88.ddns.net/nerws/ws/nerws.wsdl";
			
		$liner2 = new WSLiner2($wsdl);	
		$tuples = $liner2->chunk($content, "PLAIN:WCRFT", "TUPLES", $model);
		
		if (preg_match_all("/\((.*),(.*),\"(.*)\"\)/", $tuples, $matches, PREG_SET_ORDER)){
			print "Number of annotations: " . count($matches) . "\n";
			foreach ($matches as $m){
				$annotation_type = strtolower($m[2]);
				list($from, $to) = split(',', $m[1]);
				$ann_text = trim($m[3], '"');
					
				$sql = "SELECT `id` FROM `reports_annotations` " .
						"WHERE `report_id`=? AND `type`=? AND `from`=? AND `to`=?";
				if (count($this->db->fetch_rows($sql, array($report_id, $annotation_type, $from, $to)))==0){					
					$sql = "INSERT INTO `reports_annotations_optimized` " .
							"(`report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`,`source`) VALUES " .
							'(?, (SELECT annotation_type_id FROM annotation_types WHERE name=? AND group_id=?), ?, ?, ?, ?, now(), "new", "bootstrapping")';
					$params = array($report_id, $annotation_type, $annotation_set_id, $from, $to, $ann_text, $user_id);
					$this->db->execute($sql, $params);
				}
			}
			return sprintf("Number of recognized annotations: %d", count($matches));
		}
		return "No annotations were recognized";
	}
	
	/**
	 * Zrzut dokumentów do formatu CCL na potrzeby WCCL Matcha.
	 */
	function processUpdateCcl($report_id){
		global $config;
		$row = $this->db->fetch("SELECT content, corpora FROM reports WHERE id = ?", array($report_id));
		$content = $row['content'];
		$corpus_id = $row['corpora'];
		$content = strip_tags($content);
		$content = custom_html_entity_decode($content);
		
        $nlp = new NlpRest2('wcrft2({"guesser":"false","morfeusz2":"false"})');
        $ccl = $nlp->processSync($content);

		$corpus_dir = sprintf("%s/ccls/corpus%04d", $config->path_secured_data, $corpus_id);
		if ( !file_exists($corpus_dir) ){
			$this->info("Create folder: $corpus_dir");
			mkdir($corpus_dir);
		}
		
		$ccl_file = sprintf("%s/%08d.xml", $corpus_dir, $report_id);
		file_put_contents($ccl_file, $ccl);
		
		return "The ccl was updated";
	}
	
	/**
	 * Wykonuje eksport dokumentów do formatu CCL.
	 * @param unknown $task -- tablica z danymi tasku (pola z tabeli tasks),
	 * @param unknown $params -- tablica z parametrami tasku uzależnionymi od rodzaju taska (sparsowany JSON z pola parameters).
	 */
	function processExport($task, $params){
		global $config;
		$selectors = $params['selectors'];
		$extractors = $params['extractors'];
		$indices = $params['indices'];
		
		$working_path = sprintf("/tmp/inforex_export_%d", $task['task_id']); 
		echo $working_path;
		return true;
	}
}	
