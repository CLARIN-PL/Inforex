<?php

mb_internal_encoding("UTF-8");
//use org\bovigo\vfs\vfsStream; // for vfsStream

class ConllAndJsonFactoryTest extends PHPUnit_Framework_TestCase {

    private $virtualDir = null;
    // data for test
    private $report_id = null;
	private $ccl = null;
	private $tokens = null;
	private $tokens_ids = null;
    // data for class method
    private $file_path_without_ext = null;
	// reflection to protected method in tested class
	private $protectedMethod = null;

    protected function setUp() {

        $this->virtualDir = org\bovigo\vfs\vfsStream::setup('root',null,[]);
        $this->generateFullTestData();
        // protected method to invoke in all tests
        $this->protectedMethod = new ReflectionMethod('ConllAndJsonFactory','makeConllAndJsonExportData');
        $this->protectedMethod->setAccessible(True);

    } // setUp()

    private function generateReportTestData($report_id=1234) {

		if(!$this->report_id) {
			$this->report_id = $report_id;
		}
		// $report = DbReport::getReportById($report_id);
		//          "SELECT * FROM reports WHERE id = $report_id"
		//          id,corpora,date,title,source,author,content,type,
		//          status,user_id,subcorpus_id,tokenization,format_id,
		//          lang,filename,parent_report_id,deleted
		$report = array( "id"=>$this->report_id, "corpora"=>12,
                "date"=>'1970-01-01', "title"=>'tytuł', "source"=>'źródło',
                "author"=>'Autor',
                "content"=>'To jest duże okno. Bardzo duże.', "type"=>1,
                "status"=>2, "user_id"=>1, "subcorpus_id"=>null,
                "tokenization"=>null, "format_id"=>2, "lang"=>'pol',
                "filename"=>'plik.txt', "parent_report_id"=>null,
                "deleted"=>0
            );
 		return $report;

    } // generateReportTestData()

	private function generateTokensTestData($report_id=1234) {

        if(!$this->report_id) {
            $this->report_id = $report_id;
        }
 
		$report_id = $this->report_id;
		// $tokens = DbToken::getTokenByReportId($report_id, null, true);
		//          "SELECT * FROM tokens LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`"
		//          token_id, report_id, from, to, eos, orth_id, orth
		$tokens = array(
                ["token_id"=>1, "report_id"=>$report_id, "from"=>0, "to"=>1, "eos"=>0, "orth_id"=>1, "orth"=>'To'],
                ["token_id"=>2, "report_id"=>$report_id, "from"=>2, "to"=>5, "eos"=>0, "orth_id"=>2, "orth"=>'jest'],
                ["token_id"=>3, "report_id"=>$report_id, "from"=>6, "to"=>9, "eos"=>0, "orth_id"=>3, "orth"=>'duże'],
                ["token_id"=>4, "report_id"=>$report_id, "from"=>10, "to"=>13, "eos"=>0, "orth_id"=>4, "orth"=>'okno'],
                ["token_id"=>5, "report_id"=>$report_id, "from"=>14, "to"=>14, "eos"=>1, "orth_id"=>5, "orth"=>'.'],
                ["token_id"=>6, "report_id"=>$report_id, "from"=>15, "to"=>20, "eos"=>0, "orth_id"=>6, "orth"=>'Bardzo'],
                ["token_id"=>7, "report_id"=>$report_id, "from"=>21, "to"=>24, "eos"=>0, "orth_id"=>3, "orth"=>'duże'],
                ["token_id"=>8, "report_id"=>$report_id, "from"=>25, "to"=>25, "eos"=>1, "orth_id"=>5, "orth"=>'.']
            );

		return $tokens;

	} // generateTokensTestData()

    private function getExpectedConllHeader() {
        // static header for all CONLL files
        return "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n";
    } //

    private function getExpectedConll() {
        // expected Conll pattern for our data and not annotations
        $expectedConll = $this->getExpectedConllHeader();
        $expectedConll .=   "0\t0\tTo\t\t0\t1\tO\t_\t_\t_\n".
                            "1\t1\tjest\t\t2\t5\tO\t_\t_\t_\n".
                            "2\t2\tduże\t\t6\t9\tO\t_\t_\t_\n".
                            //"2\t2\tduże\t\t6\t9\tB-\t1\t_\t_\n".
                            //"2\t2\tduże\t\t6\t9\tB-nam_adj\t1\t_\t_\n".
                            "3\t3\tokno\t\t10\t13\tO\t_\t_\t_\n".
                            "4\t4\t.\t\t14\t14\tO\t_\t_\t_\n".
                            "\n".
                            "5\t0\tBardzo\t\t15\t20\tO\t_\t_\t_\n".
                            "6\t1\tduże\t\t21\t24\tO\t_\t_\t_\n".
                            "7\t2\t.\t\t25\t25\tO\t_\t_\t_\n".
                            "\n";
		return $expectedConll; 
    } // getExpectedConll()

