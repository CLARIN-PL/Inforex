<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");
require_once("CorpusExporterTest.php");

class CorpusExporter_part_Test extends CorpusExporterTest {

    private $virtualDir = null;

    protected function setUp() {

        $this->virtualDir = vfsStream::setup('root',null,[]);

    } // setUp()

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
        $dbEmu->setResponse("fetch_rows",
" SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`",
            array(
                array( "token_id" => 1, "report_id" => 1, "from" => 1, "to" => 1, "eos" => 1, "orth_id" => 1 ) // only "token_id" is used
            )
        );
        $dbEmu->setResponse("fetch_rows",
'SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE tto.user_id IS NULL  AND token_id IN (1);',
            array(
                array( 'token_tag_id' => 1, 'token_id' => 1, 'disamb' => 0, 'tto.ctag_id' => 1, 'ctag_id' => 1, 'ctag'=>'tag', 'tagset_id' => 1, 'base_id' => 1, 'base_text' => 'text' )
            )
        );
        $dbEmu->setResponse("fetch_rows",
"SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE (tto.user_id = ) AND (tto.stage = 'agreement') AND token_id IN (1);",
            array(
                array('token_tag_id' => 1, 'token_id' => 1, 'disamb' => 0, 'tto.ctag_id' => 1, 'ctag_id' => 1, 'ctag'=>'tag', 'base_id' => 1, 'base_text' => 'text', 'user_id' => 1 )
            )
        );

        // rekord z innymi danymi o dokumencie o podanym $record_id w bazie
        $date = '2022-12-16';
        $title = 'tytuł';
        $source = 'source';
        $author = 'author';
        $tokenization = 'tokenization';
        $token_content = 'tekst';
        $content = $token_content.' dokumentu';
        $ReturnedDataRow = array(
            "id" => $report_id,
            "corpora" => 1,
            "date" => $date,
            "title" => $title,
            "source" => $source,
            "author" => $author,
            "content" => $content,
            "type" => 1,
            "status" => 1,
            "user_id" => 1,
            "subcorpus_id" => 1,
            "tokenization" => $tokenization,
            "format_id" => 1,
            "lang" => 'pol',
            "filename" => 'nazwa pliku',
            "parent_report_id" => null,
            "deleted" => 0
        );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
            'SELECT * FROM reports WHERE id = ?',
            $allReturnedDataRows );

        $emptyDataRows = array();
        $dbEmu->setResponse("fetch_rows",
            'SELECT * FROM corpora WHERE id = ?',
            $emptyDataRows );

        global $db;
        $db = $dbEmu;

        $ce = new CorpusExporter();
        // $extractors is var parameter, but shouldn't change
        $expectedExtractors = $extractors;
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($ce,'export_document');
        $protectedMethod->invokeArgs($ce,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$this->virtualDir->url(),$subcorpora,$tagging_method));
        // check results in variables and files
        $this->assertEquals($expectedExtractors,$extractors);
        $expectedLists = array();
        $this->assertEquals($expectedLists,$lists);
        $expectedStats = array();
        $this->assertEquals($expectedStats,$extractor_stats);
        $expectedBaseFileName = $this->virtualDir->url().'/'.str_pad($report_id,8,'0',STR_PAD_LEFT);
        $expectedConllContent = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n0\t0\te\t\t1\t1\tO\t_\t_\t_\n\n";
        $resultConllFile = file_get_contents($expectedBaseFileName.'.conll');
        $this->assertEquals($expectedConllContent,$resultConllFile);
        $expectedIniContent = "[document]\nid = 1\ndate = 2022-12-16\ntitle = tytuł\nsource = source\nauthor = author\ntokenization = tokenization\nsubcorpus = ";
        $resultIniFile = file_get_contents($expectedBaseFileName.'.ini');
        $this->assertEquals($expectedIniContent,$resultIniFile);
        $expectedJsonContent = "{\n    \"chunks\": [\n        [\n            [\n                {\n                    \"order_id\": 0,\n                    \"token_id\": 0,\n                    \"orth\": \"e\",\n                    \"ctag\": null,\n                    \"from\": 1,\n                    \"to\": 1,\n                    \"annotations\": [],\n                    \"relations\": []\n                }\n            ]\n        ]\n    ],\n    \"relations\": [],\n    \"annotations\": []\n}";
        $resultJsonFile = file_get_contents($expectedBaseFileName.'.json');
        $this->assertEquals($expectedJsonContent,$resultJsonFile);
        $expectedTxtContent = $content;
        $resultTxtFile = file_get_contents($expectedBaseFileName.'.txt');
        $this->assertEquals($expectedTxtContent,$resultTxtFile);
        $expectedRelxmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<relations>\n</relations>\n";
        $resultRelxmlFile = file_get_contents($expectedBaseFileName.'.rel.xml');
        $this->assertEquals($expectedRelxmlContent,$resultRelxmlFile);
        $expectedXmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<chunkList>\n <chunk id=\"ch1\" type=\"\">\n  <sentence id=\"sent1\">\n   <tok>\n    <orth>e</orth>\n   </tok>\n   <ns/>\n  </sentence>\n </chunk>\n</chunkList>\n";
        $resultXmlFile = file_get_contents($expectedBaseFileName.'.xml');
        $this->assertEquals($expectedXmlContent,$resultXmlFile);

    } 

} // class

?>
