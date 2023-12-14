<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class CorpusExporter_part1_Test extends PHPUnit_Framework_TestCase
{
    private $virtualDir = null;

    protected function setUp() {

        $this->virtualDir = vfsStream::setup('root',null,[]);

    } // setUp()

    public function test_export_document_withDisambNotReturnsTags() {

        foreach(['tagger', 'final', 'final_or_tagger', 'user:1'] as $tagging_method) {
            $this->export_document(True,$tagging_method);
        }

    } // test_export_document_withDisambNotReturnsTags()

    public function test_export_document_withoutDisambReturnsTags() {

        foreach(['tagger', 'final', 'final_or_tagger', 'user:1'] as $tagging_method) {
            $this->export_document(False,$tagging_method);
        }

    } // test_export_document_withoutDisambReturnsTags()

// function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
    private function export_document($disamb_only,$tagging_method)
    {
        // dokument do eksportu - z parametru
        $report_id = 1;
        //   F=3:annotation_set_id=1,2
        // flaga o skrócie 'F' w stanie 3; typ annotacji = 1 lub 2
        // przeparsowanie ręczne do parametrów ekstraktora:
        $extractorParameters = array(
            "FlagName" => 'f',   // parse_extractor tu robi zawsze małą literę
            "FlagValues" => array(3),
            "Name" => 'annotation_set_id',
            "Parameters" => array(1,2)    // -- set of annotation_set_id
        );
        $lists = array();
        $subcorpora = array();

        // wykreowanie elementów ekstraktora
        $extractorObj = new MockedExtractor($extractorParameters["FlagName"],$extractorParameters["FlagValues"],$extractorParameters["Name"],$extractorParameters["Parameters"]);
        // $annotations = DbAnnotation::getAnnotationsBySets(array($report_id), $params, null, 'final');
        $extractorData = array(
            array( "id"=>1, "report_id"=>$report_id, "type_id"=>1, "type"=>'typ annotacji 1', "from"=>0, "to"=>4, "text"=>'tekst', "user_id"=>1, "creation_time"=>'2022-11-11 11:11:11', "stage"=>'final', "source"=>'user', "prop"=>'atrybut annotacji 1' ),
            array( "id"=>100, "report_id"=>$report_id, "type_id"=>2, "type"=>'typ annotacji 2', "from"=>5, "to"=>13, "text"=>'dokumentu', "user_id"=>2, "creation_time"=>'2022-11-11 11:11:12', "stage"=>'final', "source"=>'user', "prop"=>'atrybut annotacji 2' )
        );
        $extractorObj->setExtractorReturnedData('annotations',$extractorData);

        // dane jakie powinny zawierać tabele bazy danych dla przeprowadzenia
        // testu
        $dbEmu = new DocumentExportDatabaseEmulator();
        $documentDBData = array(
            "date"          => '2022-12-16',
            "title"         => 'tytuł',
            "source"        => 'source',
            "author"        => 'author',
            "tokenization"  => 'tokenization',
            "content"       => 'tekst dokumentu',
            // kolumna ext z wiersza w corpora dla id = corpora dokumentu
            "corpora_ext"   => null,
            // flagi korpusu, odpowiadającego korpusowi dokumentu 
            "flags"         => array (
                "FlagName"      => $extractorParameters["FlagName"],
                "FlagValues"    => $extractorParameters["FlagValues"]
            ),
            // tablica tokenów dokumentu
            "tokens"        => array(
                array(  "from"=>0, "to"=>4, "eos"=>0,
                        "tags"=>array(
                            // tags rows for token
                            array( "disamb" => 0, "ctag" => 'ctag', "base_text" => 'text', 'stage'=>'final'),
                            array( "disamb" => 0, "ctag" => 'ctag2', "base_text" => 'base_text_taga_2', "stage"=>'final'),
                            array( "disamb" => 0, "ctag" => 'ctag2', "base_text" => 'base_text_taga_2', "stage"=>'agreement', "user_id"=>1 ),
                            array( "disamb" => 0, "ctag" => 'ctag3', "base_text" => 'base_text_taga_2', "stage"=>'agreement', "user_id"=>1 ),
                        ), // tags
                        // to poniżej tylko dla ustalania expected, nie jest
                        // wykorzystane przy generowaniu danych z bazy
                        "in_ann_ids"=>[1]
                ),
                array(  "from"=>5, "to"=>13, "in_ann_ids"=>[100], "eos"=>1 ) // no tags
 
            ) // tokens
        );
        $dbEmu->addReportsDB($report_id,$documentDBData);

        // do test...
        global $db;
        $db = $dbEmu;

        $ce = new CorpusExporter();

        $extractor_stats = array(); // this will change
        // $extractors is var parameter, but shouldn't change
        $extractors = $extractorObj->getExtractorsTable();
        $expectedExtractors = $extractors;

        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($ce,'export_document');
        $protectedMethod->invokeArgs($ce,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$this->virtualDir->url(),$subcorpora,$tagging_method));
            
        // check results in variables and files
        $this->assertEquals($expectedExtractors,$extractors);
        $expectedLists = array();
        $this->assertEquals($expectedLists,$lists);
        $expectedStats = array(
                $extractorObj->getStatisticsName() => array(
                    "annotations"=>count($extractorData),
                    "relations"=>0,
                    "lemmas"=>0,
                    "attributes"=>0
                )
        );
        $this->assertEquals($expectedStats,$extractor_stats);

        $this->checkFiles($report_id,$disamb_only,$tagging_method,$documentDBData,$extractorData); 

    }

    private function checkFiles($report_id,$disambOnly,$tagging_method,$reportData,$extractorData) {

        $expectedBaseFileName = $this->virtualDir->url().'/'.str_pad($report_id,8,'0',STR_PAD_LEFT);
        $scl=new SimpleCcl($reportData,$tagging_method,$disambOnly);
        $scl->addAnnotations($extractorData);

        //checkConllFile
        $expectedContent = $scl->toCONLL();
        $resultConllFile = file_get_contents($expectedBaseFileName.'.conll');
        $this->assertEquals($expectedContent,$resultConllFile);

        //checkIniFile
        $expectedIniContent = "[document]\nid = ".$report_id."\ndate = ".$reportData["date"]."\ntitle = ".$reportData["title"]."\nsource = ".$reportData["source"]."\nauthor = ".$reportData["author"]."\ntokenization = ".$reportData["tokenization"]."\nsubcorpus = ";
        $resultIniFile = file_get_contents($expectedBaseFileName.'.ini');
        $this->assertEquals($expectedIniContent,$resultIniFile);

        //checkJSONFile
        $expectedContent = $scl->toJSON($extractorData);
        $resultJsonFile = file_get_contents($expectedBaseFileName.'.json');
        $this->assertEquals($expectedContent,$resultJsonFile);

        //checkTxtFile
        $expectedTxtContent = $reportData["content"];
        $resultTxtFile = file_get_contents($expectedBaseFileName.'.txt');
        $this->assertEquals($expectedTxtContent,$resultTxtFile);

        //checkRelXMLFile
        $expectedRelxmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<relations>\n</relations>\n";
        $resultRelxmlFile = file_get_contents($expectedBaseFileName.'.rel.xml');
        $this->assertEquals($expectedRelxmlContent,$resultRelxmlFile);

        //checkXMLFile
        $expectedContent = $scl->toXML();
        $resultXmlFile = file_get_contents($expectedBaseFileName.'.xml');
        $this->assertEquals($expectedContent,$resultXmlFile);

    } // checkFiles()

} // class

?>
