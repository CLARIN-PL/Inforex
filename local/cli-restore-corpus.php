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
Config::Config()->put_localConfigFilename(realpath($enginePath. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR )."config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-32");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt("Generates a set of INSERT IGNORE statements to restore a corpus");
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "corpus ID"));
$opt->addParameter(new ClioptParameter("output", "o", "path", "write generated sql to the file"));

/******************** parse cli *********************************************/
try{
    /** Parse cli parameters */
	$opt->parseCli(isset($argv) ? $argv : null);
    $dsn = CliOptCommon::parseDbParameters($opt, Config::Config()->get_dsn());
	$corpusId = $opt->getRequired("corpus");
	$output = $opt->getOptional("output", null);

    /** Setup database  */
    $GLOBALS['db'] = new Database($dsn,false);

    /** Validate parameters  */
	CliOptCommon::validateCorpusId($corpusId);

    //$collector->gather("", "", array($corpusId));

    $collector = new DataCollector($db);
	$collector->gather("corpora", "SELECT c.* FROM corpora c WHERE c.id = ?", array($corpusId));
    $collector->gather("corpus_subcorpora", "SELECT s.* FROM corpus_subcorpora s WHERE s.corpus_id = ?", array($corpusId));
    $collector->gather("reports", "SELECT r.* FROM reports r JOIN corpora c on r.corpora = c.id WHERE c.id = ?", array($corpusId));

    $collector->gather("corpora_flags", "SELECT f.* FROM corpora_flags f WHERE f.corpora_id = ?", array($corpusId));
    $collector->gather("reports_flags", "SELECT f.* FROM reports_flags f JOIN reports r on f.report_id = r.id WHERE r.corpora = ?", array($corpusId));

    $collector->gather("tokens", "SELECT t.* FROM tokens t JOIN reports r on t.report_id = r.id WHERE r.corpora = ?", array($corpusId));
    $collector->gather("bases", "SELECT DISTINCT b.* FROM bases b JOIN tokens_tags_optimized o on b.id = o.base_id JOIN tokens t on o.token_id = t.token_id JOIN reports r on t.report_id = r.id WHERE r.corpora = ?", array($corpusId));
    $collector->gather("tokens_tags_ctags", "SELECT DISTINCT c.* FROM tokens_tags_ctags c JOIN tokens_tags_optimized o on o.ctag_id = c.id JOIN tokens t on o.token_id = t.token_id JOIN reports r on t.report_id = r.id WHERE r.corpora = ?;", array($corpusId));
    $collector->gather("tokens_tags_optimized", "SELECT o.* FROM tokens_tags_optimized o JOIN tokens t on o.token_id = t.token_id JOIN reports r on t.report_id = r.id WHERE r.corpora = ?", array($corpusId));

    $collector->gather("annotation_sets_corpora", "SELECT s.* FROM annotation_sets_corpora s WHERE s.corpus_id = ?", array($corpusId));
    $collector->gather("reports_annotations_optimized", "SELECT a.* FROM reports_annotations_optimized a JOIN reports r on a.report_id = r.id WHERE r.corpora = ?", array($corpusId));
    $collector->gather("corpus_and_report_perspectives", "SELECT p.* FROM corpus_and_report_perspectives p WHERE p.corpus_id = ?", array($corpusId));


    $transaction = $collector->getTransaction();

    if ( $output != null ) {
        file_put_contents($output, implode("\n", $collector->getTransaction()));
        echo sprintf("Number of generated sqls: %d\n", count($transaction));
        echo sprintf("Restore transaction was saved to %s\n", $output);
    } else {
        print_r($transaction);
    }

}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	print("\n");
	return;
}

class DataCollector{

    function __construct(&$db){
        $this->sqls = array();
        $this->db = $db;
    }

    function gather($table, $sql, $args){
        $rows = $this->db->fetch_rows($sql, $args);
        foreach ($rows as $row) {
            $fields = array();
            $values = array();
            foreach ($row as $field => $value) {
                $fields[] = $field;
                $values[] = $value===null?"NULL" : "'".$this->db->escape($value)."'";
            }
            $this->sqls[] = sprintf("INSERT IGNORE INTO %s (%s) VALUES(%s); ", $table, implode(", ", $fields), implode(", ", $values));
        }
    }

    function getTransaction(){
        $tr = array("START TRANSACTION;");
        $tr = array_merge($tr, $this->sqls);
        $tr[] = "COMMIT;";
        return $tr;
    }
}
