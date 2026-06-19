<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath . "/include/database/CDbKorpuskopRun.php");
require_once($enginePath . "/include/integration/KorpuskopRunner.php");
Config::Cfg()->put_path_engine($enginePath);
Config::Cfg()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");

require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

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

	ini_set('memory_limit', '1024M');
    $opt->parseCli(isset($argv) ? $argv : null);

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
			Config::Cfg()->put_dsn(array(
				'phptype' => 'mysqli',
				'username' => $dbUser,
				'password' => $dbPass,
				'hostspec' => $dbHost . ":" . $dbPort,
				'database' => $dbName
			));
		}else{
			throw new Exception("DB URI is incorrect. Given '$uri', but expected 'user:pass@host:port/name'");
		}
	}
	
	Config::Cfg()->put_verbose($opt->exists("verbose"));
			
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

try{
	$daemon = new TaskDaemon(Config::Cfg()->get_dsn(), Config::Cfg()->get_verbose());
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

		$types = array('liner2', 'export', 'lpmn-postagger', 'upload-zip-txt', 'korpuskop');
		$types = "'" . implode("','", $types) . "'";

		$sql = "SELECT t.*, tr.report_id" .
				" FROM tasks t" .
				" LEFT JOIN tasks_reports tr ON (tr.task_id=t.task_id AND tr.status = 'new')" .
				" WHERE t.type IN ($types) AND t.status <> 'done' AND t.status <> 'error' AND t.status <> 'canceled'" .
				" ORDER BY datetime ASC LIMIT 1";
		$task = $this->db->fetch($sql);
		if ( count($task) == 0 ){
			return false;
		}
        $this->info($task);
	
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
			if ( in_array($task_type, array("liner2", "lpmn-postagger")) ){
				if ( $task['report_id'] ){
					switch ($task_type){
						case "liner2":
                            $message = $this->processLiner2($task['report_id'], $task['user_id'], $params);
							break;
						case "lpmn-postagger":
                            $message = $this->processLpmnPostagger($task['report_id'], $params);
                            break;
					}
                    $currentTaskStatus = $this->db->fetch_one("SELECT status FROM tasks WHERE task_id = ?", array($task['task_id']));
                    if ($currentTaskStatus === 'canceled') {
                        $this->db->update("tasks_reports",
                            array("status"=>"canceled", "message"=>"Task canceled by administrator."),
                            array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
                    } else {
					    $this->db->update("tasks_reports",
							    array("status"=>"done", "message"=>$message),
							    array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
					    $this->db->execute("UPDATE tasks SET current_step=current_step+1 WHERE task_id = ?",
							    array($task['task_id']));
                    }
				}
				else if ($task['status'] == "process") {
					/* Jeżeli nie ma dokumentów do przetworzenia w ramach tasku to ustaw status tasku na zakończony */
                    $currentTaskStatus = $this->db->fetch_one("SELECT status FROM tasks WHERE task_id = ?", array($task['task_id']));
                    if ($currentTaskStatus !== 'canceled') {
					    $this->db->update("tasks", array("status"=>"done"), array("task_id"=>$task['task_id']));
                    }
				}
			}
			/* Corpus-level task */
			else{
				if ( $task_type == "export" ){
					$message = $this->processExport($task, $params);
                    $currentTaskStatus = $this->db->fetch_one("SELECT status FROM tasks WHERE task_id = ?", array($task['task_id']));
                    if ($currentTaskStatus !== 'canceled') {
					    $this->db->update("tasks", array("status"=>"done"), array("task_id"=>$task['task_id']));
                    }
				} else if ( $task_type == "upload-zip-txt" ){
                    $oTask = new TableTask($task['task_id']);
				    $taskProcessor = new TaskProcessorUploadZipTxt($oTask);
                    $taskProcessor->run();

                    print_r("done");
                    $currentTaskStatus = $this->db->fetch_one("SELECT status FROM tasks WHERE task_id = ?", array($task['task_id']));
                    if ($currentTaskStatus !== 'canceled') {
                        $oTask->setStatus("done");
                        $oTask->update();
                    }
                    //$this->db->update("tasks", array("status"=>"done"), array("task_id"=>$task['task_id']));
                } else if ( $task_type == "korpuskop" ){
                    $this->processKorpuskop($task, $params);
                }
			}
		}
		catch(Exception $ex){
			$this->info("Exception: " . $ex->getMessage());
            $currentTaskStatus = $this->db->fetch_one("SELECT status FROM tasks WHERE task_id = ?", array($task['task_id']));
            if ($currentTaskStatus === 'canceled') {
                return false;
            }
			
			if ( $task['report_id'] && $ex->getMessage() == "TIMEOUT" ){
				$this->db->update("tasks_reports", 
						array("status"=>"new"), 
						array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));
			}
			else if ( $task['report_id'] ){
				$this->db->update("tasks_reports", 
						array("status"=>"error"), 
						array("task_id"=>$task['task_id'], "report_id"=>$task['report_id']));				
			}
			else{
				$this->db->update("tasks", array("status"=>"error", "message"=>$ex->getMessage()), array("task_id"=>$task['task_id']));
			}
			return false;
		}			
			
		return true;
	}

    function processLpmnPostagger($report_id, $params){
        echo "Starting document " . $report_id . " with LPMN postagger\n";

        $doc = $this->db->fetch("SELECT * FROM reports WHERE id=?", array($report_id));
        $text = $this->normalizeReportText($doc[DB_COLUMN_REPORTS__CONTENT]);
        if ($doc[DB_COLUMN_REPORTS__FORMAT_ID] != DB_REPORT_FORMATS_PLAIN) {
            $text = strip_tags($text);
            $text = html_entity_decode($text);
        }
        $text = trim($text);
        $text = preg_replace("/\n{2,}/", "\n", $text);

        $taggerType = $params['tagger_type'];
        $language = $params['language'];
        $tagsetName = $params['tagset'];
        $tagset_id = $this->getOrCreateTagsetId($tagsetName);

        $text_tagged = $this->tokenizeWithLpmnPostagger($text, $tagsetName, $taggerType, $language);
        $tokenData = $this->collectTokensFromLpmnJson($text_tagged, $report_id, $tagset_id, $taggerType);

        $this->db->execute("BEGIN");
        try {
            DbToken::deleteReportTokens($report_id);

            $this->insertMissingBases($tokenData['new_bases']);
            $this->insertMissingCtags($tokenData['new_ctags'], $tagset_id);
            $this->insertMissingOrths($tokenData['new_orths']);

            $token_ids = $this->insertLpmnTokens($report_id, $tokenData['tokens']);
            $this->insertLpmnTokenTags($token_ids, $tokenData['tokens_tags']);

            $tokenization = $this->getLpmnTokenizationName($taggerType, $language, $tagsetName);
            $this->db->execute("UPDATE reports SET tokenization = ? WHERE id = ?", array($tokenization, $report_id));

            $sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND (LOWER(short) = 'tokens' OR LOWER(short) = 'token')";
            $corpora_flag_id = $this->db->fetch_one($sql, array($doc['corpora']));
            if ($corpora_flag_id) {
                $this->db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)", array($corpora_flag_id, $report_id));
            }

            $this->db->execute("COMMIT");
        } catch (Exception $ex) {
            $this->db->execute("ROLLBACK");
            throw $ex;
        }

        echo "Tokens: " . count($token_ids) . "\n";

        return "Document tagged with " . $tokenization . ".";
    }

    function tokenizeWithLpmnPostagger($text, $tagsetName, $taggerType, $language){
        $client = (new \Inforex\Lpmn\LpmnClientBuilder())->build();
        $properties = (new \Inforex\Lpmn\Pipeline\PosTaggerPropertiesBuilder())
            ->methodTagger()
            ->language($language)
            ->taggerType($taggerType)
            ->outputFormat('json');
        if (!in_array($taggerType, array('archeopteryx', 'llm-pos-tagger'), true)) {
            $properties->tagset($tagsetName);
        }

        $pipeline = (new \Inforex\Lpmn\Pipeline\PipelineBuilder())
            ->any2Txt()
            ->postagger($properties->build())
            ->build();

        $taskOptions = (new \Inforex\Lpmn\Request\TaskOptions())->withApplication('postagger');
        $client->runTask(\Inforex\Lpmn\Pipeline\InputType::TEXT, $text, $pipeline, $taskOptions);

        return $client->downloadResults();
    }

    function collectTokensFromLpmnJson($text_tagged, $report_id, $tagset_id, $taggerType){
        $data = json_decode($text_tagged, true);
        if (!is_array($data) || !isset($data['tokens']) || !is_array($data['tokens'])) {
            throw new Exception("Failed to decode LPMN JSON tokenization result.");
        }

        $tokenLayers = $data['tokens'];
        $jsonTokens = isset($tokenLayers['default']) ? $tokenLayers['default'] : reset($tokenLayers);
        if (!is_array($jsonTokens) || count($jsonTokens) === 0) {
            throw new Exception("LPMN JSON tokenization result contains no tokens.");
        }

        usort($jsonTokens, function ($a, $b) {
            $startA = isset($a['start']) ? (int) $a['start'] : 0;
            $startB = isset($b['start']) ? (int) $b['start'] : 0;
            if ($startA === $startB) {
                $idA = isset($a['id']) ? (int) $a['id'] : 0;
                $idB = isset($b['id']) ? (int) $b['id'] : 0;
                return $idA - $idB;
            }
            return $startA - $startB;
        });

        $sentenceStops = array();
        if (isset($data['spans']['sentence']) && is_array($data['spans']['sentence'])) {
            foreach ($data['spans']['sentence'] as $sentence) {
                if (isset($sentence['stop'])) {
                    $sentenceStops[(int) $sentence['stop']] = true;
                }
            }
        }

        $sourceText = isset($data['text']) ? $data['text'] : '';
        $offsetSourceText = $taggerType === 'spacy' ? $this->removeNewLines($sourceText) : $sourceText;
        $lastTokenIndex = count($jsonTokens) - 1;
        $tokens = array();
        $tokens_tags = array();
        $new_bases = array();
        $new_ctags = array();
        $new_orths = array();
        $pendingToken = null;

        foreach ($jsonTokens as $index => $jsonToken) {
            if (!isset($jsonToken['start']) || !isset($jsonToken['stop'])) {
                throw new Exception("LPMN JSON token does not contain start/stop offsets.");
            }

            $sourceStart = $this->getTokenSourceStart($jsonToken, $taggerType);
            $stop = (int) $jsonToken['stop'];
            if ($stop < $sourceStart) {
                throw new Exception("LPMN JSON token has invalid offsets: start=$sourceStart stop=$stop.");
            }

            $sourceStop = $this->getTokenSourceStop($jsonToken, $offsetSourceText, $sourceStart);
            $orth = $this->removeWhitespace(mb_substr($offsetSourceText, $sourceStart, $sourceStop - $sourceStart, 'utf-8'));
            if ($orth === '') {
                continue;
            }

            if ($pendingToken !== null && $this->shouldMergeToken($pendingToken, $sourceStart, $orth, $offsetSourceText)) {
                $pendingToken['source_stop'] = $sourceStop;
                $pendingToken['stop'] = $stop;
                $pendingToken['orth'] .= $orth;
                $pendingToken['merged'] = true;
                continue;
            }

            if ($pendingToken !== null) {
                $this->appendLpmnToken($pendingToken, $report_id, $offsetSourceText, $sentenceStops, $lastTokenIndex, $tokens, $tokens_tags, $new_bases, $new_ctags, $new_orths, $tagset_id);
            }

            $pendingToken = array(
                'index' => $index,
                'json_token' => $jsonToken,
                'source_start' => $sourceStart,
                'source_stop' => $sourceStop,
                'stop' => $stop,
                'orth' => $orth,
                'merged' => false,
            );
        }

        if ($pendingToken !== null) {
            $this->appendLpmnToken($pendingToken, $report_id, $offsetSourceText, $sentenceStops, $lastTokenIndex, $tokens, $tokens_tags, $new_bases, $new_ctags, $new_orths, $tagset_id);
        }

        return array(
            'tokens' => $tokens,
            'tokens_tags' => $tokens_tags,
            'new_bases' => $new_bases,
            'new_ctags' => $new_ctags,
            'new_orths' => $new_orths,
        );
    }

    function appendLpmnToken($parsedToken, $report_id, $sourceText, $sentenceStops, $lastTokenIndex, &$tokens, &$tokens_tags, &$new_bases, &$new_ctags, &$new_orths, $tagset_id){
        $jsonToken = $parsedToken['json_token'];
        $sourceStart = $parsedToken['source_start'];
        $sourceStop = $parsedToken['source_stop'];
        $stop = $parsedToken['stop'];
        $orth = $parsedToken['orth'];

        $from = $this->getOffsetWithoutWhitespace($sourceText, $sourceStart);
        $to = $this->getOffsetWithoutWhitespace($sourceText, $sourceStop) - 1;
        $lastToken = isset($sentenceStops[$stop]) || isset($sentenceStops[$sourceStop]) || ($parsedToken['index'] === $lastTokenIndex && count($sentenceStops) === 0) ? 1 : 0;

        $orthEscaped = $this->db->escape($orth);
        $new_orths[$orth] = 1;
        $orth_sql = "(SELECT orth_id FROM orths WHERE orth='" . $orthEscaped . "')";
        $tokens[] = array($report_id, $from, $to, $lastToken, $orth_sql);

        $lexemes = isset($jsonToken['lexemes']) && is_array($jsonToken['lexemes']) ? $jsonToken['lexemes'] : array();
        if (count($lexemes) === 0 || !empty($parsedToken['merged'])) {
            $lexemes = array(array('lemma' => $orth, 'pos' => 'ign', 'disamb' => true));
        }

        $lexemes = $this->filterIgnLexemes($lexemes);
        $tags_args = array();

        foreach ($lexemes as $lexeme) {
            $base = isset($lexeme['lemma']) ? strval($lexeme['lemma']) : '';
            $ctag = isset($lexeme['pos']) ? strval($lexeme['pos']) : '';
            if ($base === '' || $ctag === '') {
                continue;
            }

            $baseEscaped = $this->db->escape($base);
            $ctagEscaped = $this->db->escape($ctag);
            $cts = explode(":", $ctag);
            $pos = $this->db->escape($cts[0]);
            $disamb = !empty($lexeme['disamb']) ? "true" : "false";

            $new_bases[$base] = 1;
            $new_ctags[$ctag] = 1;
            $base_sql = "(SELECT id FROM bases WHERE text='" . $baseEscaped . "')";
            $ctag_sql = '(SELECT id FROM tokens_tags_ctags WHERE ctag="' . $ctagEscaped . '" AND tagset_id = ' . $tagset_id . ')';
            $tags_args[] = array($base_sql, $ctag_sql, $disamb, $pos);
        }

        if (count($tags_args) === 0) {
            throw new Exception("LPMN JSON token has no valid lexemes.");
        }

        $tokens_tags[] = $tags_args;
    }

    function insertMissingBases($new_bases){
        if (count($new_bases) === 0) {
            return;
        }

        $values = array();
        foreach (array_keys($new_bases) as $base) {
            $values[] = $this->db->escape($base);
        }

        $this->db->execute('INSERT IGNORE INTO `bases` (`text`) VALUES ("' . implode('"),("', $values) . '");');
    }

    function insertMissingCtags($new_ctags, $tagset_id){
        if (count($new_ctags) === 0) {
            return;
        }

        $values = array();
        foreach (array_keys($new_ctags) as $ctag) {
            $values[] = $this->db->escape($ctag);
        }

        $this->db->execute('INSERT IGNORE INTO `tokens_tags_ctags` (`ctag`, `tagset_id`) VALUES ("' .
            implode('",' . $tagset_id . '),("', $values) . '",' . $tagset_id . ');');
    }

    function insertMissingOrths($new_orths){
        if (count($new_orths) === 0) {
            return;
        }

        $values = array();
        foreach (array_keys($new_orths) as $orth) {
            $values[] = $this->db->escape($orth);
        }

        $this->db->execute('INSERT IGNORE INTO `orths` (`orth`) VALUES ("' . implode('"),("', $values) . '");');
    }

    function insertLpmnTokens($report_id, $tokens){
        if (count($tokens) === 0) {
            throw new Exception("No tokens to insert.");
        }

        $values = array();
        foreach ($tokens as $token) {
            $values[] = "({$token[0]}, {$token[1]}, {$token[2]}, {$token[3]}, {$token[4]})";
        }

        $this->db->execute("INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`, `orth_id`) VALUES" . implode(",", $values));

        return $this->db->fetch_ones("SELECT token_id FROM tokens WHERE report_id = ? ORDER BY token_id ASC", "token_id", array($report_id));
    }

    function insertLpmnTokenTags($token_ids, $tokens_tags){
        $values = array();
        for ($i = 0; $i < count($token_ids); $i++) {
            if (!isset($tokens_tags[$i]) || count($tokens_tags[$i]) === 0) {
                throw new Exception("Data consistency error: missing tags for token index $i");
            }

            foreach ($tokens_tags[$i] as $tag) {
                $values[] = "({$token_ids[$i]}, {$tag[0]}, {$tag[1]}, {$tag[2]}, \"{$tag[3]}\")";
            }
        }

        if (count($values) === 0) {
            throw new Exception("No token tags to insert.");
        }

        $this->db->execute("INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag_id`,`disamb`,`pos`) VALUES " . implode(",", $values));
    }

    function getOrCreateTagsetId($tagsetName){
        $tagsetId = DbTagset::getTagsetId($tagsetName);
        if ($tagsetId) {
            return $tagsetId;
        }

        if ($tagsetName !== 'ud') {
            throw new Exception("Tagset '" . $tagsetName . "' not found.");
        }

        $this->db->execute("INSERT INTO tagsets (name) VALUES (?)", array($tagsetName));

        return DbTagset::getTagsetId($tagsetName);
    }

    function normalizeReportText($text){
        return strtr($text, array(
            "&oacute;" => "ó",
            "&ndash;" => "-",
            "&hellip;" => "…",
            "&sect;" => "§",
            "&Oacute;" => "Ó",
            "&sup2;" => "²",
            "&ldquo;" => "“",
            "&bull;" => "•",
            "&middot;" => "·",
            "&rsquo;" => "’",
            "&nbsp;" => " ",
            "&Uuml;" => "ü",
            "&apos;" => "'",
            "<br/>" => " ",
            "& " => "&amp; ",
        ));
    }

    function getLpmnTokenizationName($taggerType, $language, $tagsetName){
        return "lpmn:$taggerType:$language:$tagsetName";
    }

    function getTokenSourceStart($jsonToken, $taggerType){
        $start = (int) $jsonToken['start'];
        if ($taggerType === 'spacy') {
            return max(0, $start - 1);
        }

        return $start;
    }

    function getTokenSourceStop($jsonToken, $sourceText, $sourceStart){
        $stop = (int) $jsonToken['stop'];
        if ($this->isHyphenAtOffset($sourceText, $stop)) {
            return $stop + 1;
        }

        return $stop === $sourceStart ? $stop + 1 : $stop;
    }

    function shouldMergeToken($previousToken, $sourceStart, $orth, $sourceText){
        $gap = mb_substr($sourceText, $previousToken['source_stop'], $sourceStart - $previousToken['source_stop'], 'utf-8');
        if (!$this->isLineBreakGap($gap) && !$this->isLikelySplitSingleLetter($previousToken, $sourceStart, $orth, $sourceText)) {
            return false;
        }

        if (!empty($previousToken['merged'])) {
            return $this->startsWithLowercaseLetter($orth);
        }

        return mb_strlen($previousToken['orth'], 'utf-8') === 1 || $this->endsWithHyphen($previousToken['orth']);
    }

    function filterIgnLexemes($lexemes){
        $ign = null;
        $tags_ign_disamb = array();

        foreach ($lexemes as $tag) {
            $ctag = isset($tag['pos']) ? $tag['pos'] : null;
            $disamb = !empty($tag['disamb']);
            if ($ctag == "ign") {
                $ign = $tag;
            }
            if ($ctag == "ign" || $disamb) {
                $tags_ign_disamb[] = $tag;
            }
        }

        return $ign ? $tags_ign_disamb : $lexemes;
    }

    function getOffsetWithoutWhitespace($text, $offset){
        return mb_strlen($this->removeWhitespace(mb_substr($text, 0, $offset, 'utf-8')), 'utf-8');
    }

    function removeWhitespace($text){
        return preg_replace('/\s+/u', '', $text);
    }

    function removeNewLines($text){
        return str_replace(array("\r\n", "\n", "\r", "\f", "\v"), '', $text);
    }

    function isLineBreakGap($gap){
        return strpos($gap, "\n") !== false || strpos($gap, "\r") !== false || strpos($gap, "\f") !== false || strpos($gap, "\v") !== false;
    }

    function isLikelySplitSingleLetter($previousToken, $sourceStart, $orth, $sourceText){
        if (mb_strlen($previousToken['orth'], 'utf-8') !== 1 || !$this->startsWithLowercaseLetter($orth)) {
            return false;
        }
        if ($this->isCommonOneLetterWord($previousToken['orth'])) {
            return false;
        }
        if (!$this->startsWithUppercaseLetter($previousToken['orth']) && !$this->isLineInitialToken($sourceText, $previousToken['source_start'])) {
            return false;
        }
        if ($previousToken['source_stop'] === $sourceStart) {
            return true;
        }

        $gap = mb_substr($sourceText, $previousToken['source_stop'], 1, 'utf-8');
        return preg_match('/^\s$/u', $gap) === 1;
    }

    function startsWithLowercaseLetter($text){
        return preg_match('/^\p{Ll}/u', $text) === 1;
    }

    function startsWithUppercaseLetter($text){
        return preg_match('/^\p{Lu}/u', $text) === 1;
    }

    function isLineInitialToken($sourceText, $sourceStart){
        $prefix = mb_substr($sourceText, 0, $sourceStart, 'utf-8');
        return preg_match('/(^|[\r\n\f\v])\s*$/u', $prefix) === 1;
    }

    function isCommonOneLetterWord($text){
        return in_array(mb_strtolower($text, 'utf-8'), array('a', 'i', 'o', 'u', 'w', 'z'), true);
    }

    function endsWithHyphen($text){
        return preg_match('/[-‐‑‒–—]$/u', $text) === 1;
    }

    function isHyphenAtOffset($text, $offset){
        return $this->endsWithHyphen(mb_substr($text, $offset, 1, 'utf-8'));
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
	 * Wykonuje eksport dokumentów do formatu CCL.
	 * @param unknown $task -- tablica z danymi tasku (pola z tabeli tasks),
	 * @param unknown $params -- tablica z parametrami tasku uzależnionymi od rodzaju taska (sparsowany JSON z pola parameters).
	 */
	private function updateKorpuskopTaskState($task_id, $status, $current_step, $max_steps, $payload){
		$this->db->update("tasks", array(
			"status" => $status,
			"current_step" => max(0, intval($current_step)),
			"max_steps" => max(1, intval($max_steps)),
			"message" => json_encode($payload),
		), array("task_id" => $task_id));
	}

	private function updateKorpuskopRunState($task, $values){
		$existingRun = DbKorpuskopRun::getRunByTask($task['task_id'], $task['corpus_id']);
		if ($existingRun && isset($existingRun['run_id'])) {
			DbKorpuskopRun::updateRunByTask($task['task_id'], $task['corpus_id'], $values);
		}
	}

	private function storeKorpuskopRun($task, $params, $inputKind, $result, $status, $exitCode, $message){
		$values = array(
			'user_id' => $task['user_id'],
			'input_path' => $params['input'],
			'input_kind' => $inputKind,
			'output_path' => $params['output'],
			'config_json_path' => isset($params['config_json']) ? $params['config_json'] : null,
			'progress_file' => $result ? $result['progress_file'] : null,
			'status' => $status,
			'exit_code' => $exitCode,
			'message' => $message,
			'file_size' => is_file($params['output']) ? filesize($params['output']) : null,
			'finished_at' => date('Y-m-d H:i:s'),
		);

		$existingRun = DbKorpuskopRun::getRunByTask($task['task_id'], $task['corpus_id']);
		if ($existingRun && isset($existingRun['run_id'])) {
			DbKorpuskopRun::updateRunByTask($task['task_id'], $task['corpus_id'], $values);
			return intval($existingRun['run_id']);
		}

		return DbKorpuskopRun::insertRun(array_merge($values, array(
			'task_id' => $task['task_id'],
			'corpus_id' => $task['corpus_id'],
			'created_at' => isset($task['datetime']) ? $task['datetime'] : date('Y-m-d H:i:s'),
		)));
	}

	function processKorpuskop($task, $params){
		$runner = new KorpuskopRunner();
		$inputKind = isset($params['input_kind']) ? $params['input_kind'] : KorpuskopRunner::INPUT_KIND_AUTO;
		try{
			$this->updateKorpuskopTaskState($task['task_id'], 'process', 1, 100, array(
				'stage' => 'queue_dequeued',
				'message' => 'Zadanie Korpuskop zostało pobrane z kolejki Inforex.'
			));
			$this->updateKorpuskopRunState($task, array(
				'status' => 'process',
				'message' => 'Zadanie Korpuskop zostało pobrane z kolejki Inforex.',
				'finished_at' => null,
			));

			if ( $inputKind == KorpuskopRunner::INPUT_KIND_AUTO ){
				$inputKind = $runner->detectInputKind($params['input']);
			}

			$this->updateKorpuskopTaskState($task['task_id'], 'process', 5, 100, array(
				'stage' => 'inforex_input_detection',
				'input_kind' => $inputKind,
				'message' => 'Rozpoznano wariant eksportu Inforex.'
			));
			$this->updateKorpuskopRunState($task, array(
				'status' => 'process',
				'input_kind' => $inputKind,
				'message' => 'Rozpoznano wariant eksportu Inforex.',
			));

			$extraArgs = array();
			if ( isset($params['threads']) && $params['threads'] ){
				$extraArgs['threads'] = intval($params['threads']);
			}
			if ( isset($params['limit_corpus_size']) && $params['limit_corpus_size'] ){
				$extraArgs['limit-corpus-size'] = intval($params['limit_corpus_size']);
			}
            if (isset($params['focus_words']) && is_array($params['focus_words']) && !empty($params['focus_words'])) {
                $extraArgs['corpus-focus-word'] = $params['focus_words'];
            }

			$result = $runner->runWithProgress(
				isset($params['config_json']) ? $params['config_json'] : null,
				$runner->buildInforexExportArgs($params['input'], $params['output'], $inputKind, $extraArgs),
				function($event) use ($task){
					$current = isset($event['current']) ? intval($event['current']) : 0;
					$total = isset($event['total']) ? max(1, intval($event['total'])) : 100;
					$this->updateKorpuskopTaskState($task['task_id'], 'process', $current, $total, $event);
					$this->updateKorpuskopRunState($task, array(
						'status' => 'process',
						'message' => isset($event['message']) ? $event['message'] : json_encode($event),
					));
				}
			);

			$stderrMessage = !empty($result['stderr_lines']) ? implode("\n", $result['stderr_lines']) : '';
			if ( intval($result['exit_code']) === 0 ){
				$runId = $this->storeKorpuskopRun($task, $params, $inputKind, $result, 'done', 0, $stderrMessage);
				$this->updateKorpuskopTaskState($task['task_id'], 'done', 100, 100, array(
					'stage' => 'done',
					'run_id' => $runId,
					'input_kind' => $inputKind,
					'progress_file' => $result['progress_file'],
					'message' => 'Raport Korpuskop został wygenerowany.'
				));
			}
			else{
				$runId = $this->storeKorpuskopRun($task, $params, $inputKind, $result, 'error', intval($result['exit_code']), $stderrMessage);
				$this->updateKorpuskopTaskState($task['task_id'], 'error', 100, 100, array(
					'stage' => 'error',
					'run_id' => $runId,
					'input_kind' => $inputKind,
					'progress_file' => $result['progress_file'],
					'message' => $stderrMessage !== '' ? $stderrMessage : 'Proces Korpuskop zakończył się błędem.'
				));
			}
		}
		catch(Exception $ex){
			$this->storeKorpuskopRun($task, $params, $inputKind, null, 'error', 1, $ex->getMessage());
			$this->updateKorpuskopTaskState($task['task_id'], 'error', 100, 100, array(
				'stage' => 'error',
				'input_kind' => $inputKind,
				'message' => $ex->getMessage()
			));
		}
	}

	function processExport($task, $params){
		$selectors = $params['selectors'];
		$extractors = $params['extractors'];
		$indices = $params['indices'];
		
		$working_path = sprintf("/tmp/inforex_export_%d", $task['task_id']); 
		echo $working_path;
		return true;
	}
}	
