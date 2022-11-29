<?php

mb_internal_encoding("UTF-8");

class CorpusExporter_part_Test extends PHPUnit_Framework_TestCase
{
// function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
    public function test_export_document()
    {

        $dbEmu = new DatabaseEmulator();
        // set results emulation of querries external for class
        $dbEmu->setRequest("fetch_rows","SELECT r.id, cf.short, rf.flag_id FROM reports_flags rf  JOIN reports r ON r.id = rf.report_id JOIN corpora_flags cf USING (corpora_flag_id) WHERE r.id = ?",
            array(
                array( "id" => 1, "short" => 'jeden', "flad_id" => 1 )
            )
        );
        $dbEmu->setRequest("fetch_rows"," SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`",
            array(
                array( "token_id" => 1 )
            )
        );
        $dbEmu->setRequest("fetch_rows",'SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE tto.user_id IS NULL  AND token_id IN (1);',
            array(
                array( 'token_tag_id' => 1, 'token_id' => 1, 'disamb' => 0, 'tto.ctag_id' => 1, 'ctag_id' => 1, 'ctag'=>'tag', 'tagset_id' => 1, 'base_id' => 1, 'base_text' => 'text' )
            )
        );
        $dbEmu->setRequest("fetch_rows","SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE (tto.user_id = ) AND (tto.stage = 'agreement') AND token_id IN (1);",
            array(
                array('token_tag_id' => 1, 'token_id' => 1, 'disamb' => 0, 'tto.ctag_id' => 1, 'ctag_id' => 1, 'ctag'=>'tag', 'base_id' => 1, 'base_text' => 'text', 'user_id' => 1 )
            )
        );


        global $db;
        $db = $dbEmu;

        $report_id = 1;
        $extractors = array();
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $output_folder = '/tmp/';
        $subcorpora = array();
        //String tagging method from ['tagger', 'final', 'final_or_tagger', 'user:{id}']
        $tagging_method = '';

        $ce = new CorpusExporter();
        $ce->export_document($report_id,$extractors,$disamb_only,$extractor_stats,$lists,$output_folder,$subcorpora,$tagging_method);

        $expectedResults = array(
        );
        $this->assertEquals($expectedResults,$extractor_stats);

    } 

} // class

?>
