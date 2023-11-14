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
 
    public function testExport_documentCallsCheckifannotationforlemmaexistsMethod() {
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

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('getFlagsByReportId','getTokenByReportId',
                            'getReportTagsByTokens','getReportById',
                            'getReportExtById','exportReportContent',
                            'updateLists','createIniFile',
							'checkIfAnnotationForLemmaExists'))
            -> getMock();
        $mockCorpusExporter -> method('getFlagsByReportId')
            -> will($this->returnValue($reportFlags));
        $mockCorpusExporter -> method('getTokenByReportId')
            -> will($this->returnValue($reportTokens));
        $mockCorpusExporter -> method('getReportTagsByTokens')
            -> will($this->returnValue($reportTags));
        $mockCorpusExporter -> method('getReportById')
            -> will($this->returnValue($report));

        // method checkIfAnnotationForLemmaExists called exactly once with proper args:
        $expectedLemmas = array();
        $expectedAnnotationsById = array();;
        $mockCorpusExporter -> expects($this->once())
            ->method('checkIfAnnotationForLemmaExists')
            ->with($expectedLemmas,$expectedAnnotationsById);

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod($mockCorpusExporter,'export_document');
        $protectedMethod->setAccessible(True);

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

    } // testExport_documentCallsCheckifannotationforlemmaexistsMethod() 
  
    public function testExport_documentCallsCreateinifileMethod()                     {
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

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('getFlagsByReportId','getTokenByReportId',
                            'getReportTagsByTokens','getReportById',
                            'getReportExtById','exportReportContent',
                            'updateLists','createIniFile'))
            -> getMock();
        $mockCorpusExporter -> method('getFlagsByReportId')
            -> will($this->returnValue($reportFlags));
        $mockCorpusExporter -> method('getTokenByReportId')
            -> will($this->returnValue($reportTokens));
        $mockCorpusExporter -> method('getReportTagsByTokens')
            -> will($this->returnValue($reportTags));
        $mockCorpusExporter -> method('getReportById')
            -> will($this->returnValue($report));

        // method createIniFile called exactly once with proper args:
        $expectedReportArg = $report;
        $expectedBaseFileName = $output_folder.'/'.str_pad($report_id,8,'0',STR_PAD_LEFT);
        $mockCorpusExporter -> expects($this->once())
            ->method('createIniFile')
            ->with($expectedReportArg,$subcorpora,$expectedBaseFileName);

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod($mockCorpusExporter,'export_document');
        $protectedMethod->setAccessible(True);

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

    } // testExport_documentCallsCreateinifileMethod()      

    public function testExport_documentCallsUpdatelistsMethod()
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

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('getFlagsByReportId','getTokenByReportId',
                            'getReportTagsByTokens','getReportById',
                            'getReportExtById','exportReportContent',
							'updateLists','createIniFile'))
            -> getMock();
        $mockCorpusExporter -> method('getFlagsByReportId')
            -> will($this->returnValue($reportFlags));
        $mockCorpusExporter -> method('getTokenByReportId')
            -> will($this->returnValue($reportTokens));
        $mockCorpusExporter -> method('getReportTagsByTokens')
            -> will($this->returnValue($reportTags));
        $mockCorpusExporter -> method('getReportById')
            -> will($this->returnValue($report));

        // method updateLists called exactly once with proper args:
		$expectedFlags = $reportFlags;
        $expectedFileNameWithoutExt = str_pad($report_id,8,'0',STR_PAD_LEFT);
        $mockCorpusExporter -> expects($this->once())
            ->method('updateLists')
			->with($expectedFlags,$expectedFileNameWithoutExt,$lists);

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod($mockCorpusExporter,'export_document');
        $protectedMethod->setAccessible(True);

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

    } // testExport_documentCallsUpdatelistsMethod()

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

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('getFlagsByReportId','getTokenByReportId',
                            'getReportTagsByTokens','getReportById',
                            'getReportExtById','exportReportContent',
							'createIniFile')) 
            -> getMock();
        $mockCorpusExporter -> method('getFlagsByReportId')
            -> will($this->returnValue($reportFlags));
        $mockCorpusExporter -> method('getTokenByReportId')
            -> will($this->returnValue($reportTokens));
        $mockCorpusExporter -> method('getReportTagsByTokens')
            -> will($this->returnValue($reportTags));
        $mockCorpusExporter -> method('getReportById')
            -> will($this->returnValue($report));

        // method exportReportContent called exactly once with proper args:
        $expectedReportArg = $report;
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

