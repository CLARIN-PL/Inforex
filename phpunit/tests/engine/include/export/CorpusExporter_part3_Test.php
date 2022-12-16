<?php

mb_internal_encoding("UTF-8");

class CorpusExporter_part3_Test extends PHPUnit_Framework_TestCase
{
// attributes export testing

// function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
    public function test_export_document()
    {

        // export parameters
        //   F=3:attributes_annotation_set_id=1
        // flaga o skrócie 'F' w stanie 3; typ atrybutów = 1
        $report_id = 1;
        $flag_name = 'f';
        $flag_state = 3;
        $extractorDescription = "F=3:attributes_annotation_set_id=1";
        //$extractors = array();
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $subcorpora = array();
        //String tagging method from ['tagger', 'final', 'final_or_tagger', 'user:{id}']
        $tagging_method = 'tagger';

        $output_folder = '/tmp/'.get_class($this).'_'.__FUNCTION__.'/';
        mkdir($output_folder);
        $output_file_basename = $output_folder.'00000000';
        
        // DB answers injected
        $dbEmu = new DatabaseEmulator();
        //  zbiór flag dostępnych dla korpusu i dokumentu
        $ReturnedDataRow = array( "id"=>1, "short"=>$flag_name, "flag_id"=>$flag_state );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
'SELECT cf.short, rf.flag_id FROM reports_flags rf  JOIN corpora_flags cf USING (corpora_flag_id) WHERE rf.report_id = ?',
            $allReturnedDataRows );

        // funkcja wykonująca ekstraktor dla attributes_annotation_set_id
        // DbReportAnnotationLemma::getAttributes(array($report_id), $params);
        $ReturnedDataRow = array( "id"=>1, "type"=>1, "report_id"=>$report_id, "name"=>'shared_attributes - name', "value"=>'wartość atrybutu', "from"=>1, "to"=>3 ); 
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",       
"SELECT ra.id, ra.type, ra.report_id, sa.name, rasa.value, ra.from, ra.to  FROM reports_annotations_shared_attributes rasa  JOIN shared_attributes sa  ON (rasa.shared_attribute_id=sa.id)  JOIN reports_annotations ra  ON (rasa.annotation_id = ra.id)  LEFT JOIN annotation_types at ON (ra.type=at.name)  WHERE ( stage='final'  AND report_id IN (1))  AND ( at.group_id IN (1) )   ORDER BY `from`",                  $allReturnedDataRows );
 
 
        $ReturnedDataRow = array( "token_id" => 1, "report_id" => 1, "from" => 1, "to" => 1, "eos" => 1, "orth_id" => 1 );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
' SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`',
            $allReturnedDataRows );

        $ReturnedDataRow = array( 'token_tag_id' => 1, 'token_id' => 1, 'disamb' => 0, 'tto.ctag_id' => 1, 'ctag_id' => 1, 'ctag'=>'tag', 'tagset_id' => 1, 'base_id' => 1, 'base_text' => 'text' );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
'SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE tto.user_id IS NULL  AND token_id IN (1);',
            $allReturnedDataRows );

        // do test...
        global $db;
        $db = $dbEmu;
        $ce = new CorpusExporter();
        $extractors = $ce->parse_extractor($extractorDescription);
        // $extractors is var parameter, but shouldn't change
        $expectedExtractors = $extractors;
        $ce->export_document($report_id,$extractors,$disamb_only,$extractor_stats,$lists,$output_folder,$subcorpora,$tagging_method);
        // check results in variables and files
        $this->assertEquals($expectedExtractors,$extractors);
        $expectedLists = array();
        $this->assertEquals($expectedLists,$lists);
        $expectedStats = array( 
            "f=3:attributes_annotation_set_id=1" => array(
                "annotations"=>0,
                "relations"=>0,
                "lemmas"=>0,
                "attributes"=>1
            )
        );
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
        foreach(array('conll','ini','json','txt','rel.xml','xml') as $ext) {
            unlink($output_folder.'00000000.'.$ext);
        }
        rmdir($output_folder);

    }

} // class

?>
