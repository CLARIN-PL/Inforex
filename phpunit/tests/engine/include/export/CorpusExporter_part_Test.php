<?php

mb_internal_encoding("UTF-8");
require_once("CorpusExporterTest.php");

class CorpusExporter_part_Test extends CorpusExporterTest 
{
// function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
    public function test_export_document()
    {

        $report_id = 1;
        $extractors = array();
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $subcorpora = array();
        //String tagging method from ['tagger', 'final', 'final_or_tagger', 'user:{id}']
        $tagging_method = '';

        
        $dbEmu = new DatabaseEmulator();
        // set results emulation of querries external for class
        $dbEmu->setResponse("fetch_rows",
'SELECT cf.short, rf.flag_id FROM reports_flags rf  JOIN corpora_flags cf USING (corpora_flag_id) WHERE rf.report_id = ?',
            array(
                array( "id" => 1, "short" => 'jeden', "flag_id" => 1 )
            )
        );
        $dbEmu->setResponse("fetch_rows"," SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`",
            array(
                array( "token_id" => 1, "report_id" => 1, "from" => 1, "to" => 1, "eos" => 1, "orth_id" => 1 ) // only "token_id" is used
            )
        );
        $dbEmu->setResponse("fetch_rows",'SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE tto.user_id IS NULL  AND token_id IN (1);',
            array(
                array( 'token_tag_id' => 1, 'token_id' => 1, 'disamb' => 0, 'tto.ctag_id' => 1, 'ctag_id' => 1, 'ctag'=>'tag', 'tagset_id' => 1, 'base_id' => 1, 'base_text' => 'text' )
            )
        );
        $dbEmu->setResponse("fetch_rows","SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE (tto.user_id = ) AND (tto.stage = 'agreement') AND token_id IN (1);",
            array(
                array('token_tag_id' => 1, 'token_id' => 1, 'disamb' => 0, 'tto.ctag_id' => 1, 'ctag_id' => 1, 'ctag'=>'tag', 'base_id' => 1, 'base_text' => 'text', 'user_id' => 1 )
            )
        );


        global $db;
        $db = $dbEmu;

        $report_id = 0;
        $this->makeWorkDir(__FUNCTION__,$report_id);
        $output_folder = $this->createWorkDirName(__FUNCTION__);
        $output_file_basename = $this->createBaseFilename(__FUNCTION__,$report_id);

        $ce = new CorpusExporter();
        // $extractors is var parameter, but shouldn't change
        $expectedExtractors = $extractors;
        $ce->export_document($report_id,$extractors,$disamb_only,$extractor_stats,$lists,$output_folder,$subcorpora,$tagging_method);
        // check results in variables and files
        $this->assertEquals($expectedExtractors,$extractors);
        $expectedLists = array();
        $this->assertEquals($expectedLists,$lists);
        $expectedStats = array();
        $this->assertEquals($expectedStats,$extractor_stats);
        $expectedConllContent = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n\n";
        $resultConllFile = file_get_contents($output_file_basename.'.conll');
        $this->assertEquals($expectedConllContent,$resultConllFile);
        $expectedIniContent = "[document]\nid = \ndate = \ntitle = \nsource = \nauthor = \ntokenization = \nsubcorpus = ";
        $resultIniFile = file_get_contents($output_file_basename.'.ini');
        $this->assertEquals($expectedIniContent,$resultIniFile);
        $expectedJsonContent = "{\n    \"chunks\": [\n        [\n            []\n        ]\n    ],\n    \"relations\": [],\n    \"annotations\": []\n}";
        $resultJsonFile = file_get_contents($output_file_basename.'.json');
        $this->assertEquals($expectedJsonContent,$resultJsonFile);
        $expectedTxtContent = "";
        $resultTxtFile = file_get_contents($output_file_basename.'.txt');
        $this->assertEquals($expectedTxtContent,$resultTxtFile);
        $expectedRelxmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<relations>\n</relations>\n";
        $resultRelxmlFile = file_get_contents($output_file_basename.'.rel.xml');
        $this->assertEquals($expectedRelxmlContent,$resultRelxmlFile);
        $expectedXmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<chunkList>\n <chunk id=\"ch1\" type=\"\">\n  <sentence id=\"sent1\">\n  </sentence>\n </chunk>\n</chunkList>\n";
        $resultXmlFile = file_get_contents($output_file_basename.'.xml');
        $this->assertEquals($expectedXmlContent,$resultXmlFile);

        // remove all files and directories created
        $this->removeWorkDir(__FUNCTION__,$report_id);

    } 

} // class

?>