// protected function updateLists($flags,$reportFileName,&$lists) {...}

    public function testUpdatelistsEmptyCallDoNothing() {

		$flags = array();
		$file_name_without_ext = 'reportFileName';
		$lists = array();

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','updateLists');
        $protectedMethod->setAccessible(True);

        $ce = new CorpusExporter();
		$protectedMethod->invokeArgs($ce,array($flags,$file_name_without_ext,&$lists));

		// check results
		$expectedLists = array();
		$this->assertEquals($expectedLists,$lists); 

    } // testUpdatelistsEmptyCallDoNothing()

    public function testUpdatelistsOnEmptyListsDoNothing() {

        $testFlagName = 'TFN';
        $testFlagId = 123;
        $listName = 'ix';

        $flags = array(
            $testFlagName => $testFlagId
        );
        $file_name_without_ext = 'reportFileName';
        $lists = array();

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','updateLists');
        $protectedMethod->setAccessible(True);

        $ce = new CorpusExporter();
        $listsBeforeCall = $lists;
        $protectedMethod->invokeArgs($ce,array($flags,$file_name_without_ext,&$lists));

        // check results
        $expectedLists = $listsBeforeCall;
        $this->assertEquals($expectedLists,$lists);

    } // testUpdatelistsOnEmptyListsDoNothing() 

    public function testUpdatelistsSetsDocument_namesToLists() {

        $testFlagName = 'TFN';
        $testFlagId = 123;
        $listName = 'ix';

        $flags = array(
            $testFlagName => $testFlagId
        );
        $file_name_without_ext = 'reportFileName';
        $lists = array(
				$listName => array(
                    "flags" => array(  
                        array( "flag_name"=>'flagName', 
                                "flag_ids"=> array(1,2)),
                        array( "flag_name"=>$testFlagName,                                                      "flag_ids"=> array(1,$testFlagId)),
                    )
                )
			);

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','updateLists');
        $protectedMethod->setAccessible(True);

        $ce = new CorpusExporter();
        $listsBeforeCall = $lists;
        $protectedMethod->invokeArgs($ce,array($flags,$file_name_without_ext,&$lists));

        // check results
        $expectedLists = $listsBeforeCall;
        $expectedLists[$listName]["document_names"][$file_name_without_ext.".xml"] = 1;
        $this->assertEquals($expectedLists,$lists);

    } // testUpdatelistsSetsDocument_namesToLists()

// protected function createIniFile($report,$file_path_without_ext) {...}

    public function testCreateinifileExportsReportMetadataToIniFile() {

        $report_id = 1;
        $reportContent = "tekst dokumentu raportu";
		$reportDate = '23/12/2023';
		$reportTitle = 'TYTUŁ RAPORTU';
		$reportSource = 'ŹRÓDŁO RAPORTU';
		$reportAuthor = 'AUTOR RAPORTU';
		$reportTokenization = 'TOKENIZACJA';
		$reportNonidExtKey = 'nonid';
		$reportNonidExtValue = 13;
        $reportNonLNKey = ' klucz ze spacjami';
        $reportNonLNKey_converted = '_klucz_ze_spacjami';
        $reportNonLNValue = 48;
		$subcorpusKey = 341;
		$subcorpusName = 'SUBCORPUS NAME';
		$subcorpora = array( $subcorpusKey => $subcorpusName );
        $file_path_without_ext = $this->virtualDir->url().'/testname';
        $report = array( 'id'=>$report_id, 'subcorpus_id'=>$subcorpusKey,
						'date' => $reportDate, 'title' => $reportTitle,
						'source' => $reportSource, 'author' => $reportAuthor,
						'tokenization' => $reportTokenization );
		$reportExt = array( 'id' => 12,  // id is excluded from .ini
                            $reportNonLNKey => $reportNonLNValue,
							$reportNonidExtKey => $reportNonidExtValue );

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('getReportExtById'))
            -> getMock();
        $mockCorpusExporter -> method('getReportExtById')                                      -> will($this->returnValue($reportExt));

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','createIniFile');
        $protectedMethod->setAccessible(True);

        $result=$protectedMethod->invokeArgs($mockCorpusExporter,array($report,$subcorpora,$file_path_without_ext));

        //check Ini File
        $expectedIniContent = 
"[document]
id = 1
date = $reportDate
title = $reportTitle
source = $reportSource
author = $reportAuthor
tokenization = $reportTokenization
subcorpus = $subcorpusName

[metadata]
$reportNonLNKey_converted = $reportNonLNValue
$reportNonidExtKey = $reportNonidExtValue";
        $resultIniFile = file_get_contents($file_path_without_ext.'.ini');
        $this->assertEquals($expectedIniContent,$resultIniFile);

    } // testCreateinifileExportsReportMetadataToIniFile()
 
// protected function checkIfAnnotationForLemmaExists($lemmas,$annotations_by_id) {...}

    public function testCheckifannotationforlemmaexistsReturnsTrueIfAllLemmasMatched() {
		$annoId1 = 10;  $annoId2 = 20;
        $lemmas = array(
			array( "id"=>$annoId1 ),
			array( "id"=>$annoId2 )
		);
		$annotationsById = array( $annoId1 => True, $annoId2 => True );

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','checkIfAnnotationForLemmaExists');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($ce,array($lemmas,$annotationsById));
        // returns True
        $this->assertTrue($result);

        // internal errors table is empty
        $internalErrorsTable = $export_errorsPrivateProperty->getValue($ce);
		$this->assertEquals(0,count($internalErrorsTable));

    } // testCheckifannotationforlemmaexistsReturnsTrueIfAllLemmasMatched() 
 
    public function testCheckifannotationforlemmaexistsSetInternalError() {

        $annoId1 = 10;  $annoId2 = 20;
        $lemmas = array(
            array( "id"=>$annoId1 ),
            array( "id"=>$annoId2 )
        );
        $annotationsById = array( $annoId1 => True );

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','checkIfAnnotationForLemmaExists');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($ce,array($lemmas,$annotationisById));

        // returns False
        $this->assertFalse($result);

        // internal errors table
        $internalErrorsTable = $export_errorsPrivateProperty->getValue($ce);
        $expectedErrorsCount = 1;
        $this->assertEquals($expectedErrorsCount,count($internalErrorsTable));
        $expectedErrorsKey = "Brak warstwy anotacji dla lematu.";
        $this->assertEquals($expectedErrorsKey,$internalErrorsTable[6]['message']);

    } // testCheckifannotationforlemmaexistsSetInternalError()

} // CorpusExporter_part10_Test class

?>
