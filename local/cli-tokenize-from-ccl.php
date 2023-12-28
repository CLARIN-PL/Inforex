<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath . DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath . DIRECTORY_SEPARATOR . 'include.php');
Config::Cfg()->put_path_engine($enginePath);
Config::Cfg()->put_localConfigFilename(realpath($enginePath . "/../config/") . DIRECTORY_SEPARATOR . "config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));
$opt->addParameter(new ClioptParameter("input", "i", "input", "Input directory path. File names in folder must be in format {report_id}.ccl"));

try {
    ini_set('memory_limit', '1024M');
    $opt->parseCli($argv);
    $path = $opt->getRequired("input");

    $dbHost = "db";
    $dbUser = "inforex";
    $dbPass = "password";
    $dbName = "inforex";
    $dbPort = "3306";

    if ($opt->exists("db-uri")) {
        $uri = $opt->getRequired("db-uri");
        if (preg_match("/(.+):(.+)@(.*):(.*)\/(.*)/", $uri, $m)) {
            $dbUser = $m[1];
            $dbPass = $m[2];
            $dbHost = $m[3];
            $dbPort = $m[4];
            $dbName = $m[5];
            Config::Cfg()->put_dsn(array(
                'phptype' => 'mysql',
                'username' => $dbUser,
                'password' => $dbPass,
                'hostspec' => $dbHost . ":" . $dbPort,
                'database' => $dbName
            ));
        } else {
            throw new Exception("DB URI is incorrect. Given '$uri', but expected 'user:pass@host:port/name'");
        }
    }
    Config::Cfg()->put_verbose($opt->exists("verbose"));
} catch (Exception $ex) {
    print "!! " . $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    die("\n");
}

try {
    $loader = new CclLoader(Config::Cfg()->get_dsn(), Config::Cfg()->get_verbose());
    $dir = new DirectoryIterator(dirname($path));

    foreach (new DirectoryIterator($path) as $fileInfo) {
        if($fileInfo->isDot()) continue;
        if($fileInfo->getExtension() != "ccl") continue;
        $report_id = str_replace(".ccl", "", $fileInfo->getFilename());
        if(is_numeric($report_id)){
            $loader->load($report_id, $fileInfo->getPathname());
        }
    }


} catch (Exception $ex) {
    print "Error: " . $ex->getMessage() . "\n";
    print_r($ex);
}
sleep(1);

/**
 * Handle single request from tasks_documents.
 */
class CclLoader
{

    function __construct($dsn, $verbose)
    {
        $this->db = new Database($dsn, false);
        $GLOBALS['db'] = $this->db;
        $this->verbose = $verbose;
    }

    /**
     * Print message if verbose mode is on.
     */
    function info($message)
    {
        if ($this->verbose) {
            echo $message . "\n";
        }
    }

    function load($report_id, $path_to_file)
    {
        $tagset_id = 1;
        $text_tagged = file_get_contents($path_to_file);
        $doc = $this->db->fetch("SELECT * FROM reports WHERE id=?", array($report_id));

        echo "Processing " . $report_id . "\n";

        DbToken::deleteReportTokens($report_id);

        $index_bases = DbBase::getBasesMap();
        $index_ctags = DbTag::getTagsetTagsMap($tagset_id);
        $index_orths = DbOrth::getOrthsMap();

        $takipiText = "";
        $new_bases = array();
        $new_ctags = array();
        $new_orths = array();
        $tokens = array();
        $tokens_tags = array();
        $tokenization = "nlprest2:nlprest2:wcrft2({\"guesser\":\"false\",\"allforms\":\"true\",\"morfeusz2\":\"false\"})";

        $ccl = WcclReader::createFromString($text_tagged);

        if (count($ccl->chunks) == 0) {
            throw new Exception("Failed to load the document.");
        }

        foreach ($ccl->chunks as $chunk) {
            foreach ($chunk->sentences as $sentence) {
                $lastId = count($sentence->tokens) - 1;
                foreach ($sentence->tokens as $index => $token) {
                    $orth = custom_html_entity_decode($token->orth);
                    $from = mb_strlen($takipiText);
                    $takipiText = $takipiText . $orth;
                    $to = mb_strlen($takipiText) - 1;
                    $lastToken = $index == $lastId ? 1 : 0;

                    if (isset($index_orths[$orth])) {
                        $orth_sql = $index_orths[$orth];
                    } else {
                        $new_orths[$orth] = 1;
                        $orth_sql = "(SELECT orth_id FROM orths WHERE orth='" . $orth . "')";
                    }

                    $args = array($report_id, $from, $to, $lastToken, $orth_sql);
                    $tokens[] = $args;

                    $tags = $token->lex;

                    /** W przypadku ignów zostaw tylko ign i disamb */
                    $ign = null;
                    $tags_ign_disamb = array();
                    foreach ($tags as $i_tag => $tag) {
                        if ($tag->ctag == "ign") {
                            $ign = $tag;
                        }
                        if ($tag->ctag == "ign" || $tag->disamb) {
                            $tags_ign_disamb[] = $tag;
                        }
                    }
                    /** Jeżeli jedną z interpretacji jest ign, to podmień na ign i disamb */
                    if ($ign) {
                        $tags = $tags_ign_disamb;
                    }

                    $tags_args = array();
                    foreach ($tags as $lex) {
                        $base = addslashes(strval($lex->base));
                        $ctag = addslashes(strval($lex->ctag));
                        $cts = explode(":", $ctag);
                        $pos = $cts[0];
                        $disamb = $lex->disamb ? "true" : "false";
                        if (isset($index_bases[$base])) {
                            $base_sql = $index_bases[$base];
                        } else {
                            if (!isset($new_bases[$base])) $new_bases[$base] = 1;
                            $base_sql = "(SELECT id FROM bases WHERE text='" . $base . "')";
                        }
                        if (isset($index_ctags[$ctag])) {
                            $ctag_sql = $index_ctags[$ctag];
                        } else {
                            if (!isset($new_ctags[$ctag])) $new_ctags[$ctag] = 1;
                            $ctag_sql = '(SELECT id FROM tokens_tags_ctags ' .
                                'WHERE ctag="' . $ctag . '"' .
                                ' AND tagset_id = ' . $tagset_id . ')';
                        }
                        $tags_args[] = array($base_sql, $ctag_sql, $disamb, $pos);
                    }
                    $tokens_tags[] = $tags_args;
                }
            }
        }

        /* Wstawienie tagów morflogicznych */
        if (count($new_bases) > 0) {
            $sql_new_bases = 'INSERT IGNORE INTO `bases` (`text`) VALUES ("';
            $sql_new_bases .= implode('"),("', array_keys($new_bases)) . '");';
            $this->db->execute($sql_new_bases);
            echo "New bases: " . count($new_bases) . "\n";
        }
        if (count($new_ctags) > 0) {
            $sql_new_ctags = 'INSERT IGNORE INTO `tokens_tags_ctags` (`ctag`, `tagset_id`) VALUES ("';
            $sql_new_ctags .= implode('",' . $tagset_id . '),("', array_keys($new_ctags)) . '",' . $tagset_id . ');';
            $this->db->execute($sql_new_ctags);
            echo "New ctags: " . count($new_ctags) . "\n";
        }
        if (count($new_orths) > 0) {
            $new_orths = array_keys($new_orths);
            for ($i = 0; $i < count($new_orths); $i++) {
                $new_orths[$i] = $new_orths[$i];
            }
            $sql_new_orths = 'INSERT IGNORE INTO `orths` (`orth`) VALUES ("' . implode('"),("', $new_orths) . '");';
            $this->db->execute($sql_new_orths);
            echo "New orths: " . count($new_orths) . "\n";
            echo $sql_new_orths . "\n";
        }

        $sql_tokens = "INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`, `orth_id`) VALUES";
        $sql_tokens_values = array();
        foreach ($tokens as $t) {
            $sql_tokens_values[] = "({$t[0]}, {$t[1]}, {$t[2]}, {$t[3]}, {$t[4]})";
        }
        $sql_tokens .= implode(",", $sql_tokens_values);
        $this->db->execute($sql_tokens);

        $tokens_id = array();
        foreach ($this->db->fetch_rows("SELECT token_id FROM tokens WHERE report_id = ? ORDER BY token_id ASC", array($report_id)) as $t) {
            $tokens_id[] = $t['token_id'];
        }
        echo "Tokens: " . count($tokens_id) . "\n";

        $sql_tokens_tags = "INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag_id`,`disamb`,`pos`) VALUES ";
        $sql_tokens_tags_values = array();
        for ($i = 0; $i < count($tokens_id); $i++) {
            $token_id = $tokens_id[$i];
            if (!isset($tokens_tags[$i]) || count($tokens_tags[$i]) == 0) {
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
        if ($corpora_flag_id) {
            $this->db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)", array($corpora_flag_id, $report_id));
        }
        $this->db->execute("COMMIT");
    }

}	