	private function getExpectedEmptyJson() {

        return 	array(
            		"chunks" => array(
                			array()
            		),
            		"relations" => array(),
            		"annotations" => array()
        		);

	} // getExpectedEmptyJson()

    private function getExpectedChunks() {
        // returns "chunks" section in expected table for JSON
        // for our data without annotations
        $chunks = array(
                array(
                    array(
                        array('order_id' => 0,'token_id' => 0,'orth' => 'To','ctag' => null,'from' => 0,'to' => 1,'annotations' => array(),'relations' => array()),
                        array('order_id' => 1,'token_id' => 1,'orth' => 'jest','ctag' => null,'from' => 2,'to' => 5,'annotations' => array(),'relations' => array()),
                        array('order_id' => 2,'token_id' => 2,'orth' => 'duże','ctag' => null,'from' => 6,'to' => 9,'annotations' => array(),'relations' => array()),
                        array('order_id' => 3,'token_id' => 3,'orth' => 'okno','ctag' => null,'from' => 10,'to' => 13,'annotations' => array(),'relations' => array()),
                        array('order_id' => 4,'token_id' => 4,'orth' => '.','ctag' => null,'from' => 14,'to' => 14,'annotations' => array(),'relations' => array())
                    ),
                    array(
                        array('order_id' => 5,'token_id' => 0,'orth' => 'Bardzo','ctag' => null,'from' => 15,'to' => 20,'annotations' => array(),'relations' => array()),
                        array('order_id' => 6,'token_id' => 1,'orth' => 'duże','ctag' => null,'from' => 21,'to' => 24,'annotations' => array(),'relations' => array()),
                        array('order_id' => 7,'token_id' => 2,'orth' => '.','ctag' => null,'from' => 25,'to' => 25,'annotations' => array(),'relations' => array())
                    )
                )
            );
        return $chunks;
    } // getExpectedChunks

    private function generateAnnotation_By_IdTestData(
                        $annotationExtractorFields=true,
                        $annotationsWithLemmaField=true,
                        $annotationsWithTypeAttributte=true
                    ) {

        $annotation_id = 1;
        // index must match 'id' field
        $annotations_by_id = array(
            $annotation_id  =>  array(  'id'=>$annotation_id, 
                        // fields, that always exists
                        //  only 'to' changes business logic
                        'report_id'=>1,'type_id'=>360,'from'=>6,'to'=>9,'text'=>'duże','user_id'=>1,'creation_time'=>'2022-10-03 08:07:37','stage'=>'final','source'=>'user','annotation_subset_id'=>'52',
                        // this are always, but from different tables
                        'type'=>'nam_adj','group_id'=>1, 
                    )

        );
        if($annotationExtractorFields) {
            // fields, that exists for extractor 'annotation' only
            $annotations_by_id[$annotation_id]['login']='admin';
            $annotations_by_id[$annotation_id]['screename']='Inforex Admin';
            if($annotationsWithLemmaField){
                $annotations_by_id[$annotation_id]['lemma']='lemat dodany do duże';
            } else {
                $annotations_by_id[$annotation_id]['lemma']=null;
            }
        } else {
            // fields, that exists for extractors 'annotation_id' 
            //   and 'annotation_subset_id' only
            $annotations_by_id[$annotation_id]['group']=1;
            $annotations_by_id[$annotation_id]['name']='nam_adj';
            $annotations_by_id[$annotation_id]['description']='Przymiotnik utworzony od nazwy własnej';
            $annotations_by_id[$annotation_id]['css']='background: lightgreen;';
            $annotations_by_id[$annotation_id]['cross_sentence']='0';
            $annotations_by_id[$annotation_id]['shortlist']='0';
            // this section only for simple extractor with attribute in DB
            if($annotationsWithTypeAttributte) {
                $annotations_by_id[$annotation_id]['annotation_id']=$annotation_id;
                $annotations_by_id[$annotation_id]['annotation_attribute_id']=1;
                $annotations_by_id[$annotation_id]['value']='valueAtrTypu';
                $annotations_by_id[$annotation_id]['prop']='valueAtrTypu';
            }
        }

        return $annotations_by_id;

    } // generateAnnotation_By_IdTestData()

    private function generateAnnotationsFromAnnotations_By_Id(array $annotations_by_id) {
        return array_values($annotations_by_id);

    } // generateAnnotationsFromAnnotations_By_Id

