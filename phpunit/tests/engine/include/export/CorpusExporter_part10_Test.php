<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class CorpusExporter_part10_Test extends PHPUnit_Framework_TestCase
{
    private $virtualDir = null;

    private function createAccessToProtectedMethodOfClassObject($classObject,$method) {

        // reflection for access to private method
        $protectedMethod = new ReflectionMethod($classObject,$method);
        $protectedMethod->setAccessible(True);
		return $protectedMethod;
		
    } // createAccessToProtectedMethodOfClassObject() 

    protected function setUp() {

        $this->virtualDir = vfsStream::setup('root',null,[]);

    } // setUp()

//    protected function export_document($report_id, $extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
 
    public function testExport_documentCallsAllProcessingMethods() {
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
							'checkIfAnnotationForLemmaExists',
                            'checkIfAnnotationForRelationExists',
							'sortUniqueAnnotationsById',
							'dispatchElements','generateCcl',
                            'runExtractor'))
            -> getMock();
        $mockCorpusExporter -> method('getFlagsByReportId')
            -> will($this->returnValue($reportFlags));
        $mockCorpusExporter -> method('getTokenByReportId')
            -> will($this->returnValue($reportTokens));
        $mockCorpusExporter -> method('getReportTagsByTokens')
            -> will($this->returnValue($reportTags));
        $mockCorpusExporter -> method('getReportById')
            -> will($this->returnValue($report));

        // tested methods called exactly once with proper args:
        $expectedFlags = $reportFlags;
        $expectedExtractor = $fakeExtractor;
        $expectedElements = array(
            'annotations' => array(),
            'relations' => array(),
            'lemmas' => array(),
            'attributes' => array()
        );
        $expectedExtractorStats = $extractor_stats;
        $mockCorpusExporter -> expects($this->once())
            ->method('runExtractor')
            ->with($expectedFlags,$report_id,$expectedExtractor,$expectedElements,$expectedExtractorStats);
        $expectedReport = $report;
        $expectedTokens = $reportTokens;
        $expectedTagsByTokens = array();
		$fileName = str_pad($report_id,8,'0',STR_PAD_LEFT);
		$returnedCcl = new CclDocument(); 
        $returnedCcl -> setFileName($fileName); 
        $mockCorpusExporter -> expects($this->once())
            ->method('generateCcl')
            ->with($expectedReport,$expectedTokens,$expectedTagsByTokens)
            -> will($this->returnValue($returnedCcl));
		$expectedElements = array("annotations"=>array(), "relations"=>array(), "lemmas"=>array(), "attributes"=>array());
		$returnedTableList = [array(),array(),array(),array()];
        $mockCorpusExporter -> expects($this->once())
            ->method('dispatchElements')
            ->with($expectedElements)
            -> will($this->returnValue($returnedTableList));
		$expectedAnnotations = array();
		$returnedAnnotationsById = array();
        $mockCorpusExporter -> expects($this->once())
            ->method('sortUniqueAnnotationsById')
            ->with($expectedAnnotations)
			-> will($this->returnValue($returnedAnnotationsById));
        $expectedRelations = array();
        $mockCorpusExporter -> expects($this->once())
            ->method('checkIfAnnotationForRelationExists')
            ->with($expectedRelations,$returnedAnnotationsById);
        $expectedLemmas = array();
        $mockCorpusExporter -> expects($this->once())
            ->method('checkIfAnnotationForLemmaExists')
            ->with($expectedLemmas,$returnedAnnotationsById);
        $expectedReportArg = $report;
        $expectedBaseFileName = $output_folder.'/'.str_pad($report_id,8,'0',STR_PAD_LEFT);
        $mockCorpusExporter -> expects($this->once())
            ->method('createIniFile')
            ->with($expectedReportArg,$subcorpora,$expectedBaseFileName);
        $expectedFlags = $reportFlags;
        $expectedFileNameWithoutExt = str_pad($report_id,8,'0',STR_PAD_LEFT);
        $mockCorpusExporter -> expects($this->once())
            ->method('updateLists')
            ->with($expectedFlags,$expectedFileNameWithoutExt,$lists);
        $expectedReportArg = $report;
        $mockCorpusExporter -> expects($this->once())
            ->method('exportReportContent')
            ->with($expectedReportArg,$expectedBaseFileName);


        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod($mockCorpusExporter,'export_document');
        $protectedMethod->setAccessible(True);

        // tested call
        $protectedMethod->invokeArgs($mockCorpusExporter,array($report_id,$extractors,$disamb_only,&$extractor_stats,&$lists,$output_folder,$subcorpora,$tagging_method));

    } // testExport_documentCallsAllProcessingMethods
  
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

