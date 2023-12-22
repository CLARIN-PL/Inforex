<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class CorpusExporter_part12_Test extends PHPUnit_Framework_TestCase
{
    // tests with lemma fields sets    

    private $virtualDir = null;

    protected function setUp() {

        $this->virtualDir = vfsStream::setup('root',null,[]);

    } // setUp()

    private function emptyExtractorData() {

        return array("annotations"=>array(), "relations"=>array(), "lemmas"=>array(), "attributes"=>array());

    } // emptyExtractorData

    private function lemmaData() {

        return array ( 'report_annotation_id' => '2', 'lemma' => 'lemat dodany do okno', 'id' => '2', 'report_id' => '1', 'type_id' => '119', 'type' => 'nam_oth', 'group' => '1', 'from' => '10', 'to' => '13', 'text' => 'okno', 'user_id' => NULL, 'creation_time' => '2022-10-03 08:34:21', 'stage' => 'final', 'source' => 'user', 'annotation_type_id' => '119', 'name' => 'nam_oth', 'description' => 'Nazwy własne niezaklasyfikowane do pozostałych grup (w przypadku braku bardziej szczegółowego typu anotacji w obrębie nam_oth).', 'group_id' => '1', 'annotation_subset_id' => '8', 'level' => '0', 'short_description' => '', 'css' => 'background: lightgreen; border: 1px dashed red; border-bottom: 2px solid red;', 'cross_sentence' => '0', 'shortlist' => '0', 'annotation_id' => NULL, 'annotation_attribute_id' => NULL, 'value' => NULL );

    } // lemmaData()

    private function annotationWithLemmaData() {

        return array ( 'id' => '2', 'report_id' => '1', 'type_id' => '119', 'from' => '10', 'to' => '13', 'text' => 'okno', 'user_id' => '1', 'creation_time' => '2022-10-03 08:34:21', 'stage' => 'final', 'source' => 'user', 'type' => 'nam_oth', 'group_id' => '1', 'annotation_subset_id' => '8', 'lemma' => 'lemat dodany do okno', 'login' => 'admin', 'screename' => 'Inforex Admin' );

    } // annotationWithLemmaData()

    private function annotationWoLemmaData() {

        return array ( 'id' => '1', 'report_id' => '1', 'type_id' => '360', 'from' => '6', 'to' => '9', 'text' => 'duże', 'user_id' => '1', 'creation_time' => '2022-10-03 08:07:37', 'stage' => 'final', 'source' => 'user', 'type' => 'nam_adj', 'group_id' => '1', 'annotation_subset_id' => '52', 'lemma' => NULL, 'login' => 'admin', 'screename' => 'Inforex Admin' );

    } // annotationWoLemmaData() 

    public function test_LemmaInAnnotationsTable() {

        // export_document() dumb parameters
        $report_id = 1;
        $fakeExtractor = array();
        $extractors = array( $fakeExtractor );
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $output_folder = $this->virtualDir->url();
        $subcorpora = '';
        $tagging_method = 'tagger';



        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
			-> setMethods(array('getFlagsByReportId','getTokenByReportId',
							'getReportTagsByTokens','getReportById',
                            'getReportExtById','exportReportContent',
                            'updateLists','createIniFile',
                            'checkIfAnnotationForLemmaExists',
                            'checkIfAnnotationForRelationExists',
                            //'sortUniqueAnnotationsById',
                            'dispatchElements','generateCcl',
                            'runExtractor'))
            -> getMock();

		$reportFlags = array('flagnamelowercase'=>array(-1));
        $mockCorpusExporter -> method('getFlagsByReportId')
            -> will($this->returnValue($reportFlags));
        $annotations = array();
        $returnedTableList = [$annotations,array(),array(),array()];
        $mockCorpusExporter -> expects($this->once())
            ->method('dispatchElements')
            -> will($this->returnValue($returnedTableList));
		$reportTokens = array();
        $mockCorpusExporter -> method('getTokenByReportId')
            -> will($this->returnValue($reportTokens));
		$reportTags = array();
        $mockCorpusExporter -> method('getReportTagsByTokens')
            -> will($this->returnValue($reportTags));
        $reportContent = "tekst dokumentu raportu";
        $report = array( 'id'=>$report_id, 'content'=>$reportContent,
                    'name'=>"report name must exists" );
        $mockCorpusExporter -> method('getReportById')
            -> will($this->returnValue($report));
        $fileName = str_pad($report_id,8,'0',STR_PAD_LEFT);
        $returnedCcl = new CclExportDocument($report,$reportTokens,$reportTags);
        //$returnedCcl -> setFileName($fileName);
        $mockCorpusExporter -> expects($this->once())
            ->method('generateCcl')
            ->with($report,$reportTokens,$reportTags)
            -> will($this->returnValue($returnedCcl));

		// reflection for acces to private elements
		$protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCorpusExporter,'export_document');

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

		// returned results in $extractor_stats, $lists and export files


    } // test_LemmaInAnnotationsTable()

