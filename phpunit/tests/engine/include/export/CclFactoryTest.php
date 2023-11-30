<?php

mb_internal_encoding("UTF-8");

class CclFactoryTest extends PHPUnit_Framework_TestCase
{
//    function createFromReportAndTokens(&$report, &$tokens, &$tags){
//   returns object of CclDocument class

    public function test_createFromReportAndTokens_createsValidCclDocumentFromNullParameters() {

        $report = null;
        $tokens = null;
        $tags_by_tokens = null;
        $ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags_by_tokens);
        $this->assertInstanceOf('CclDocument',$ccl);

    } // test_createFromReportAndTokens_createsValidCclDocumentFromNullParameters() 

/*
    function setAnnotationProperties(&$ccl, &$annotation_properties){
*/

    public function test_setAnnotationProperties_failsFor1stParameterIsNull() {
        $ccl = null;

        $annotation_properties = null;
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        $this->assertFalse($result);

        $annotation_properties = array();
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        $this->assertFalse($result);

        /* this one for non-empty 2nd param calls method on null object...
        $annotation_properties = array("id"=>1);
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        $this->assertFalse($result); 
        */
       
    } // test_setAnnotationProperties_failsFor1stParameterIsNull() 


    public function test_setAnnotationProperties_returnsFalseForEmpty2ndParameter() {
        $annotation_properties = null;

        $ccl = null;
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        $this->assertFalse($result);

        $report = null;
        $tokens = null;
        $tags_by_tokens = null;
        $ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags_by_tokens);
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        $this->assertFalse($result);

        $annotation_properties = array(); // empty array as null

        $ccl = null;
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        $this->assertFalse($result);

        $report = null;
        $tokens = null;
        $tags_by_tokens = null;
        $ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags_by_tokens);
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        $this->assertFalse($result);

    } // test_setAnnotationProperties_returnsFalseForEmpty2ndParameter()  

    public function test_setAnnotationProperties_silentlyDoNothingIfNoProperTokenInDocument() {

        $type = 1;
        $from = 1; $to = 3;
        $name = 'nazwa własności';
        $value = 'wartość własności';
        $annotation_property = array( // must have this 5 fields
            "type" => $type,
            "from" => $from,
            "to" => $to,
            "name" => $name,
            "value" => $value
        );
        $annotation_properties = array( $annotation_property );

        // new document w/o tokens
        $ccl = new CclDocument();
        // no tokens added to document - no action will be proceeded

        // do test
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        // returns null like for good results...
        $this->assertNull($result);
        // ...but no token with property added exists
        $expectedTokensTable = array();
        $this->assertEquals($expectedTokensTable,$ccl->tokens);

    } // test_setAnnotationProperties_silentlyDoNothingIfNoProperTokenInDocument() 
 
    public function test_setAnnotationProperties_setPropTableInDocumentToken() {

        $type = 1;
        $from = 1; $to = 3;
        $name = 'nazwa własności';
        $value = 'wartość własności';
        $annotation_property = array( // must have this 5 fields
            "type" => $type,
            "from" => $from,
            "to" => $to,
            "name" => $name,
            "value" => $value
        );
        $annotation_properties = array( $annotation_property );

        // document must have valid cclToken for $from to $to chars
        $t = new CclToken(); // $t->prop is null here
        $t->setFrom($from); $t->setTo($to);
        // must have valid document with this token added
        $ccl = new CclDocument();
        // must set token to document to add property for this range
        $ccl->addToken($t);
 
        // do test
        $result = CclFactory::setAnnotationProperties($ccl,$annotation_properties);
        // returns null for good results...
        $this->assertNull($result);
        // added property should be written to token prop table as below
        $expectedPropTable = array(
            $type.':'.$name => $value
        );
        $this->assertEquals($expectedPropTable,$ccl->tokens[0]->prop);

    } // test_setAnnotationProperties_setPropTableInDocumentToken()

