<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
use Inforex\Lpmn\LpmnClientBuilder;
use Inforex\Lpmn\Pipeline\InputType;
use Inforex\Lpmn\Pipeline\PipelineBuilder;
use Inforex\Lpmn\Pipeline\PosTaggerPropertiesBuilder;
use Inforex\Lpmn\Request\TaskOptions;

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
Config::Cfg()->put_path_engine($enginePath);
Config::Cfg()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");

require_once($enginePath . "/cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
$opt = create_cli_options();
try {
    configure_from_cli($opt, isset($argv) ? $argv : null);
} catch (Exception $ex) {
    print "!! " . $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    print("\n");
    return;
}

function main ($config){

	try{
		$db = new Database($config->get_dsn());
	}catch(Exception $ex){
		echo "Error: 'Database connection failed'\n";
		echo "in: ".$ex->getFile().", line: ". $ex->getLine()." (tokenize.php:110)\n";
		exit();
	}
	$GLOBALS['db'] = $db;

	$reports = DbReport::getReports($config->get_corpus(),$config->get_subcorpus(),$config->get_documents(), $config->get_flags());

	$tagset_id = get_or_create_tagset_id($db, $config->get_tagsetName());

	if(!$tagset_id){
        echo "Error: Tagset '".$config->get_tagsetName()."' not found in table 'tagsets'\n";
        echo "in: (tokenize.php:127)\n";
        exit();
	}

	tag_documents($config, $reports, $tagset_id);
} 

function create_cli_options()
{
    $opt = new Cliopt();
    $opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
    $opt->addParameter(new ClioptParameter("tagger", "T", "(morphodita|ptag|archeopteryx|llm-pos-tagger|spacy)", "LPMN tagger type; default: morphodita"));
    $opt->addParameter(new ClioptParameter("language", "l", "code", "language code; morphodita: pl; ptag: pl; archeopteryx: pl; llm-pos-tagger: pl; spacy: en,de,pl,ru,pt,fr,es"));
    $opt->addParameter(new ClioptParameter("tagset", null, "(nkjp|sgjp|ud)", "tagset; morphodita: nkjp,sgjp; ptag/archeopteryx/llm-pos-tagger: nkjp; spacy: ud"));
    $opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
    $opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
    $opt->addParameter(new ClioptParameter("document", "d", "id", "id of the document"));
    $opt->addParameter(new ClioptParameter("user", "u", "id", "id of the user"));
    $opt->addParameter(new ClioptParameter("discard-sentence-tags", null, null, "discard sentence tags before tokenize"));
    $opt->addParameter(new ClioptParameter("insert-sentence-tags", "S", null, "adds <sentence> tags into document content"));
    $opt->addParameter(new ClioptParameter("debug-tokens", null, null, "print token offsets parsed from LPMN JSON"));
    $opt->addParameter(new ClioptParameter("dump-lpmn-json", null, null, "print raw LPMN JSON response and skip database writes"));
    $opt->addParameter(new ClioptParameter("flag", "F", "flag", "tokenize using flag \"flag name\"=flag_value or \"flag name\"=flag_value1,flag_value2,..."));

    return $opt;
}

function get_or_create_tagset_id($db, $tagsetName)
{
    $tagset_id = DbTagset::getTagsetId($tagsetName);
    if ($tagset_id || $tagsetName !== 'ud') {
        return $tagset_id;
    }

    $db->execute("INSERT INTO tagsets (name) VALUES (?)", array($tagsetName));

    return DbTagset::getTagsetId($tagsetName);
}

function configure_from_cli($opt, $argv)
{
    $opt->parseCli($argv);

    Config::Cfg()->put_dsn(parse_dsn($opt));
    $taggerType = strtolower($opt->getOptional("tagger", "morphodita"));
    $language = strtolower($opt->getOptional("language", "pl"));
    $lpmnTagsetName = strtolower($opt->getOptional("tagset", get_default_lpmn_tagset_name($taggerType)));
    Config::Cfg()->put_lpmn_tagger_type($taggerType);
    Config::Cfg()->put_lpmn_tagger_language($language);
    Config::Cfg()->put_lpmn_tagsetName($lpmnTagsetName);
    Config::Cfg()->put_tagsetName($lpmnTagsetName);
    Config::Cfg()->put_discardSentenceTags($opt->exists("discard-sentence-tags"));
    Config::Cfg()->put_insertSentenceTags($opt->exists("insert-sentence-tags"));
    Config::Cfg()->put_debugTokens($opt->exists("debug-tokens"));
    Config::Cfg()->put_dumpLpmnJson($opt->exists("dump-lpmn-json"));
    Config::Cfg()->put_corpus($opt->getParameters("corpus"));
    Config::Cfg()->put_documents($opt->getParameters("document"));
    Config::Cfg()->put_subcorpus($opt->getParameters("subcorpus"));
    Config::Cfg()->put_user($opt->getOptional("user", "1"));
    Config::Cfg()->put_flags(parse_flags($opt));

    if (!Config::Cfg()->get_corpus() && !Config::Cfg()->get_subcorpus() && !Config::Cfg()->get_documents()) {
        throw new Exception("No corpus, subcorpus nor document id set");
    }

    validate_lpmn_tagger_config(
        Config::Cfg()->get_lpmn_tagger_type(),
        Config::Cfg()->get_lpmn_tagger_language(),
        Config::Cfg()->get_lpmn_tagsetName()
    );

}

function parse_dsn($opt)
{
    $dbHost = "db";
    $dbUser = "inforex";
    $dbPass = "password";
    $dbName = "inforex";
    $dbPort = "3306";

    if ($opt->exists("db-uri")) {
        $uri = $opt->getRequired("db-uri");
        if (!preg_match("/(.+):(.+)@(.*):(.*)\/(.*)/", $uri, $m)) {
            throw new Exception("DB URI is incorrect. Given '$uri', but expected 'user:pass@host:port/name'");
        }

        $dbUser = $m[1];
        $dbPass = $m[2];
        $dbHost = $m[3];
        $dbPort = $m[4];
        $dbName = $m[5];
    }

    return array(
        'phptype' => 'mysqli',
        'username' => $dbUser,
        'password' => $dbPass,
        'hostspec' => $dbHost . ":" . $dbPort,
        'database' => $dbName,
    );
}

function parse_flags($opt)
{
    if (!$opt->exists("flag")) {
        return null;
    }

    $flags = array();
    foreach ($opt->getParameters("flag") as $flag) {
        if (!preg_match("/(.+)=(.+)/", $flag, $match)) {
            throw new Exception("Flag is incorrect. Given '$flag', but expected 'name=value'");
        }

        $flag_name = $match[1];
        if (!array_key_exists($flag_name, $flags)) {
            $flags[$flag_name] = array();
        }

        if (preg_match_all("/(?P<digit>\d+)/", $match[2], $values)) {
            foreach ($values['digit'] as $digit) {
                $flags[$flag_name][] = $digit;
            }
        }
    }

    return $flags;
}

function validate_lpmn_tagger_config($taggerType, $language, $tagsetName)
{
    $taggerType = strtolower($taggerType);
    $language = strtolower($language);
    $tagsetName = strtolower($tagsetName);

    if (!in_array($taggerType, array('morphodita', 'ptag', 'archeopteryx', 'llm-pos-tagger', 'spacy'), true)) {
        throw new Exception("Unsupported LPMN tagger type: $taggerType");
    }

    if ($taggerType === 'morphodita') {
        if ($language !== 'pl') {
            throw new Exception("MorphoDita supports only the pl language");
        }
        if (!in_array($tagsetName, array('nkjp', 'sgjp'), true)) {
            throw new Exception("MorphoDita supports only these tagsets: nkjp,sgjp");
        }
    }

    if ($taggerType === 'ptag') {
        if ($language !== 'pl') {
            throw new Exception("PTag supports only the pl language");
        }
        if ($tagsetName !== 'nkjp') {
            throw new Exception("PTag supports only the nkjp tagset");
        }
    }

    if ($taggerType === 'archeopteryx') {
        if ($language !== 'pl') {
            throw new Exception("Archeopteryx supports only the pl language");
        }
        if ($tagsetName !== 'nkjp') {
            throw new Exception("Archeopteryx supports only the nkjp tagset");
        }
    }

    if ($taggerType === 'llm-pos-tagger') {
        if ($language !== 'pl') {
            throw new Exception("LLM POS Tagger supports only the pl language");
        }
        if ($tagsetName !== 'nkjp') {
            throw new Exception("LLM POS Tagger supports only the nkjp tagset");
        }
    }

    if ($taggerType === 'spacy') {
        $spacyLanguages = get_spacy_languages();
        if (!in_array($language, $spacyLanguages, true)) {
            throw new Exception("spaCy supports only these languages: " . implode(',', $spacyLanguages));
        }
        if ($tagsetName !== 'ud') {
            throw new Exception("spaCy supports only the ud tagset");
        }
    }
}

function get_default_lpmn_tagset_name($taggerType)
{
    if ($taggerType === 'spacy') {
        return 'ud';
    }

    return 'nkjp';
}

function get_spacy_languages()
{
    return array(
        'en',
        'de',
        'pl',
        'ru',
        'pt',
        'fr',
        'es',
    );
}

function tag_documents($config, $reports, $tagset_id){
	$n = 0;
    $count = count($reports);

    if ($count === 0) {
        echo "No reports to tokenize.\n";
        return;
    }

	foreach ($reports as $report){
		$report_id = $report['id'];
		$db = new Database($config->get_dsn());
        $GLOBALS['db'] = $db;
		echo "\r " . (++$n) . " z " . $count . " :  id=$report_id  ";
		progress(($n-1),$count);
		
		try{
			$tokens_id = tokenize_report($config, $db, $report, $tagset_id);
			echo "Tokens: " . count($tokens_id) . "\n";
		}
		catch(Exception $ex){
			echo "\n";
			echo "-------------------------------------------------------------\n";
			echo "!! Exception @ id = {$report_id}\n";
			echo "   " . $ex->getMessage() . "\n";
			echo "-------------------------------------------------------------\n";
		}
	}
	echo "\r End tokenize " . ($n) . " z " . $count ;
	progress(($n),$count);
	echo "\n";
}

function tokenize_report($config, $db, $report, $tagset_id)
{
    $report_id = $report['id'];
    if ($report["format"] != "plain") {
        throw new Exception("Only plain text reports are supported for MorphoDita.");
    }

    $doc = $db->fetch("SELECT * FROM reports WHERE id=?", array($report_id));
    $text = normalize_report_text($doc['content']);

    if ($config->get_discardSentenceTags() && !$config->get_insertSentenceTags()) {
        $text = preg_replace("/(<sentence>)(.*)?(<\/sentence>)/", "$2", $text);
        $t_report = new TableReport($report_id);
        $t_report->content = $text;
        $t_report->save();
        DbReport::updateFlagByShort($t_report->id, "Sent", "NIE GOTOWY");
    }

    $useSentencer = strpos($text, "<sentence>") === false;
    $index_bases = load_bases_index($db);
    $index_ctags = load_ctags_index($db, $tagset_id);

    $taggerType = $config->get_lpmn_tagger_type();
    $language = $config->get_lpmn_tagger_language();
    $lpmnTagsetName = $config->get_lpmn_tagsetName();
    $text_tagged = tokenize_with_lpmn_tagger($text, $lpmnTagsetName, $taggerType, $language);
    if ($config->get_dumpLpmnJson()) {
        echo $text_tagged . "\n";
        return array();
    }

    $tokenData = collect_tokens_from_json($text_tagged, $report_id, $db, $tagset_id, $index_bases, $index_ctags, $taggerType, $config->get_debugTokens());

    $db->execute("BEGIN");
    try {
        DbToken::deleteReportTokens($report_id);
        insert_missing_bases($db, $tokenData['new_bases']);
        insert_missing_ctags($db, $tokenData['new_ctags'], $tagset_id);
        $token_ids = insert_tokens($db, $report_id, $tokenData['tokens']);
        insert_token_tags($db, $token_ids, $tokenData['tokens_tags']);
        update_tokenization_status($db, $doc['corpora'], $report_id, get_tokenization_name($taggerType, $language, $lpmnTagsetName));

        if ($config->get_insertSentenceTags() && $useSentencer) {
            Premorph::set_sentence_tag($report_id, $config->get_user());
        }

        $db->execute("COMMIT");
    } catch (Exception $ex) {
        $db->execute("ROLLBACK");
        throw $ex;
    }

    return $token_ids;
}

function progress($act_num,$all){
	echo " " . number_format(($act_num/$all)*100, 2)."%    ";	
}

function normalize_report_text($text)
{
    $replacements = array(
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
        "<br/>" => " ",
        "& " => "&amp; ",
    );

    return strtr($text, $replacements);
}

function load_bases_index($db)
{
    $index = array();
    foreach ($db->fetch_rows("SELECT * FROM bases") as $base) {
        $index[$base['text']] = $base['id'];
    }

    return $index;
}

function load_ctags_index($db, $tagset_id)
{
    $index = array();
    foreach ($db->fetch_rows("SELECT * FROM tokens_tags_ctags WHERE tagset_id = " . $tagset_id) as $ctag) {
        $index[$ctag['ctag']] = $ctag['id'];
    }

    return $index;
}

function tokenize_with_lpmn_tagger($text, $tagsetName, $taggerType, $language)
{
    $client = (new LpmnClientBuilder())->build();
    $properties = (new PosTaggerPropertiesBuilder())
        ->methodTagger()
        ->language($language)
        ->taggerType($taggerType)
        ->outputFormat('json');
    if (!in_array($taggerType, array('archeopteryx', 'llm-pos-tagger'), true)) {
        $properties->tagset($tagsetName);
    }

    $pipeline = (new PipelineBuilder())
        ->any2Txt()
        ->postagger($properties->build())
        ->build();

    $taskOptions = (new TaskOptions())->withApplication('postagger');
    $client->runTask(InputType::TEXT, $text, $pipeline, $taskOptions);

    return $client->downloadResults();
}

function get_tokenization_name($taggerType, $language, $tagsetName)
{
    return "lpmn:$taggerType:$language:$tagsetName";
}

function insert_missing_bases($db, $new_bases)
{
    if (count($new_bases) === 0) {
        return;
    }

    $values = array();
    foreach (array_keys($new_bases) as $base) {
        $values[] = $db->escape($base);
    }

    $db->execute('INSERT IGNORE INTO `bases` (`text`) VALUES ("' . implode('"),("', $values) . '");');
}

function insert_missing_ctags($db, $new_ctags, $tagset_id)
{
    if (count($new_ctags) === 0) {
        return;
    }

    $values = array();
    foreach (array_keys($new_ctags) as $ctag) {
        $values[] = $db->escape($ctag);
    }

    $db->execute('INSERT IGNORE INTO `tokens_tags_ctags` (`ctag`, `tagset_id`) VALUES ("' .
        implode('",' . $tagset_id . '),("', $values) . '",' . $tagset_id . ');');
}

function insert_tokens($db, $report_id, $tokens)
{
    if (count($tokens) === 0) {
        throw new Exception("No tokens to insert.");
    }

    $values = array();
    foreach ($tokens as $token) {
        $values[] = "({$token[0]}, {$token[1]}, {$token[2]}, {$token[3]})";
    }

    $db->execute("INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`) VALUES" . implode(",", $values));

    $token_ids = array();
    foreach ($db->fetch_rows("SELECT token_id FROM tokens WHERE report_id = ? ORDER BY token_id ASC", array($report_id)) as $token) {
        $token_ids[] = $token['token_id'];
    }

    return $token_ids;
}

function insert_token_tags($db, $token_ids, $tokens_tags)
{
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

    $db->execute("INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag_id`,`disamb`,`pos`) VALUES " . implode(",", $values));
}

function update_tokenization_status($db, $corpus_id, $report_id, $tokenization)
{
    $db->execute("UPDATE reports SET tokenization = ? WHERE id = ?", array($tokenization, $report_id));

    $sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND LOWER(short) = 'tokens'";
    $corpora_flag_id = $db->fetch_one($sql, array($corpus_id));
    if ($corpora_flag_id) {
        $db->execute(
            "REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)",
            array($corpora_flag_id, $report_id)
        );
    }
}

function collect_tokens_from_json(
    $text_tagged,
    $report_id,
    $db,
    $tagset_id,
    $index_bases,
    $index_ctags,
    $taggerType,
    $debugTokens
) {
    $data = json_decode($text_tagged, true);
    if (!is_array($data)) {
        throw new Exception("Failed to decode JSON tokenization result.");
    }

    if (!isset($data['tokens']) || !is_array($data['tokens'])) {
        throw new Exception("JSON tokenization result does not contain tokens.");
    }

    $tokenLayers = $data['tokens'];
    $jsonTokens = isset($tokenLayers['default']) ? $tokenLayers['default'] : reset($tokenLayers);
    if (!is_array($jsonTokens) || count($jsonTokens) === 0) {
        throw new Exception("JSON tokenization result contains no tokens.");
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
    $offsetSourceText = $taggerType === 'spacy' ? remove_new_lines($sourceText) : $sourceText;
    $lastTokenIndex = count($jsonTokens) - 1;
    $new_bases = array();
    $new_ctags = array();
    $tokens = array();
    $tokens_tags = array();
    $debugTokenLimit = 80;
    $debugTokenCount = 0;
    $pendingLineBreakToken = null;

    foreach ($jsonTokens as $index => $jsonToken) {
        if (!isset($jsonToken['start']) || !isset($jsonToken['stop'])) {
            throw new Exception("JSON token does not contain start/stop offsets.");
        }

        $from = get_token_source_start($jsonToken, $taggerType);
        $stop = (int) $jsonToken['stop'];
        if ($stop < $from) {
            throw new Exception("JSON token has invalid offsets: start=$from stop=$stop.");
        }

        $sourceStop = get_token_source_stop($jsonToken, $offsetSourceText, $from, $taggerType);
        $orth = mb_substr($offsetSourceText, $from, $sourceStop - $from, 'utf-8');
        $orth = remove_whitespace($orth);
        if ($orth === '') {
            continue;
        }

        if ($pendingLineBreakToken !== null && should_merge_line_break_token($pendingLineBreakToken, $from, $orth, $offsetSourceText)) {
            $pendingLineBreakToken['source_stop'] = $sourceStop;
            $pendingLineBreakToken['stop'] = $stop;
            $pendingLineBreakToken['orth'] .= $orth;
            $pendingLineBreakToken['merged'] = true;
            continue;
        }

        if ($pendingLineBreakToken !== null) {
            append_json_token(
                $pendingLineBreakToken,
                $report_id,
                $offsetSourceText,
                $sentenceStops,
                $pendingLineBreakToken['index'],
                $lastTokenIndex,
                $tokens,
                $tokens_tags,
                $new_bases,
                $new_ctags,
                $index_bases,
                $index_ctags,
                $tagset_id,
                $db,
                $debugTokens,
                $debugTokenCount,
                $debugTokenLimit
            );
        }

        $pendingLineBreakToken = array(
            'index' => $index,
            'json_token' => $jsonToken,
            'source_start' => $from,
            'source_stop' => $sourceStop,
            'stop' => $stop,
            'orth' => $orth,
            'merged' => false,
        );
    }

    if ($pendingLineBreakToken !== null) {
        append_json_token(
            $pendingLineBreakToken,
            $report_id,
            $offsetSourceText,
            $sentenceStops,
            $pendingLineBreakToken['index'],
            $lastTokenIndex,
            $tokens,
            $tokens_tags,
            $new_bases,
            $new_ctags,
            $index_bases,
            $index_ctags,
            $tagset_id,
            $db,
            $debugTokens,
            $debugTokenCount,
            $debugTokenLimit
        );
    }

    return array(
        'tokens' => $tokens,
        'tokens_tags' => $tokens_tags,
        'new_bases' => $new_bases,
        'new_ctags' => $new_ctags,
    );
}

function append_json_token(
    $parsedToken,
    $report_id,
    $sourceText,
    $sentenceStops,
    $index,
    $lastTokenIndex,
    &$tokens,
    &$tokens_tags,
    &$new_bases,
    &$new_ctags,
    $index_bases,
    $index_ctags,
    $tagset_id,
    $db,
    $debugTokens,
    &$debugTokenCount,
    $debugTokenLimit
) {
        $jsonToken = $parsedToken['json_token'];
        $from = $parsedToken['source_start'];
        $sourceStop = $parsedToken['source_stop'];
        $stop = $parsedToken['stop'];
        $orth = $parsedToken['orth'];

        $from = get_offset_without_whitespace($sourceText, $from);
        $to = get_offset_without_whitespace($sourceText, $sourceStop) - 1;
        $lastToken = isset($sentenceStops[$stop]) || isset($sentenceStops[$sourceStop]) || ($index === $lastTokenIndex && count($sentenceStops) === 0) ? 1 : 0;
        $tokens[] = array($report_id, $from, $to, $lastToken);

        if ($debugTokens && $debugTokenCount < $debugTokenLimit) {
            print_debug_token($index, $jsonToken, $sourceStop, $orth, $from, $to);
            $debugTokenCount++;
        }

        $lexemes = isset($jsonToken['lexemes']) && is_array($jsonToken['lexemes']) ? $jsonToken['lexemes'] : array();
        if (count($lexemes) === 0) {
            $lexemes[] = array('lemma' => $orth, 'pos' => 'ign', 'disamb' => true);
        }
        if (!empty($parsedToken['merged'])) {
            $lexemes = array(array('lemma' => $orth, 'pos' => 'ign', 'disamb' => true));
        }

        $lexemes = filter_ign_lexemes($lexemes);
        $tags_args = array();

        foreach ($lexemes as $lexeme) {
            $base = isset($lexeme['lemma']) ? strval($lexeme['lemma']) : '';
            $ctag = isset($lexeme['pos']) ? strval($lexeme['pos']) : '';
            if ($base === '' || $ctag === '') {
                continue;
            }

            $baseEscaped = $db->escape($base);
            $ctagEscaped = $db->escape($ctag);
            $cts = explode(":", $ctag);
            $pos = $db->escape($cts[0]);
            $disamb = !empty($lexeme['disamb']) ? "true" : "false";

            if (isset($index_bases[$base])) {
                $base_sql = $index_bases[$base];
            } else {
                if (!isset($new_bases[$base])) {
                    $new_bases[$base] = 1;
                }
                $base_sql = '(SELECT id FROM bases WHERE text="' . $baseEscaped . '")';
            }

            if (isset($index_ctags[$ctag])) {
                $ctag_sql = $index_ctags[$ctag];
            } else {
                if (!isset($new_ctags[$ctag])) {
                    $new_ctags[$ctag] = 1;
                }
                $ctag_sql = '(SELECT id FROM tokens_tags_ctags ' .
                    'WHERE ctag="' . $ctagEscaped . '"' .
                    ' AND tagset_id = ' . $tagset_id . ')';
            }

            $tags_args[] = array($base_sql, $ctag_sql, $disamb, $pos);
        }

        if (count($tags_args) === 0) {
            throw new Exception("JSON token has no valid lexemes.");
        }

        $tokens_tags[] = $tags_args;
}

function get_token_source_start($jsonToken, $taggerType)
{
    $start = (int) $jsonToken['start'];
    if ($taggerType === 'spacy') {
        return max(0, $start - 1);
    }

    return $start;
}

function get_token_source_stop($jsonToken, $sourceText, $sourceStart, $taggerType)
{
    $stop = (int) $jsonToken['stop'];

    if (is_hyphen_at_offset($sourceText, $stop)) {
        return $stop + 1;
    }

    return $stop === $sourceStart ? $stop + 1 : $stop;
}

function should_merge_line_break_token($previousToken, $sourceStart, $orth, $sourceText)
{
    $gap = mb_substr(
        $sourceText,
        $previousToken['source_stop'],
        $sourceStart - $previousToken['source_stop'],
        'utf-8'
    );

    if (!is_line_break_gap($gap) && !is_likely_split_single_letter($previousToken, $sourceStart, $orth, $sourceText)) {
        return false;
    }

    if (!empty($previousToken['merged'])) {
        return starts_with_lowercase_letter($orth);
    }

    return mb_strlen($previousToken['orth'], 'utf-8') === 1
        || ends_with_hyphen($previousToken['orth']);
}

function starts_with_lowercase_letter($text)
{
    return preg_match('/^\p{Ll}/u', $text) === 1;
}

function is_line_break_gap($gap)
{
    return strpos($gap, "\n") !== false
        || strpos($gap, "\r") !== false
        || strpos($gap, "\f") !== false
        || strpos($gap, "\v") !== false;
}

function is_likely_split_single_letter($previousToken, $sourceStart, $orth, $sourceText)
{
    if (mb_strlen($previousToken['orth'], 'utf-8') !== 1 || !starts_with_lowercase_letter($orth)) {
        return false;
    }

    if (is_common_one_letter_word($previousToken['orth'])) {
        return false;
    }

    if (!starts_with_uppercase_letter($previousToken['orth']) && !is_line_initial_token($sourceText, $previousToken['source_start'])) {
        return false;
    }

    if ($previousToken['source_stop'] === $sourceStart) {
        return true;
    }

    $gap = mb_substr(
        $sourceText,
        $previousToken['source_stop'],
        1,
        'utf-8'
    );

    return preg_match('/^\s$/u', $gap) === 1;
}

function is_common_one_letter_word($text)
{
    return in_array(mb_strtolower($text, 'utf-8'), array('a', 'i', 'o', 'u', 'w', 'z'), true);
}

function starts_with_uppercase_letter($text)
{
    return preg_match('/^\p{Lu}/u', $text) === 1;
}

function is_line_initial_token($sourceText, $sourceStart)
{
    $prefix = mb_substr($sourceText, 0, $sourceStart, 'utf-8');

    return preg_match('/(^|[\r\n\f\v])\s*$/u', $prefix) === 1;
}

function ends_with_hyphen($text)
{
    return preg_match('/[-‐‑‒–—]$/u', $text) === 1;
}

function is_hyphen_at_offset($text, $offset)
{
    return ends_with_hyphen(mb_substr($text, $offset, 1, 'utf-8'));
}

function print_debug_token($index, $jsonToken, $sourceStop, $orth, $dbFrom, $dbTo)
{
    $sourceStart = isset($jsonToken['start']) ? (int) $jsonToken['start'] : -1;
    $sourceStopRaw = isset($jsonToken['stop']) ? (int) $jsonToken['stop'] : -1;
    $id = isset($jsonToken['id']) ? $jsonToken['id'] : '?';
    $orthPrintable = str_replace(array("\r", "\n", "\t"), array("\\r", "\\n", "\\t"), $orth);

    echo sprintf(
        "DEBUG token #%d id=%s src=%d..%d normalizedSrcStop=%d db=%d..%d orth=[%s]\n",
        $index,
        $id,
        $sourceStart,
        $sourceStopRaw,
        $sourceStop,
        $dbFrom,
        $dbTo,
        $orthPrintable
    );
}

function get_offset_without_whitespace($text, $offset)
{
    return mb_strlen(remove_whitespace(mb_substr($text, 0, $offset, 'utf-8')), 'utf-8');
}

function remove_whitespace($text)
{
    return preg_replace('/\s+/u', '', $text);
}

function remove_new_lines($text)
{
    return str_replace(array("\r\n", "\n", "\r", "\f", "\v"), '', $text);
}

function filter_ign_lexemes($lexemes)
{
    $ign = null;
    $tags_ign_disamb = array();

    foreach ($lexemes as $tag) {
        $ctag = is_array($tag) ? (isset($tag['pos']) ? $tag['pos'] : null) : $tag->ctag;
        $disamb = is_array($tag) ? !empty($tag['disamb']) : $tag->disamb;

        if ($ctag == "ign") {
            $ign = $tag;
        }
        if ($ctag == "ign" || $disamb) {
            $tags_ign_disamb[] = $tag;
        }
    }

    if ($ign) {
        return $tags_ign_disamb;
    }

    return $lexemes;
}

main(Config::Cfg());
