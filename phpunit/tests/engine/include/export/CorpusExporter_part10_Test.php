<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class CorpusExporter_part10_Test extends PHPUnit_Framework_TestCase
{
    private $virtualDir = null;

    protected function setUp() {

        $this->virtualDir = vfsStream::setup('root',null,[]);

    } // setUp()

//    protected function export_document($report_id, $extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
 
    public function testExport_documentCallsExportreportcontentMethod ()
    {
        // export_document() dumb parameters
        $report_id = 1;
        $extractors = array();
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $output_folder = $this->virtualDir->url();
        $subcorpora = '';
        $tagging_method = 'tagger';

        // results returned by mocking methods
        $reportContent = "tekst dokumentu raportu";
        $reportFlags = array('flagnamelowercase'=>array(-1)); 
        $reportTokens = array();
        $reportTags = array();
        $report = array( 'id'=>$report_id, 'content'=>$reportContent );
        $reportExt = null;

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('getFlagsByReportId','getTokenByReportId',
                            'getReportTagsByTokens','getReportById',
                            'getReportExtById','exportReportContent')) 
            -> getMock();
        $mockCorpusExporter -> method('getFlagsByReportId')
            -> will($this->returnValue($reportFlags));
        $mockCorpusExporter -> method('getTokenByReportId')
            -> will($this->returnValue($reportTokens));
        $mockCorpusExporter -> method('getReportTagsByTokens')
            -> will($this->returnValue($reportTags));
        $mockCorpusExporter -> method('getReportById')
            -> will($this->returnValue($report));
        $mockCorpusExporter -> method('getReportExtById')
            -> will($this->returnValue($reportExt));

        // method exportReportContent called exactly once with proper args:
        $expectedReportArg = $report;
        $expectedReportArg['subcorpus'] = ''; // this one is added
        $expectedBaseFileName = $output_folder.'/'.str_pad($report_id,8,'0',STR_PAD_LEFT);
        $mockCorpusExporter -> expects($this->once())
            ->method('exportReportContent')
            ->with($expectedReportArg,$expectedBaseFileName);
 
        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod($mockCorpusExporter,'export_document');
        $protectedMethod->setAccessible(True);

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

    } // testExport_documentCallsExportreportcontentMethod()

// protected function exportReportContent($report,$file_path_without_ext){...}

    public function testExportreportcontentsExportsDocumentContentToTxtFile() {

		$report_id = 1;
		$reportContent = "tekst dokumentu raportu";
		$file_path_without_ext = $this->virtualDir->url().'/testname'; 
		$report = array( 'id'=>$report_id, 'content'=>$reportContent );

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','exportReportContent');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

		$result=$protectedMethod->invokeArgs($ce,array($report,$file_path_without_ext));

        // returns True
        $this->assertTrue($result);
        // no errors collected
        $expectedErrors = array();
        $this->assertEquals($expectedErrors,
            $export_errorsPrivateProperty->getValue($ce)
            );
        //check TxtFile
        $expectedTxtContent = $reportContent;
        $resultTxtFile = file_get_contents($file_path_without_ext.'.txt');
        $this->assertEquals($expectedTxtContent,$resultTxtFile);

    } // testExportreportcontentsExportsDocumentContentToTxtFile()

    public function testExportreportcontentsWithOversizedDocumentSetInternalError() {
        $report_id = 1;
		$reportContent = str_repeat('s',300000); // content too long
        $file_path_without_ext = $this->virtualDir->url().'/testname';
        $report = array( 'id'=>$report_id, 'content'=>$reportContent );

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','exportReportContent');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($ce,array($report,$file_path_without_ext));

        // returns False
        $this->assertFalse($result);
        // internal errors table
        $internalErrorsTable = $export_errorsPrivateProperty->getValue($ce);
        $expectedErrorsCount = 1;
        $this->assertEquals($expectedErrorsCount,count($internalErrorsTable));
        $expectedErrorsKey = "Text too long to display (over 50k characters)";
        $this->assertEquals(1,$internalErrorsTable[8]['details']['errors'][$expectedErrorsKey]);

    } // testExportreportcontentsWithOversizedDocumentSetInternalError() 

} // CorpusExporter_part10_Test class

?>