// =====

    public function testMinimalParameterForExampleExecution() {
    // minimal setting for proper execution of mock export_document 

        // var_dump(file_get_contents('.xml')); // zwraca zawartość ?
        $report_id = 1;
        $fakeExtractor = array("extractor" => null, "params"=>array(), "flag_name"=>null, "flag_ids"=>array());
        $extractors = array( $fakeExtractor );
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $output_folder = $this->virtualDir->url();
        $subcorpora = array(1=>'must exists for report[subcorpus_id] index');
        $tagging_method = 'tagger';
        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array(
                'getFlagsByReportId', // block fetch() error
                'getTokenByReportId', // block fetch() error
                'getReportTagsByTokens', // block fetch() error
                'getReportById', // block fetch() error
                'getReportExtById', // block fetch() error
                'generateCcl', // for proper filename generation
                ))
                            -> getMock();
        $mockCorpusExporter -> method('getTokenByReportId')
            -> will($this->returnValue(array())); // block array_column error
        // for create proper filename for output files:
        $fileName = str_pad($report_id,8,'0',STR_PAD_LEFT);
        $report = array('id'=>$report_id, 
                        'content'=>'content must exists',
                        'name'=>"report name must exists",
                        'subcorpus_id'=>1,
                        "date"=>'DATA', 
                        "title"=>'TITLE', 
                        "source"=>'SOURCE', 
                        "author"=>'AUTHOR', 
                        "tokenization"=>'TOKENIZATION',
                        "format"=>'FORMAT'); 
        $tokens = array(); $tags = array();
        $ccl = new CclExportDocument($report,$tokens,$tags); 
        $ccl -> setFileName($fileName);
        $mockCorpusExporter 
            -> expects($this->once())
            -> method('getReportById')
            -> with($report_id) 
            -> will($this->returnValue($report));
        $mockCorpusExporter -> expects($this->once())
            -> method('generateCcl')
            -> with($report,array(),null) 
            -> will($this->returnValue($ccl));
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCorpusExporter,'export_document');
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));
        
        // possible outputs 
        $this->assertEquals(array(),$lists);    // lists array
        $this->assertEquals(array(),$extractor_stats); // extractor_stats array
        // 6 files should be created
        $file_path_without_ext = $output_folder."/".$fileName;
        //  INI file
        $this->assertTrue(file_exists($file_path_without_ext.'.ini'));
        //  TXT file 
        $this->assertTrue(file_exists($file_path_without_ext.'.txt'));
        //  XML file 
        $this->assertTrue(file_exists($file_path_without_ext.'.xml'));
        //  REL.XML file 
        $this->assertTrue(file_exists($file_path_without_ext.'.rel.xml'));
        //  JSON file 
        $this->assertTrue(file_exists($file_path_without_ext.'.json'));
        //  CONLL file 
        $this->assertTrue(file_exists($file_path_without_ext.'.conll'));
 
        //$resultFileContent = file_get_contents($file_path_without_ext.'.xml');

    } // testMinimalParameterForExampleExecution() 

    public function testMainFlowForXMLLemmaExport() {

        $report_id = 1;
        $flag_name = 'flag_short_name';
        $flag_id = 1;   // -1,...,5
        $fakeExtractor = array( 
            'name' => 'extractorNAME', 
            "flag_name" => $flag_name,
            "flag_ids" => array($flag_id), 
            "extractor" => null,
            "params" => array() );
        $extractors = array( $fakeExtractor );
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $output_folder = $this->virtualDir->url();
        $subcorpora = '';
        $tagging_method = 'tagger';
		// emulowane dane z bazy danych
		$flags = array( $flag_name => $flag_id );
        $report = array('id'=>$report_id,
                        "content"=>'To jest duże okno.',
                        'name'=>"report name must exists",
                        'subcorpus_id'=>1,
                        "date"=>'DATA',
                        "title"=>'TITLE',
                        "source"=>'SOURCE',
                        "author"=>'AUTHOR',
                        "tokenization"=>'TOKENIZATION',
                        "format"=>'FORMAT');
		$tokens = array(
			// all fields from DB, enough are "from","to","token_id" 
			// from,to are offset in content without white chars and tags
			array( "from"=>6, "to"=>9, "orth"=>'duże', "eos"=>false, "token_id"=>231, "report_id"=>$report_id ),
			array( "from"=>10, "to"=>13, "orth"=>'okno', "eos"=>true, "token_id"=>2314, "report_id"=>$report_id  ),
		);
        // records for tokens id's above
		$tags_by_tokens = array( 
            231 => array (
                    // must have "disamb","ctag","base_text" 
                    [ "token_tag_id" => "30","token_id"=>"231","disamb"=>"1","ctag_id"=>"25","ctag"=>"CTAG","tagset_id"=>1,"base_id"=>7,"base_text"=>"BASE" ]
                )
            );
		$annotations = array(
			$this->annotationWoLemmaData(),
			$this->annotationWithLemmaData()
		);
		$relations = array();
		$lemmas = array( $this->lemmaData() );
		$attributes = array();
        $extractors[0]["extractor"] = 
            function($report_id, $params, &$extractor_elements) {
                $extractor_elements=  array( 
                    "relations"=>[], 
                    "attributes"=>[],
                    "annotations" => array(
                        $this->annotationWoLemmaData(),
                        $this->annotationWithLemmaData() ),
                    "lemmas" => array( $this->lemmaData() ));
            };
 
        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array(
                'getFlagsByReportId', // block fetch() error
                'getTokenByReportId', // block fetch() error
                'getReportTagsByTokens', // block fetch() error
                'getReportById', // block fetch() error
                'getReportExtById', // block fetch() error
                )) -> getMock();
        $mockCorpusExporter -> expects($this->once())
			-> method('getTokenByReportId')
			-> with($report_id)
            -> will($this->returnValue($tokens)); // block array_column error
		//  === dodane do generowania lematów
		// wygenerowne flagi - używane tylko w ekstraktorze
		$mockCorpusExporter -> expects($this->once())
			-> method('getFlagsByReportId')
			-> with($report_id)
			-> will($this->returnValue($flags));
        // emulacja getReportById - dla generateCcl
        $mockCorpusExporter -> expects($this->once())
            -> method('getReportById')
            -> with($report_id)
            -> will($this->returnValue($report))
        ;
		// emulacja getReportTagsByTokens - dla generate Ccl
		$token_ids = array(231,2314); // array_column($tokens, 'token_id')
		$mockCorpusExporter -> expects($this->once())
            -> method('getReportTagsByTokens')
			-> with($report_id,$token_ids,$disamb_only,$tagging_method)
			-> will($this->returnValue($tags_by_tokens))
		;

        // reflection for acces to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCorpusExporter,'export_document');

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

		// check results in XML file
		$fileName = str_pad($report_id,8,'0',STR_PAD_LEFT);
		$resultFileName = $output_folder.'/'.$fileName.".xml";
        $this->assertTrue(file_exists($resultFileName));
		$resultFileContent = file_get_contents($resultFileName);
		//var_dump($resultFileContent);
		// prefix for proper XML
		$expectedXmlContentPrefix = '<?xml version="1.0" encoding="UTF-8"?';
		$this->assertRegexp('@'.$expectedXmlContentPrefix."@",$resultFileContent);
		// annotacja bez lematu ( lemma=NULL )
		$expectedAnnWithoutLemmaLine = '<ann chan="nam_adj">1</ann>';
		$this->assertRegexp('@'.$expectedAnnWithoutLemmaLine.'@m',$resultFileContent);
		// annotacja towarzysząca lematowi
		$expectedAnnWithLemmaLine = '<ann chan="nam_oth">2</ann>';
        $this->assertRegexp('@'.$expectedAnnWithLemmaLine.'@m',$resultFileContent);
		// lemat - z pozycji w $lemmas
		$expectedLemmaLine = '<prop key="nam_oth:lemma">lemat dodany do okno</prop>';
		$this->assertRegexp('@'.$expectedLemmaLine.'@m',$resultFileContent);
		// lemma line after anno line in export XML 
        $expectedAnnWithLemmaLines = 