// function setAnnotationLemmas(&$ccl, &$annotation_lemmas)
    
    public function testEmptyLemmasCallsNoAction() {

        $mockCcl = $this->getMockBuilder(CclDocument::class)->getMock();
        // for empty $lemmas there are no internal call
        $annotation_lemmas = array();
        $mockCcl->expects($this->never())->method('setAnnotationLemma');
        // result is returned in $ccl changes
        (new CclFactory())->setAnnotationLemmas($mockCcl,$annotation_lemmas);

    } // testEmptyLemmasCallsNoAction()     

	public function testLemmaIsPassedToCclSetMethod() {

		$mockCcl = $this->getMockBuilder(CclDocument::class)->getMock();
        // $ccl->setAnnotationLemma should be call for each item from
        // $lemmas array
        $annotation_lemmas = array('element');
        $mockCcl->expects($this->once())->method('setAnnotationLemma')->with('element');
        (new CclFactory())->setAnnotationLemmas($mockCcl,$annotation_lemmas);		
	} // testLemmaIsPassedToCclSetMethod()

    public function testAddingLemmaToCclIsRepeatedForEachLemma() {

        $mockCcl = $this->getMockBuilder(CclDocument::class)->getMock();
        // $ccl->setAnnotationLemma should be call for each item from
        // $lemmas array
        $annotation_lemmas = array('raz','dwa','trzy');
		//$mockCcl->expects($this->at(0))->method('setAnnotationLemma')->with('raz');
		//$mockCcl->expects($this->at(1))->method('setAnnotationLemma')->with('dwa');
		//$mockCcl->expects($this->at(2))->method('setAnnotationLemma')->with('trzy');
		// zachowanie kolejności nie jest tu istotne
        $mockCcl->expects($this->exactly(3))->method('setAnnotationLemma');
        (new CclFactory())->setAnnotationLemmas($mockCcl,$annotation_lemmas);

    } // testAddingLemmaToCclIsRepeatedForEachLemma()

// function setAnnotationsAndRelations(&$ccl, &$annotations, &$relations){...}
//   modifies $ccl and returns boolean as operation result

    public function testCorrectDataForSetAnnotationAndRelationCallsCclMethods() {
        $relations = array(
            // continuous type==1
            array( 'relation_type_id'=>1,'source_id'=>1,'target_id'=>2 ),
            // normal
            array( 'relation_type_id'=>2,'source_id'=>3,'target_id'=>4 )
        );
        $annotations = array( // ids includes all source and targets from above
            array( 'id'=>1 ), array( 'id'=>2 ), array( 'id'=>3 ),
            array( 'id'=>4 )
        );

        $mockCcl = $this->getMockBuilder(CclDocument::class)
            -> setMethods(['setAnnotation', 'setContinuousAnnotation2',
                            'addError','setRelation'])
            -> getMock();
        // call setAnnotation() 2 times for noncontinuous annotations
        $mockCcl->expects($this->exactly(2))->method('setAnnotation'); 
        $mockCcl->expects($this->at(0))->method('setAnnotation')->with($annotations[2]);
        $mockCcl->expects($this->at(1))->method('setAnnotation')->with($annotations[3]);
        // call setContinuousAnnotation2() once for continuous annotations pair
        $mockCcl->expects($this->once())->method('setContinuousAnnotation2')->with($annotations[0],$annotations[1]);
		// call setRelation() once for noncontinuous relation only
		//  parametres are: source and target annotations record
        $mockCcl->expects($this->once())->method('setRelation')->with($annotations[2],$annotations[3],$relations[1]);
        // no error set is called
        $mockCcl->expects($this->never())->method('addError');

        $result = (new CclFactory())->setAnnotationsAndRelations($mockCcl,$annotations,$relations);

        // returns true
        $this->assertTrue($result); 
        // no errors in $ccl
        $this->assertEquals(0,count($ccl->errors));

    } // testCorrectDataForSetAnnotationAndRelationCallsCclMethods()

    public function testSetannotationsandrelationsOnEmptyAnnotationsReturnsFalse() {

        $ccl = new CclDocument;
        $annotations = array();
        $relations = array(array("key"=>"value"));
        $result = (new CclFactory())->setAnnotationsAndRelations($ccl,$annotations,$relations);
        $this->assertFalse($result);
        // no errors in $ccl
        $this->assertEquals(0,count($ccl->errors));

    } // testSetannotationsandrelationsOnEmptyAnnotationsReturnsFalse() 

    public function testSetannotationsandrelationsOnAnnotationsWithNoRelationsCallCclsetannotation() {

        $annotation1 = array( "annotation_id"=>1 );
        $annotations = array( $annotation1 );
        $relations = array();

        $mockCcl = $this->getMockBuilder(CclDocument::class)
            -> setMethods(['setAnnotation'])
            -> getMock();
        $mockCcl->expects($this->once())
            ->method('setAnnotation')
            -> with($annotation1);

        $result = (new CclFactory())->setAnnotationsAndRelations($mockCcl,$annotations,$relations);

        $this->assertTrue($result);
        // no errors in $ccl
        $this->assertEquals(0,count($ccl->errors));

    } // testSetannotationsandrelationsOnAnnotationsWithNoRelationsCallCclsetannotation() 


} // CclFactoryTest class

?>
