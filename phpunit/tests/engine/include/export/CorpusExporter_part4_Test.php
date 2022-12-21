<?php

mb_internal_encoding("UTF-8");
require_once("CorpusExporterTest.php");

class CorpusExporter_part4_Test extends CorpusExporterTest
//PHPUnit_Framework_TestCase
{
// attributes export testing

// function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
    public function test_export_document()
    {
        // dokument do eksportu - z parametru
        $report_id = 1;
        // export parameters
        //   F=3:attributes_annotation_set_id=1
        // flaga o skrócie 'F' w stanie 3; typ atrybutów = 1
        $flag_name = 'f';
        $flag_state = 3;
        $annotation_layer = 1;
        $extractorDescription = "F=3:attributes_annotation_set_id=".$annotation_layer;
        //$extractors = array();
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $subcorpora = array();
        //String tagging method from ['tagger', 'final', 'final_or_tagger', 'user:{id}']
        $tagging_method = 'tagger';

        $this->makeWorkDir(__FUNCTION__,$report_id);
        $output_folder = $this->createWorkDirName(__FUNCTION__);
        $output_file_basename = $this->createBaseFilename(__FUNCTION__,$report_id);

        // DB answers injected
        $dbEmu = new DatabaseEmulator();
        //  zbiór flag dostępnych dla dokumentu $report_id i odpowiadajacego
        // mu korpusu
        //    nazwa i flaga z ekstraktora musi się zgadzać z istniejącymi
        //    w bazie dla tego dokumentu
        $ReturnedDataRow = array( "id"=>1, "short"=>$flag_name, "flag_id"=>$flag_state );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
'SELECT cf.short, rf.flag_id FROM reports_flags rf  JOIN corpora_flags cf USING (corpora_flag_id) WHERE rf.report_id = ?',
            $allReturnedDataRows );

        // funkcja wykonująca ekstraktor dla attributes_annotation_set_id
        // DbReportAnnotationLemma::getAttributes(array($report_id), $params);
        //   zwraca dane wszystkich atrybutów dla dokumentu $report_id
        //   i z group zgodne z $annotation_layer
        $type = 4;
        $from = 0; $to = 4;
        $name = 'nazwa własności';
        $value = 'wartość własności';
        $ReturnedDataRow = array( "id"=>1, "type"=>$type, "report_id"=>$report_id, "name"=>$name, "value"=>$value, "from"=>$from, "to"=>$to ); 
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",       
"SELECT ra.id, ra.type, ra.report_id, sa.name, rasa.value, ra.from, ra.to  FROM reports_annotations_shared_attributes rasa  JOIN shared_attributes sa  ON (rasa.shared_attribute_id=sa.id)  JOIN reports_annotations ra  ON (rasa.annotation_id = ra.id)  LEFT JOIN annotation_types at ON (ra.type=at.name)  WHERE ( stage='final'  AND report_id IN (".$report_id."))  AND ( at.group_id IN (".$annotation_layer.") )   ORDER BY `from`",                  $allReturnedDataRows );
 
        //   zwraca dane wszystkich tokenów w bazie dla dokumentu $report_id 
        $token_id = 1;
        $ReturnedDataRow = array( "token_id" => $token_id, "report_id" => $report_id, "from" => $from, "to" => $to, "eos" => 1, "orth_id" => 1 );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
' SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`',
            $allReturnedDataRows );

        // zwraca szczegółowe dane o wyżej wybranym tokenie $token_id
        $ReturnedDataRow = array( 'token_tag_id' => 1, 'token_id' => $token_id, 'disamb' => 0, 'tto.ctag_id' => 1, 'ctag_id' => 1, 'ctag'=>'tag', 'tagset_id' => 1, 'base_id' => 1, 'base_text' => 'text' );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
'SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE tto.user_id IS NULL  AND token_id IN (1);',
            $allReturnedDataRows );

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
            "$flag_name=$flag_state:attributes_annotation_set_id=1" => array(
                "annotations"=>0,
                "relations"=>0,
                "lemmas"=>0,
                "attributes"=>1
            )
        );
        $this->assertEquals($expectedStats,$extractor_stats);
        $expectedConllContent = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n0\t0\t$token_content\t\t$from\t$to\tO\t_\t_\t_\n\n";
        $resultConllFile = file_get_contents($output_file_basename.'.conll');
        $this->assertEquals($expectedConllContent,$resultConllFile);
        $expectedIniContent = "[document]\nid = $report_id\ndate = $date\ntitle = $title\nsource = $source\nauthor = $author\ntokenization = $tokenization\nsubcorpus = ";
        $resultIniFile = file_get_contents($output_file_basename.'.ini');
        $this->assertEquals($expectedIniContent,$resultIniFile);
        $expectedJsonContent = "{\n    \"chunks\": [\n        [\n            [\n                {\n                    \"order_id\": 0,\n                    \"token_id\": 0,\n                    \"orth\": \"$token_content\",\n                    \"ctag\": null,\n                    \"from\": $from,\n                    \"to\": $to,\n                    \"annotations\": [],\n                    \"relations\": []\n                }\n            ]\n        ]\n    ],\n    \"relations\": [],\n    \"annotations\": []\n}";
        $resultJsonFile = file_get_contents($output_file_basename.'.json');
        $this->assertEquals($expectedJsonContent,$resultJsonFile);
        $expectedTxtContent = $content;
        $resultTxtFile = file_get_contents($output_file_basename.'.txt');
        $this->assertEquals($expectedTxtContent,$resultTxtFile);
        $expectedRelxmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<relations>\n</relations>\n";
        $resultRelxmlFile = file_get_contents($output_file_basename.'.rel.xml');
        $this->assertEquals($expectedRelxmlContent,$resultRelxmlFile);
        $expectedXmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<chunkList>\n <chunk id=\"ch1\" type=\"\">\n  <sentence id=\"sent1\">\n   <tok>\n    <orth>$token_content</orth>\n    <prop key=\"$type:$name\">$value</prop>\n   </tok>\n  </sentence>\n </chunk>\n</chunkList>\n";
        $resultXmlFile = file_get_contents($output_file_basename.'.xml');
        $this->assertEquals($expectedXmlContent,$resultXmlFile);
        
        // remove all files and directories created
        $this->removeWorkDir(__FUNCTION__,$report_id);

    }

} // class

?>
