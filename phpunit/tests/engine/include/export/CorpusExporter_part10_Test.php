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
 
    public function testExport_documentExportsDocumentContentToTxtFile()
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
        //$reportContent = str_repeat('s',300000);
        $reportFlags = array('flagnamelowercase'=>array(-1)); 
        $reportTokens = array();
        $reportTags = array();
        $report = array( 'id'=>$report_id, 'content'=>$reportContent );
        $reportExt = null;

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('getFlagsByReportId','getTokenByReportId',
                            'getReportTagsByTokens','getReportById',
                            'getReportExtById')) 
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
 
        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod($mockCorpusExporter,'export_document');
        $protectedMethod->setAccessible(True);
        $export_errorsPrivateProperty = new ReflectionProperty($mockCorpusExporter,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True); 

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

        // no errors check
        $expectedErrors = array();
        $this->assertEquals($expectedErrors,
            $export_errorsPrivateProperty->getValue($mockCorpusExporter)
            );
        //check TxtFile
        $expectedBaseFileName = $output_folder.'/'.str_pad($report_id,8,'0',STR_PAD_LEFT);
        $expectedTxtContent = $reportContent;
        $resultTxtFile = file_get_contents($expectedBaseFileName.'.txt');
        $this->assertEquals($expectedTxtContent,$resultTxtFile);

    } // testExport_documentExportsDocumentContentToTxtFile()

    public function testExport_ErrorneusContentSetInternalError()
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
        $reportContent = str_repeat('s',300000); // content too long
        $reportFlags = array('flagnamelowercase'=>array(-1));
        $reportTokens = array();
        $reportTags = array();
        $report = array( 'id'=>$report_id, 'content'=>$reportContent );
        $reportExt = null;

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('getFlagsByReportId','getTokenByReportId',
                            'getReportTagsByTokens','getReportById',
                            'getReportExtById'))
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

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod($mockCorpusExporter,'export_document');
        $protectedMethod->setAccessible(True);
        $export_errorsPrivateProperty = new ReflectionProperty($mockCorpusExporter,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

        // internal errors table
		$internalErrorsTable = $export_errorsPrivateProperty->getValue($mockCorpusExporter);
		$expectedErrorsCount = 1;
		$this->assertEquals($expectedErrorsCount,count($internalErrorsTable)); 
        $expectedErrorsKey = "Text too long to display (over 50k characters)";
        $this->assertEquals(1,$internalErrorsTable[2]['details']['error'][$expectedErrorsKey]);

    } // testExport_ErrorneusContentSetInternalError()

} // CorpusExporter_part10_Test class

?>