// protected function checkIfAnnotationForRelationExists($relations,$annotations_by_id) {

    public function testCheckifannotationforrelationsexistsReturnsTrueIfAllRelationsMatched() {
        $sourceId1 = 10; $targetId1 = 13;  
		$sourceId2 = 20; $targetId2 = 27;
        $relations = array(
            array( "source_id"=>$sourceId1, "target_id"=>$targetId1 ),
            array( "source_id"=>$sourceId2, "target_id"=>$targetId2 )
        );
        $annotationsById = array( $sourceId1 => True, $targetId1 => True,
								  $sourceId2 => True, $targetId2 => True  );

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','checkIfAnnotationForRelationExists');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($ce,array($relations,$annotationsById));
        // returns True
        $this->assertTrue($result);

        // internal errors table is empty
        $internalErrorsTable = $export_errorsPrivateProperty->getValue($ce);
        $this->assertEquals(0,count($internalErrorsTable));

    } // testCheckifannotationforrelationsexistsReturnsTrueIfAllRelationsMatched() 

    public function testCheckifannotationforrelationsexistsSetInternalError() {
        $sourceId1 = 10; $targetId1 = 13;
        $sourceId2 = 20; $targetId2 = 27;
        $relations = array(
            array( "source_id"=>$sourceId1, "target_id"=>$targetId1 ),
            array( "source_id"=>$sourceId2, "target_id"=>$targetId2 )
        );
        $annotationsById = array( $targetId1 => True,
                                  $sourceId2 => True  );

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','checkIfAnnotationForRelationExists');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($ce,array($relations,$annotationsById));
        // returns False
        $this->assertFalse($result);

        // internal errors table
        $internalErrorsTable = $export_errorsPrivateProperty->getValue($ce);
        $expectedErrorsCount = 2;
        $this->assertEquals($expectedErrorsCount,count($internalErrorsTable));
        $expectedErrorsKey = "Brak anotacji źródłowej dla relacji.";
        $this->assertEquals($expectedErrorsKey,$internalErrorsTable[4]['message']);
        $expectedErrorsKey = "Brak anotacji docelowej dla relacji.";
        $this->assertEquals($expectedErrorsKey,$internalErrorsTable[5]['message']);

    } // testCheckifannotationforrelationsexistsSetInternalError()

// protected function sortUniqueAnnotationsById($annotations) {...}

    public function testSortuniqueannotationsbyidReturnsIndexedArray() {

		$anno1 = array( "id"=>1 ); $anno2 = array( "id"=>2 );
		$annotations = array( $anno1,$anno2,$anno2 );

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','sortUniqueAnnotationsById');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($ce,array($annotations));

		$expectedAnnotationById = array(
			1 => $anno1,
			2 => $anno2
		);
 		$this->assertEquals($expectedAnnotationById,$result);

    } // testSortuniqueannotationsbyidReturnsIndexedArray() 

    public function testSortuniqueannotationsbyidSetInternalError() {

        $annoWoId = array( "name"=>1 ); $anno2 = array( "id"=>2 );
        $annotations = array( $annoWoId,$anno2,$anno2 );

        // reflection for acces to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','sortUniqueAnnotationsById');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($ce,array($annotations));

        $expectedAnnotationById = array(
            2 => $anno2
        );
        $this->assertEquals($expectedAnnotationById,$result);

        // internal errors table
        $internalErrorsTable = $export_errorsPrivateProperty->getValue($ce);
        $expectedErrorsCount = 1;
        $this->assertEquals($expectedErrorsCount,count($internalErrorsTable));
        $expectedErrorsKey = "Brak identyfikatora anotacji.";
        $this->assertEquals($expectedErrorsKey,$internalErrorsTable[3]['message']);

    } // testSortuniqueannotationsbyidSetInternalError()

// protected function dispatchElements($elements) 

    public function testDispatchelementsGeneratesTableList() {

		$annotations = array( 'tableName' => 'annotations' );
		$relations = array( 'tableName' => 'relations' );
		$lemmas = array( 'tableName' => 'lemmas' );
		$attributes = array( 'tableName' => 'attributes' );
		$elements = array(
			"annotations" => $annotations,
			"relations" => $relations,
			"lemmas" => $lemmas,
			"attributes" => $attributes
		);

        // reflection for access to private elements
        $protectedMethod = new ReflectionMethod('CorpusExporter','dispatchElements');
        $protectedMethod->setAccessible(True);
        $ce = new CorpusExporter();
        $export_errorsPrivateProperty = new ReflectionProperty($ce,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($ce,array($elements));

		$expectedTableList = [$annotations,$relations,$lemmas,$attributes];
		$this->assertEquals($expectedTableList,$result);

    } // testDispatchelementsGeneratesTableList()

// protected function generateCcl($report,$tokens,$tags_by_tokens) 

    public function testGeneratecclReturnsObjectofCclDocumentClass() {

		$report_id = 12;
		$report = array( 'id'=>$report_id );
		$tokens = array();
		$tags_by_tokens = array();
	
		$ce = new CorpusExporter();
		$protectedMethod = $this->createAccessToProtectedMethodOfClassObject($ce,'generateCcl');
		$result=$protectedMethod->invokeArgs($ce,array($report,$tokens,$tags_by_tokens));

		// result is object of CclDocument class
		$this->assertInstanceOf('CclDocument',$result);
		// Ccl->fileName is set to name coresponding to report_id
		$expectedFileName = str_pad($report_id,8,'0',STR_PAD_LEFT);
		$this->assertEquals($expectedFileName,$result->getFilename());

    } // testGeneratecclReturnsObjectofCclDocumentClass() 

    public function testGeneratecclOnExceptionReturnsFalse() {

        $report_id = 13; 
        $report = array();
        $tokens = array();
        $tags_by_tokens = array();

		// mocking for throw exception emulate
        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('callCclCreator'))
            -> getMock();
        $mockCorpusExporter -> method('callCclCreator')
			-> will($this->throwException(new Exception()));         

        $protectedMethod = $this->createAccessToProtectedMethodOfClassObject($mockCorpusExporter,'generateCcl');
        $export_errorsPrivateProperty = new ReflectionProperty($mockCorpusExporter,'export_errors');
        $export_errorsPrivateProperty->setAccessible(True);

        $result=$protectedMethod->invokeArgs($mockCorpusExporter,array($report,$tokens,$tags_by_tokens));

        // result should be false
        $this->assertFalse($result);
        // internal errors table
        $internalErrorsTable = $export_errorsPrivateProperty->getValue($mockCorpusExporter);
        $expectedErrorsCount = 1;
        $this->assertEquals($expectedErrorsCount,count($internalErrorsTable));
        $expectedErrorsKey = "Problem z utworzeniem CCL";
        $this->assertEquals($expectedErrorsKey,$internalErrorsTable[2]['message']);

    } // testGeneratecclOnBadDataReturnsFalse()

