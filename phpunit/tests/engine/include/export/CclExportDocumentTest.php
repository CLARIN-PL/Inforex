<?php

mb_internal_encoding("UTF-8");

class CclExportDocumentTest extends PHPUnit_Framework_TestCase
{
    private $ccl = null;

    protected function setUp() {

        $report = null; $tokens = array(); $tags_by_tokens = null;
        $this->ccl = new CclExportDocument($report, $tokens, $tags_by_tokens);

    } // setUp()

//    function __construct(&$report, &$tokens, &$tags){

    public function test_constructor_createsValidCclExportDocumentFromNullParameters() {

        $this->assertInstanceOf('CclExportDocument',$this->ccl);

    } // test_constructor_createsValidCclExportDocumentFromNullParameters() 

//    function setAnnotationProperties(&$annotation_properties){

    public function test_setAnnotationProperties_failsFor1stParameterIsNull() {

        $annotation_properties = null;
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationProperties');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotation_properties));
        $this->assertFalse($result);

        $annotation_properties = array();
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationProperties');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotation_properties));
        $this->assertFalse($result);

        // this one for non-empty 2nd param calls method on null object...
        $annotation_properties = array("id"=>1);
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationProperties');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotation_properties));
        //$this->assertFalse($result); 
       
    } // test_setAnnotationProperties_failsFor1stParameterIsNull() 


    public function test_setAnnotationProperties_returnsFalseForEmpty2ndParameter() {
        $annotation_properties = null;
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationProperties');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotation_properties));
        $this->assertFalse($result);

        $report = null;
        $tokens = null;
        $tags_by_tokens = null;
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationProperties');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotation_properties));
        $this->assertFalse($result);

        $annotation_properties = array(); // empty array as null
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationProperties');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotation_properties));
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
        // no tokens added to document - no action will be proceeded

        // do test
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationProperties');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotation_properties));
        // returns null like for good results...
        $this->assertNull($result);
        // ...but no token with property added exists
        $expectedTokensTable = array();
        $this->assertEquals($expectedTokensTable,$this->ccl->tokens);

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
        // must set token to document to add property for this range
        $this->ccl->addToken($t);
 
        // do test
        //$result = $this->ccl->setAnnotationProperties($annotation_properties);
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationProperties');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotation_properties));
        // returns null for good results...
        $this->assertNull($result);
        // added property should be written to token prop table as below
        $expectedPropTable = array(
            $type.':'.$name => $value
        );
        $this->assertEquals($expectedPropTable,$this->ccl->tokens[0]->prop);

    } // test_setAnnotationProperties_setPropTableInDocumentToken()

// function setAnnotationLemmas(&$annotation_lemmas)
    
    public function testEmptyLemmasCallsNoAction() {

        $mockCcl = $this->getMockBuilder('CclExportDocument')
            ->disableOriginalConstructor()
            -> setMethods(['setAnnotationLemma'])
            ->getMock();
        // for empty $lemmas there are no internal call
        $annotation_lemmas = array();
        $mockCcl->expects($this->never())->method('setAnnotationLemma');
        // result is returned in $mockCcl changes
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCcl,'setAnnotationLemmas');
        $result = $protectedMethod->invokeArgs($mockCcl,array($annotation_lemmas));
 

    } // testEmptyLemmasCallsNoAction()     

	public function testLemmaIsPassedToCclSetMethod() {

		$mockCcl = $this->getMockBuilder(CclExportDocument::class)
            ->disableOriginalConstructor()
            -> setMethods(['setAnnotationLemma'])
            ->getMock();
        // $mockCcl->setAnnotationLemma should be call for each item from
        // $lemmas array
        $annotation_lemmas = array('element');
        $mockCcl->expects($this->once())->method('setAnnotationLemma')->with('element');
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCcl,'setAnnotationLemmas');
        $result = $protectedMethod->invokeArgs($mockCcl,array($annotation_lemmas));

	} // testLemmaIsPassedToCclSetMethod()

    public function testAddingLemmaToCclIsRepeatedForEachLemma() {

        $mockCcl = $this->getMockBuilder(CclExportDocument::class)
            ->disableOriginalConstructor()
            -> setMethods(['setAnnotationLemma'])
            ->getMock();
        // $mockCcl->setAnnotationLemma should be call for each item from
        // $lemmas array
        $annotation_lemmas = array('raz','dwa','trzy');
		//$mockCcl->expects($this->at(0))->method('setAnnotationLemma')->with('raz');
		//$mockCcl->expects($this->at(1))->method('setAnnotationLemma')->with('dwa');
		//$mockCcl->expects($this->at(2))->method('setAnnotationLemma')->with('trzy');
		// zachowanie kolejności nie jest tu istotne
        $mockCcl->expects($this->exactly(3))->method('setAnnotationLemma');
        // reflection test call for access to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCcl,'setAnnotationLemmas');
        $result = $protectedMethod->invokeArgs($mockCcl,array($annotation_lemmas));

    } // testAddingLemmaToCclIsRepeatedForEachLemma()