'<ann chan="nam_oth">2</ann>'.'\s*'.'<prop key="nam_oth:lemma">lemat dodany do okno</prop>';
        $this->assertRegexp('@'.$expectedAnnWithLemmaLines.'@m',$resultFileContent);

        // check results in JSON file
        $resultFileName = $output_folder.'/'.$fileName.".json";
        $resultFileContent = file_get_contents($resultFileName);
        //var_dump($resultFileContent);
        // annotacja bez lematu ( lemma=NULL )
        $expectedAnnWithoutLemmaPattern = '@'
            .'"annotations": '.'.+'        // w sekcji po słowie 'annotations'
            .'\{.+"from": "6",.+"to": "9",[^\}]*' // w tokenie na poz. 6-9
                .'"lemma": null,'   // jest wpis o lemacie
            .'.*\}'.'@s';
        $this->assertRegexp($expectedAnnWithoutLemmaPattern,$resultFileContent);
        // annotacja z lematem ( "lemat dodany do okno" )
        $expectedAnnWithLemmaPattern = '@'
            .'"annotations": '.'.+'        // w sekcji po słowie 'annotations'
            .'\{.+"from": "10",.+"to": "13",[^\}]*' // w tokenie na poz. 10-13
                .'"lemma": "lemat dodany do okno",'   // jest wpis o lemacie
            .'.*\}'.'@s';
        $this->assertRegexp($expectedAnnWithLemmaPattern,$resultFileContent);
       

	 }// testMainFlowForXMLLemmaExport()

} // CorpusExporter_part12_Test class

?>
