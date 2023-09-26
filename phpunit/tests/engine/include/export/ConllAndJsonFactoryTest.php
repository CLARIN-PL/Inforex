<?php

mb_internal_encoding("UTF-8");
//use org\bovigo\vfs\vfsStream; // for vfsStream

class ConllAndJsonFactoryTest extends PHPUnit_Framework_TestCase {

    private $virtualDir = null;
    // data for test
    private $report_id = null;
    // data for class method
    private $file_path_without_ext = null;

    protected function setUp() {

        $this->virtualDir = org\bovigo\vfs\vfsStream::setup('root',null,[]);
        $this->generateFullTestData();

    } // setUp()

    private function generateReportTestData() {

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

	private function generateTokensTestData() {

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

    private function generateCclTestData() {

        // $report parameter
        $report = $this->generateReportTestData();
		// $tokens parameter
		$tokens = $this->generateTokensTestData();
		// $tags_by_tokens parameter
		$tags_by_tokens = array();

        $ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags_by_tokens);
        return $ccl;
    
    } // generateCclTestData()

    private function generateFullTestData() {

        $this->report_id = 1234;

        // $file_path_without_ext parameter
        $this->file_path_without_ext = $this->virtualDir->url()."/test";

        // $ccl parameter
        $this->ccl = $this->generateCclTestData();


    } // generateFullTestData()

// tests

    public function testEmptyReportMakesEmptyDataToWrite() {

        // args for call
            $report = array(); 
            $tokens = array();
            $tags_by_tokens = array();
        $ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags_by_tokens);
        $tokens = array();
        $relations = array();
        $annotations = array();
        $tokens_ids = array();
        $annotations_by_id = array();
    
        // invoke protected method
        $protectedMethod = new ReflectionMethod('ConllAndJsonFactory','makeConllAndJsonExportData');
        $protectedMethod->setAccessible(True);
        list($conll,$json_builder) = $protectedMethod->invoke(new ConllAndJsonFactory(),$ccl,$tokens,$relations,$annotations,$tokens_ids,$annotations_by_id);
        
        // check results
        $expectedConll = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n";
        $this->assertEquals($expectedConll,$conll);

        $expectedJson = array(
            "chunks" => array(
                array()
            ),
            "relations" => array(),
            "annotations" => array()
        );
        $this->assertEquals($expectedJson,$json_builder);

    } // testEmptyReportMakesEmptyDataToWrite()

    public function testAllDataPlacesToDataConllAndJsonStructures() {

        // args for call
        $tokens = array();
    
        // DbToken::getTokenByReportId($report_id, null, true);
        $relations = array();
        $annotations = array();
        $tokens_ids = array();
        $annotations_by_id = array();

        // invoke protected method
        $protectedMethod = new ReflectionMethod('ConllAndJsonFactory','makeConllAndJsonExportData');
        $protectedMethod->setAccessible(True);
        list($conll,$json_builder) = $protectedMethod->invoke(new ConllAndJsonFactory(),$this->ccl,$tokens,$relations,$annotations,$tokens_ids,$annotations_by_id);

        // check results
        $expectedConll = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n";
        $expectedConll .=   "0\t0\tTo\t\t0\t1\tO\t_\t_\t_\n".
                            "1\t1\tjest\t\t2\t5\tO\t_\t_\t_\n".
                            "2\t2\tduże\t\t6\t9\tO\t_\t_\t_\n".
                            "3\t3\tokno\t\t10\t13\tO\t_\t_\t_\n".
                            "4\t4\t.\t\t14\t14\tO\t_\t_\t_\n".
                            "\n".
                            "5\t0\tBardzo\t\t15\t20\tO\t_\t_\t_\n".
                            "6\t1\tduże\t\t21\t24\tO\t_\t_\t_\n".
                            "7\t2\t.\t\t25\t25\tO\t_\t_\t_\n".
                            "\n";
		$this->assertEquals($expectedConll,$conll);

        $expectedJson = array(
            "chunks" => array(
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
            ),
            "relations" => array(),
            "annotations" => array()
        );
        $this->assertEquals($expectedJson,$json_builder);

    } // testAllDataPlacesToDataConllAndJsonStructures() 

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