// function setAnnotationsAndRelations( &$annotations, &$relations){...}
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

        $mockCcl = $this->getMockBuilder(CclExportDocument::class)
            ->disableOriginalConstructor()
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

        // reflection test call for acces to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCcl,'setAnnotationsAndRelations');
        $result = $protectedMethod->invokeArgs($mockCcl,array(&$annotations,&$relations));

        // returns true
        $this->assertTrue($result); 
        // no errors in $ccl
        $this->assertEquals(0,count($this->ccl->errors));

    } // testCorrectDataForSetAnnotationAndRelationCallsCclMethods()

    public function testSetannotationsandrelationsOnEmptyAnnotationsReturnsFalse() {

        $annotations = array();
        $relations = array(array("key"=>"value"));
        // reflection test call for acces to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($this->ccl,'setAnnotationsAndRelations');
        $result = $protectedMethod->invokeArgs($this->ccl,array(&$annotations,&$relations));

        $this->assertFalse($result);
        // no errors in $ccl
        $this->assertEquals(0,count($this->ccl->errors));

    } // testSetannotationsandrelationsOnEmptyAnnotationsReturnsFalse() 

    public function testSetannotationsandrelationsOnAnnotationsWithNoRelationsCallCclsetannotation() {

        $annotation1 = array( "id"=>1, "annotation_id"=>1 );
        $annotations = array( $annotation1 );
        $relations = array();

        $mockCcl = $this->getMockBuilder(CclExportDocument::class)
            ->disableOriginalConstructor()
            -> setMethods(['setAnnotation'])
            -> getMock();
        $mockCcl->expects($this->once())
            ->method('setAnnotation')
            -> with($annotation1);

        // reflection test call for acces to private elements
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($mockCcl,'setAnnotationsAndRelations');
        $result = $protectedMethod->invokeArgs($mockCcl,array(&$annotations,&$relations));

        $this->assertTrue($result);
        // no errors in $ccl
        $this->assertEquals(0,count($this->ccl->errors));

    } // testSetannotationsandrelationsOnAnnotationsWithNoRelationsCallCclsetannotation() 

// public function setCclProperties(&$annotations, &$relations, $lemmas, $attributes ) 

    public function testSetcclpropertiesCallsAllPropertySetMethodsOnce() {

        $annotations = array('annotacje');
        $relations = array('relacje');
        $lemmas = array('lematy');
        $attributes = array('atrybuty');
        $mockCcl = $this->getMockBuilder(CclExportDocument::class)
            ->disableOriginalConstructor()
            -> setMethods(['setAnnotationsAndRelations','setAnnotationLemmas','setAnnotationProperties'])
            -> getMock();
        $mockCcl->expects($this->once())->method('setAnnotationsAndRelations')
            -> with($annotations,$relations);
        $mockCcl->expects($this->once())->method('setAnnotationLemmas')
            -> with($lemmas);
        $mockCcl->expects($this->once())->method('setAnnotationProperties')
            -> with($attributes);
 
        $mockCcl->setCclProperties($annotations, $relations, $lemmas, $attributes );

    } // testSetcclpropertiesCallsAllPropertySetMethodsOnce() 

} // CclExportDocumentTest class

?>