    private function generateFullTestData() {

        $this->report_id = 1234;

        // $file_path_without_ext parameter
        $this->file_path_without_ext = $this->virtualDir->url()."/test";

		// $tokens parameter
		$this->tokens = $this->generateTokensTestData($this->report_id);
        // tak jest ustawiane zawsze w CorpusExporter
        $this->tokens_ids = array_column($this->tokens, 'token_id');

        // $ccl parameter
		$this->ccl = $this->generateFullCclData();

    } // generateFullTestData()

	private function makeMockToken($id,$orth,$lexemes,$from,$to) {

		$mockToken = $this->getMockBuilder(CclToken::class)->getMock();
        $mockToken->id = $id; 
		$mockToken->orth = $orth; 
		$mockToken->lexemes = $lexemes; 
		$mockToken->from = $from; 
		$mockToken->to = $to;
		return $mockToken;

	} // makeMockToken

    private function generateFullCclData() {

        // mocked $ccl argument for empty report
		$mockToken11 = $this->makeMockToken(0,"To",array(),0,1);
		$mockToken12 = $this->makeMockToken(1,"jest",array(),2,5);
		$mockToken13 = $this->makeMockToken(2,"duże",array(),6,9);
		$mockToken14 = $this->makeMockToken(3,"okno",array(),10,13);
		$mockToken15 = $this->makeMockToken(4,".",array(),14,14);
        $mockSentence1 = $this->getMockBuilder(CclSentence::class)->getMock();
        $mockSentence1->tokens = array($mockToken11,$mockToken12,$mockToken13,$mockToken14,$mockToken15);
		$mockToken21 = $this->makeMockToken(5,"Bardzo",array(),15,20);
		$mockToken22 = $this->makeMockToken(6,"duże",array(),21,24);
		$mockToken23 = $this->makeMockToken(7,".",array(),25,25);
		$mockSentence2 = $this->getMockBuilder(CclSentence::class)->getMock();
        $mockSentence2->tokens = array($mockToken21,$mockToken22,$mockToken23);
        $mockChunk = $this->getMockBuilder(CclChunk::class)->getMock();
        $mockChunk->sentences = array($mockSentence1,$mockSentence2);
        $mockCclDocument = $this->getMockBuilder(CclDocument::class)->getMock();
        $mockCclDocument->chunks = array($mockChunk);
        return  $mockCclDocument;

    } // generateFullCclData()

// tests

    public function testEmptyReportMakesEmptyDataToWrite() {

		// all empty input data generates minimal export output
        // args for call
        $tokens = array();
        $relations = array();
        $annotations = array();
        $tokens_ids = array();
        $annotations_by_id = array();
		// mocked $ccl argument for empty report
        $mockChunk = $this->getMockBuilder(CclChunk::class)->getMock();
        $mockChunk->sentences = array();
        $mockCclDocument = $this->getMockBuilder(CclDocument::class)->getMock();
        $mockCclDocument->chunks = array($mockChunk);
        $ccl = $mockCclDocument;
    
        // invoke tested method
        list($conll,$json_builder) = $this->protectedMethod->invoke(new ConllAndJsonFactory(),$ccl,$tokens,$relations,$annotations,$tokens_ids,$annotations_by_id);
        
        // check results
		$this->assertEquals($this->getExpectedConllHeader(),$conll);
        $this->assertEquals($this->getExpectedEmptyJson(),$json_builder);

    } // testEmptyReportMakesEmptyDataToWrite()

    public function testAllDataPlacesToDataConllAndJsonStructures() {

        // args for call
        $relations = array();
        $annotations_by_id = $this->generateAnnotation_By_IdTestData(False,True);
        $annotations = $this->generateAnnotationsFromAnnotations_By_Id($annotations_by_id);

		// invoke tested method
        list($conll,$json_builder) = $this->protectedMethod->invoke(new ConllAndJsonFactory(),$this->ccl,$this->tokens,$relations,$annotations,$this->tokens_ids,$annotations_by_id);

        // check results
		$expectedConll = $this->getExpectedConll();
        $expectedConll = str_replace(
                            "2\t2\tduże\t\t6\t9\tO\t_\t_\t_\n",
                            "2\t2\tduże\t\t6\t9\tB-nam_adj\t1\t_\t_\n",
                            $expectedConll);
		$this->assertEquals($expectedConll,$conll);

        $expectedChunks = $this->getExpectedChunks();
        // annotation_id to 'annotations' list in 3-rd chunk
        $expectedChunks[0][0][2]['annotations'][] = 1;
        $expectedJson = array(
            "chunks" => $expectedChunks,
            "relations" => array(),
            "annotations" => array(
                array('id'=>1,'report_id'=>1,'type_id'=>360,'from'=>6,'to'=>9,
                    'text' => 'duże','user_id' => 1,
                    'creation_time' => '2022-10-03 08:07:37','stage' => 'final',
                    'source' => 'user','annotation_subset_id' => '52',
                    'type'=>'nam_adj','group_id'=>1,
/*
                    'login' => 'admin','screename' => 'Inforex Admin',
                    'lemma' => 'lemat dodany do duże',
*/
                    'group' => 1,'description' => 'Przymiotnik utworzony od nazwy własnej','css' => 'background: lightgreen;','cross_sentence' => '0','shortlist' => '0',
                    'name'=>'nam_adj', 
                    // tylko jeśli istnieje atrybut
                    'annotation_id' => 1,'annotation_attribute_id' => 1,'value' => 'valueAtrTypu','prop' => 'valueAtrTypu'
                ),
            )
        );
        $this->assertEquals($expectedJson,$json_builder);

    } // testAllDataPlacesToDataConllAndJsonStructures() 