// protected function updateExtractorStats($extractorName,$extractor_stats,$extractor_elements) 

	public function testUpdateextractorstatsUpdatesStatsTable() {

        $extractorName = 'nazwa_extraktora';
		$existentType 	= 'typ istniejący';
		$existentCount	= 219;
        $newType    = 'niestniejacy typ';
        $arrWith2Items = array( 3,4 );
		$extractor_stats = array(
            $extractorName => array(
			    $existentType => $existentCount 
            )
		);
		$extractor_elements = array(
	        $newType => $arrWith2Items,
            $existentType => $arrWith2Items
		);	

        $ce = new CorpusExporter();
        $protectedMethod = $this->createAccessToProtectedMethodOfClassObject($ce,'updateExtractorStats');
        $result=$protectedMethod->invokeArgs($ce,array($extractorName,$extractor_stats,$extractor_elements));

		$expectedStats = array(
			$extractorName => array(
                $existentType => $existentCount + 2,
                $newType => 2 // count of $arrWith2Items 
            )
		);
		$this->assertEquals($expectedStats,$result);

	} // testUpdateextractorstatsUpdatesStatsTable()

// protected function runExtractor($flags,$report_id,$extractor,&$elements,&$extractor_stats) 

    public function testRunextractorsReturnsChangedElementsAndStats() {

        $report_id = 13;
        $annotation1 = array( "id"=>17, "other_field"=>"sth" );
        $flagId = -1;
        $flagName = 'xxx'; $flagIds = [$flagId,0,1];
        $flags = array( $flagName=>$flagId );
        $extractorName = "exName";
        $extractorFunc = function ($report_id, $params, &$elements) {
                $elements["annotations"]=array(
                    array( "id"=>17, "other_field"=>"sth" )  // $annotation1
                );
        };
        $extractor = array( 
            "name"      =>  $extractorName,
            "flag_name" =>  $flagName,
            "flag_ids"  =>  $flagIds,
            "extractor" =>  $extractorFunc,
            "params"    => array()
        ); 
        $elements = array(
            "annotations"   => array(),
            "relations"     => array(),
            "lemmas"        => array(),
            "attributes"    => array(),
        );
        $extractor_stats = array();

        // mocking for throw exception emulate
        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array('updateExtractorStats'))
            -> getMock();
        $expectedName = $extractorName;
        $expectedStats = array();
        $expectedElements = array(
            "annotations"   => array($annotation1),
            "relations"     => array(),
            "lemmas"        => array(),
            "attributes"    => array(),
        );
        $returnedStats = array(
            $extractorName  => array("annotations"=>1,"relations"=>0,"lemmas"=>0,"attributes"=>0)
        );

        $mockCorpusExporter -> expects($this->once())
			-> method('updateExtractorStats')
            -> with($expectedName,$expectedStats,$expectedElements)
            -> will($this->returnValue($returnedStats));

        $protectedMethod = $this->createAccessToProtectedMethodOfClassObject($mockCorpusExporter,'runExtractor');

        $protectedMethod->invokeArgs($mockCorpusExporter,array($flags,$report_id,$extractor,&$elements,&$extractor_stats));

        $expectedAnnotationsElements = array($annotation1);
        $this->assertEquals($expectedAnnotationsElements,$elements["annotations"]);
        $expectedStats = $returnedStats;
        $this->assertEquals($expectedStats,$extractor_stats);

    } // testRunextractorsReturnsChangedElementsAndStats()
    
} // CorpusExporter_part10_Test class

?>
