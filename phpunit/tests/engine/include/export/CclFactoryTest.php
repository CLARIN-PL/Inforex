<?php

mb_internal_encoding("UTF-8");

class CclFactoryTest extends PHPUnit_Framework_TestCase
{
/*
    function createFromReportAndTokens(&$report, &$tokens, &$tags){
   returns object of CclDocument class
*/

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

} // CclFactoryTest class

?>
