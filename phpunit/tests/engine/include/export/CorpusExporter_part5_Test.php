<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class CorpusExporter_part5_Test extends PHPUnit_Framework_TestCase
{
    use CorpusExportAssertions;

    private $virtualDir = null;

    protected function setUp(): void {

        $this->virtualDir = vfsStream::setup('root',null,[]);

    } // setUp()

    /** @dataProvider documentFormats */
    public function test_export_document_withDisambNotReturnsTags($format) {

        foreach(['tagger', 'final', 'final_or_tagger', 'user:1'] as $tagging_method) {
            $this->export_document(True,$tagging_method,$format);
        }

    } // test_export_document_withDisambNotReturnsTags()

    /** @dataProvider documentFormats */
    public function test_export_document_withoutDisambReturnsTags($format) {

        foreach(['tagger', 'final', 'final_or_tagger', 'user:1'] as $tagging_method) {
            $this->export_document(False,$tagging_method,$format);
        }

    } // test_export_document_withoutDisambReturnsTags()

// function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
    private function export_document($disamb_only,$tagging_method,$format)
    {
        // dokument do eksportu - z parametru
        $report_id = 1;
        //   F=3:annotation_subset_id=1
        // flaga o skrócie 'F' w stanie 3; podtyp annotacji = 1
        // przeparsowanie ręczne do parametrów ekstraktora:
        $extractorParameters = array(
            "FlagName" => 'f',   // parse_extractor tu robi zawsze małą literę
            "FlagValues" => array(3),
            "Name" => 'lemma_annotation_set_id',
            "Parameters" => array(1,2)  // annotation layers = sets
        );
        $lists = array();
        $subcorpora = array();

        // wykreowanie elementów ekstraktora
        $extractorObj = new MockedExtractor($extractorParameters["FlagName"],$extractorParameters["FlagValues"],$extractorParameters["Name"],$extractorParameters["Parameters"]);
        // $lemmas = DbReportAnnotationLemma::getLemmasBySets(array($report_id), $params);
        $extractorData = array(
            array( "reports_annotation_id"=>1, "lemma"=>'lemma annotacji 1', "id"=>12345, "group_id"=>1, "name"=>'NAME', "from"=>0, "to"=>4, "type"=>'TYPE' ),
            array( "reports_annotation_id"=>100, "lemma"=>'lemma annotacji 100', "id"=>12346, "group_id"=>1, "name"=>'NAME', "from"=>5, "to"=>13, "type"=>'TYPE' ),
        );
        $extractorObj->setExtractorReturnedData('lemmas',$extractorData);

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
        $output_folder = $this->virtualDir->url();
        $extractor_stats = array(); // this will change
        // $extractors is var parameter, but shouldn't change
        $extractors = $extractorObj->getExtractorsTable();
        $expectedExtractors = $extractors;

        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($ce,'export_document');
        $protectedMethod->invokeArgs($ce,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method,$format));
            
        // check results in variables and files
        $this->assertEquals($expectedExtractors,$extractors);
        $expectedLists = array();
        $this->assertEquals($expectedLists,$lists);
        $expectedStats = array(
                $extractorObj->getStatisticsName() => array(
                    "annotations"=>0,
                    "relations"=>0,
                    "lemmas"=>count($extractorData),
                    "attributes"=>0
                )
        );
        $this->assertEquals($expectedStats,$extractor_stats);

        $this->checkFiles($report_id,$disamb_only,$tagging_method,$documentDBData,$extractorData,$format);

    }

    private function checkFiles($report_id,$disambOnly,$tagging_method,$reportData,$extractorData,$format) {

        $expectedBaseFileName = $this->virtualDir->url().'/'.str_pad($report_id,8,'0',STR_PAD_LEFT);
        $scl=new SimpleCcl($reportData,$tagging_method,$disambOnly);
        // if no annotations, there are no lemmas
        //$scl->addLemmas($extractorData);

        $this->assertDocumentExport($format, $expectedBaseFileName, $report_id, $reportData, $scl, array(), array());

    } // checkFiles()

} // CorpusExporter_part5_Test class

?>
