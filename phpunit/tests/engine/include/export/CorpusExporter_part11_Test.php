<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class CorpusExporter_part11_Test extends PHPUnit_Framework_TestCase
{
    // tests with lemma fields sets    

    private $virtualDir = null;

// protected function runExtractor($flags,$report_id,$extractor,&$elements,&$extractor_stats) 

    // 2a
    public function testRunextractorsForExtractorCustomAnnotationsSetsLemmaFieldsInElementsAnnotationsSection() {

        $report_id = 13;
        // annotacja z pustym lematem, z wykonania LEFT JOIN
        $annotation1 = array( "id"=>1, "orth"=>"nam_adj", "lemma"=>NULL );
        // annotacja z niepustym lematem
        $annotation2 = array( "id"=>2, "orth"=>"nam_orth", "lemma"=>"lemat1" );
        $flagId = -1;
        $flagName = 'xxx'; $flagIds = [$flagId,0,1];
        $flags = array( $flagName=>$flagId );
        $extractorName = "exName";
        $extractorFunc = function ($report_id, $params, &$elements) {
                $elements["annotations"]=array(
                    // annotation 1
                    array( "id"=>1, "orth"=>"nam_adj", "lemma"=>NULL ),
                    // annotation 2
                    array( "id"=>2, "orth"=>"nam_orth", "lemma"=>"lemat1" )
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
            "annotations"   => array($annotation1,$annotation2),
            "relations"     => array(),
            "lemmas"        => array(),
            "attributes"    => array(),
        );
        $returnedStats = array(
            $extractorName  => array("annotations"=>2,"relations"=>0,"lemmas"=>0,"attributes"=>0)
        );

        $mockCorpusExporter -> expects($this->once())
			-> method('updateExtractorStats')
            -> with($expectedName,$expectedStats,$expectedElements)
            -> will($this->returnValue($returnedStats));

        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCorpusExporter,'runExtractor');
        $protectedMethod->invokeArgs($mockCorpusExporter,array($flags,$report_id,$extractor,&$elements,&$extractor_stats));

        $expectedElements = array(
            "annotations" => array( $annotation1, $annotation2 ),
            "relations"=>[], "lemmas"=>[], "attributes"=>[]
        );
        $this->assertEquals($expectedElements,$elements);
        $expectedStats = $returnedStats;
        $this->assertEquals($expectedStats,$extractor_stats);

    } // testRunextractorsForExtractorCustomAnnotationsSetsLemmaFieldsInElementsAnnotationsSection()
    
    // 2b
    public function testRunextractorsForExtractorCustomLemmasSetsLemmaFieldsInElementsAnnotationsAndLemmasSection() {

        $report_id = 13;
        // annotacja z pustym lematem, z wykonania LEFT JOIN
        $annotation1 = array( "id"=>1, "orth"=>"nam_adj", "lemma"=>NULL );
        // annotacja z niepustym lematem
        $annotation2 = array( "id"=>2, "orth"=>"nam_orth", "lemma"=>"lemat1" );
        $lemma2 = array( "report_annotation_id" => "2", "lemma" => "lemat1", "id"=>2 );
        $flagId = -1;
        $flagName = 'xxx'; $flagIds = [$flagId,0,1];
        $flags = array( $flagName=>$flagId );
        $extractorName = "exName";
        $extractorFunc = function ($report_id, $params, &$elements) {
                $elements["annotations"]=array(
                    // annotation 1
                    array( "id"=>1, "orth"=>"nam_adj", "lemma"=>NULL ),
                    // annotation 2
                    array( "id"=>2, "orth"=>"nam_orth", "lemma"=>"lemat1" )
                );
                $elements["lemmas"]=array(
                    // lemma2
                    array( "report_annotation_id" => "2", "lemma" => "lemat1", "id"=>2 )
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
            "annotations"   => array($annotation1,$annotation2),
            "relations"     => array(),
            "lemmas"        => array($lemma2),
            "attributes"    => array(),
        );
        $returnedStats = array(
            $extractorName  => array("annotations"=>2,"relations"=>0,"lemmas"=>1,"attributes"=>0)
        );

        $mockCorpusExporter -> expects($this->once())
            -> method('updateExtractorStats')
            -> with($expectedName,$expectedStats,$expectedElements)
            -> will($this->returnValue($returnedStats));

        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCorpusExporter,'runExtractor');
        $protectedMethod->invokeArgs($mockCorpusExporter,array($flags,$report_id,$extractor,&$elements,&$extractor_stats));

        $expectedElements = array(
            "annotations" => array( $annotation1, $annotation2 ),
            "relations"=>[], 
			"lemmas"=>array($lemma2), 
			"attributes"=>[]
        );
        $this->assertEquals($expectedElements,$elements);
        $expectedStats = $returnedStats;
        $this->assertEquals($expectedStats,$extractor_stats);

    } // testRunextractorsForExtractorCustomLemmasSetsLemmaFieldsInElementsAnnotationsAndLemmasSection() 

    // 3
    public function testRunextractorsForExtractorStandardLemmasSetsLemmaFieldsInElementsLemmasSection() {

        $report_id = 13;
        $lemma2 = array( "report_annotation_id" => "2", "lemma" => "lemat1", "id"=>2 );
        $flagId = -1;
        $flagName = 'xxx'; $flagIds = [$flagId,0,1];
        $flags = array( $flagName=>$flagId );
        $extractorName = "exName";
        $extractorFunc = function ($report_id, $params, &$elements) {
                $elements["lemmas"]=array(
                    // lemma2
                    array( "report_annotation_id" => "2", "lemma" => "lemat1", "id"=>2 )
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
            "annotations"   => array(),
            "relations"     => array(),
            "lemmas"        => array($lemma2),
            "attributes"    => array(),
        );
        $returnedStats = array(
            $extractorName  => array("annotations"=>0,"relations"=>0,"lemmas"=>1,"attributes"=>0)
        );

        $mockCorpusExporter -> expects($this->once())
            -> method('updateExtractorStats')
            -> with($expectedName,$expectedStats,$expectedElements)
            -> will($this->returnValue($returnedStats));

        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCorpusExporter,'runExtractor');
        $protectedMethod->invokeArgs($mockCorpusExporter,array($flags,$report_id,$extractor,&$elements,&$extractor_stats));

        $expectedElements = array(
            "annotations" => [],
            "relations"=>[],
            "lemmas"=>array($lemma2),
            "attributes"=>[]
        );
        $this->assertEquals($expectedElements,$elements);
        $expectedStats = $returnedStats;
        $this->assertEquals($expectedStats,$extractor_stats);

    } // testRunextractorsForExtractorStandardLemmasSetsLemmaFieldsInElementsLemmasSection() 

} // CorpusExporter_part11_Test class

?>