    public function testDocumentWithoutTokenizationPlaceDataToJsonAndConllArray() {
		// CCL is self-tokenized by constructor
        // args for call
		$tokens = array();
        // tak jest ustawiane zawsze w CorpusExporter
        $tokens_ids = array_column($tokens, 'token_id');
        $relations = array();
        $annotations = array();
        $annotations_by_id = array();

		// invoke tested method
        list($conll,$json_builder) = $this->protectedMethod->invoke(new ConllAndJsonFactory(),$this->ccl,$tokens,$relations,$annotations,$tokens_ids,$annotations_by_id);

        // check results
        $this->assertEquals($this->getExpectedConll(),$conll);

		$expectedJson = $this->getExpectedEmptyJson();
		$expectedJson["chunks"] = $this->getExpectedChunks();
		$this->assertEquals($expectedJson,$json_builder);

		// with tokens without annotations there are all the same as above

		// invoke tested method
        list($conll,$json_builder) = $this->protectedMethod->invoke(new ConllAndJsonFactory(),$this->ccl,$this->tokens,$relations,$annotations,$this->tokens_ids,$annotations_by_id);

		$this->assertEquals($this->getExpectedConll(),$conll);
		$this->assertEquals($expectedJson,$json_builder);
 
    } // testDocumentWithoutTokenizationPlaceDataToJsonAndConllArray() 

    public function testGeneralAnnotationFieldsPlacedToJsonAndConllArray() {

        // are all fields which always should be present in annotation 
        // record placed correctly to JSON and CONLL structures

        $this->assertTrue(True);

    } // testGeneralAnnotationFieldsPlacedToJsonAndConllArray

/* class ConllAndJsonFactory has only 1 function
    function exportToConllAndJson($file_path_without_ext, $ccl, $tokens, $relations, $annotations, $tokens_ids, $annotations_by_id)
*/

	public function testDeliveredDataAreWrittenToFiles() {

        $conll = "jakiś text";
        $json_builder = array("a"=>1);

        // this values doesn't matter
            $report = array();
            $tokens = array();
            $tags_by_tokens = array();
        $ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags_by_tokens);
        $tokens = array();
        $relations = array();
        $annotations = array();
        $tokens_ids = array();
        $annotations_by_id = array();

        // self-mocking another method
        $mockedMethodNamesList = array('makeConllAndJsonExportData');
        $mockedResult = array($conll,$json_builder);
        $mock = $this->getMockBuilder(ConllAndJsonFactory::class)
            -> setMethods($mockedMethodNamesList) // ustawia je na null
            -> getMock();
        $mock    // i mockuje na zwracanie określonego rezultatu
            -> method('makeConllAndJsonExportData')
            -> will($this->returnValue($mockedResult));
        
		// metoda exportToConllAndJson() powinna zachować obsługę oryginalną
        $mock->exportToConllAndJson($this->file_path_without_ext,$this->ccl,$tokens,$relations,$annotations,$tokens_ids,$annotations_by_id);

        $expectedConll = $conll;
        $conllFileName = $this->file_path_without_ext.".conll";
        $resultConll = file_get_contents($conllFileName);
        $this->assertEquals($expectedConll,$resultConll);
        $expectedJson = 
'{
    "a": 1
}';
        $jsonFileName = $this->file_path_without_ext.".json";
        $resultJson = file_get_contents($jsonFileName);
        $this->assertEquals($expectedJson,$resultJson);

	} // testDeliveredDataAreWrittenToFiles

} // ConllAndJsonFactoryTest class

?>
