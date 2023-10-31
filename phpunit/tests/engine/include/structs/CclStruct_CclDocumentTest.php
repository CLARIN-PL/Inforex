<?php

mb_internal_encoding("UTF-8");

class CclDocument_Test extends PHPUnit_Framework_TestCase
{

    public function test_addToken_set_char2token_array() {

        $from = 1; $to = 3;
        $t = new CclToken();
        $t->setFrom($from); $t->setTo($to);
        $d = new CclDocument();
        $d->addToken($t);

        // on empty document token index starts from 0
        $expectedTokenIndex = 0;  
        // first token in token list is $t
        $this->assertEquals($t,$d->tokens[$expectedTokenIndex]);
        // all token chars has same token index
        for($i=$from;$i<=$to;$i++) {
            $this->assertEquals($expectedTokenIndex,$d->char2token[$i]);
        }

    } // test_addToken_set_char2token_array()

/*
function setAnnotationProperty($annotation_property){
*/
    public function test_setAnnotationProperty_setInternalErrorOnNull() {

        $annotation_property = null;        

        $cclDocument = new CclDocument();
        $cclDocument->setAnnotationProperty($annotation_property);

        // error message
        $this->assertTrue(is_array($cclDocument->errors));
        $this->assertTrue(count($cclDocument->errors)>0);
        $this->assertInstanceOf('CclError',$cclDocument->errors[0]);

    } // test_setAnnotationProperty_setInternalErrorOnNull() 

    public function test_setAnnotationProperty_setInternalErrorOnEmptyArray() {

        $annotation_property = array();

        $cclDocument = new CclDocument();
        $cclDocument->setAnnotationProperty($annotation_property);

        // error message
        $this->assertTrue(is_array($cclDocument->errors));
        $this->assertTrue(count($cclDocument->errors)>0);
        $this->assertInstanceOf('CclError',$cclDocument->errors[0]);

    } // test_setAnnotationProperty_setInternalErrorOnEmptyArray()

    public function test_setAnnotationProperty() {

        $type = 1;
        $from = 1; $to = 3;
        $name = 'nazwa własności';
        $value = 'wartość własności';
        $annotation_property = array(
            "type" => $type,
            "from" => $from,
            "to" => $to,
            "name" => $name,
            "value" => $value
        );

        // document must have valid cclToken for $from to $to chars 
        $t = new CclToken();
        $t->setFrom($from); $t->setTo($to);        
        $cclDocument = new CclDocument();
        // must set token to document to add property for this range
        $cclDocument->addToken($t);
        $cclDocument->setAnnotationProperty($annotation_property);

        // no error message
        $this->assertTrue(is_array($cclDocument->errors));
        $this->assertEquals(count($cclDocument->errors),0);
        // added property should be written to token prop table
        $expectedPropTable = array(
            $type.':'.$name => $value
        );
        $this->assertEquals($expectedPropTable,$cclDocument->tokens[0]->prop);

    } // test_setAnnotationProperty_setInternalErrorOnEmptyArray()

//  function setAnnotationLemma($annotation_lemma){...}

    public function testAddLemmaWithNullParameterSetsError() {

        $annotation_lemma = null;
        $ccl = new CclDocument();
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(1,count($ccl->errors)); // is sth in errors table
        $this->assertInstanceOf('CclError',$ccl->errors[0]); // is this object
        // verify message content
        $expectedErrorMsg = array("Annotation out of range (annotation.from > document.char_count)");
        $this->assertEquals($expectedErrorMsg,$ccl->errors[0]->comments);

	} // testAddLemmaWithNullParameterSetsError()

    public function testAddLemmaWithEmptyParameterSetsError() {

        $annotation_lemma = array(); // should have 'from' and 'to' field
        $ccl = new CclDocument();
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(1,count($ccl->errors)); // is sth in errors table
        $this->assertInstanceOf('CclError',$ccl->errors[0]); // is this object
        // verify message content
        $expectedErrorMsg = array("Annotation out of range (annotation.from > document.char_count)");
        $this->assertEquals($expectedErrorMsg,$ccl->errors[0]->comments);

    } // testAddLemmaWithEmptyParameterSetsError()

    public function testLemmaWithoutFromSetsError() {

        $from = 1; $to = 3;
        $annotation_lemma = array('to'=>$to,'type'=>'TYP');

        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])                                           -> getMock();
        // $token->setAnnotationLemma will not be called in this case
        $mockToken->expects($this->never())->method('setAnnotationLemma');
        // this methods are originally, sets token range
        $mockToken->setFrom($from); $mockToken->setTo($to);

        $ccl = new CclDocument();
        $ccl->addToken($mockToken);
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(1,count($ccl->errors)); // is sth in errors table
        $this->assertInstanceOf('CclError',$ccl->errors[0]); // is this object
        // verify message content
        $expectedErrorMsg = array("Annotation out of range (annotation.from > document.char_count)");
        $this->assertEquals($expectedErrorMsg,$ccl->errors[0]->comments);

	} // testLemmaWithoutFromSetsError()

    public function testLemmaWithoutFromMatchesAnyTokenSetsError() {

        $from = 1; $to = 3;
        $annotation_lemma = array('from'=>$from-1,'to'=>$to,'type'=>'TYP');

        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])
            -> getMock();
        // $token->setAnnotationLemma will not be called in this case
        $mockToken->expects($this->never())->method('setAnnotationLemma');
        // this methods are originally, sets token range
        $mockToken->setFrom($from); $mockToken->setTo($to);

        $ccl = new CclDocument();
        $ccl->addToken($mockToken);
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(1,count($ccl->errors)); // is sth in errors table
        $this->assertInstanceOf('CclError',$ccl->errors[0]); // is this object
        // verify message content
        $expectedErrorMsg = array("Annotation out of range (annotation.from > document.char_count)");
        $this->assertEquals($expectedErrorMsg,$ccl->errors[0]->comments);

	} // testLemmaWithoutFromMatchesAnyTokenSetsError() 

    public function testLemmaWithoutToSetsError() {

        $from = 1; $to = 3;
        $annotation_lemma = array('from'=>$from,'type'=>'TYP');

        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])
            -> getMock();
        // $token->setAnnotationLemma will not be called in this case
        $mockToken->expects($this->never())->method('setAnnotationLemma');
        // this methods are originally, sets token range
        $mockToken->setFrom($from); $mockToken->setTo($to);

        $ccl = new CclDocument();
        $ccl->addToken($mockToken);
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(1,count($ccl->errors)); // is sth in errors table
        $this->assertInstanceOf('CclError',$ccl->errors[0]); // is this object
        // verify message content
        $expectedErrorMsg = array("Annotation out of range (annotation.to > document.char_count)");
        $this->assertEquals($expectedErrorMsg,$ccl->errors[0]->comments);

	} // testLemmaWithoutToSetsError

    public function testLemmaWithoutToMatchesAnyTokenSetsError() {

        $from = 1; $to = 3;
        $annotation_lemma = array('from'=>$from,'to'=>$to+1,'type'=>'TYP');

        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])
            -> getMock();
        // $token->setAnnotationLemma will not be called in this case
        $mockToken->expects($this->never())->method('setAnnotationLemma');
        // this methods are originally, sets token range
        $mockToken->setFrom($from); $mockToken->setTo($to);

        $ccl = new CclDocument();
        $ccl->addToken($mockToken);
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(1,count($ccl->errors)); // is sth in errors table
        $this->assertInstanceOf('CclError',$ccl->errors[0]); // is this object
        // verify message content
        $expectedErrorMsg = array("Annotation out of range (annotation.to > document.char_count)");
        $this->assertEquals($expectedErrorMsg,$ccl->errors[0]->comments);

    } // testLemmaWithoutToMatchesAnyTokenSetsError()

    public function testLemmaWithoutTypeCallTokenSetLemmaMethodProperly() {

        $from = 1; $to = 3;
        $annotation_lemma = array('from'=>$from,'to'=>$to);

        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])
            -> getMock();
        // $token->setAnnotationLemma will be called exactly once
        $mockToken->expects($this->once())->will($this->returnValue(True))->method('setAnnotationLemma');
        // this methods are originally, sets token range
        $mockToken->setFrom($from); $mockToken->setTo($to);

        $ccl = new CclDocument();
        $ccl->addToken($mockToken);
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(0,count($ccl->errors)); // no errors

    } // testLemmaWithoutTypeCallTokenSetLemmaMethodProperly()

    public function testSetAnnotationLemmaCallTokenSetLemmaMethod() {

		$from = 1; $to = 3;
		$annotation_lemma = array('from'=>$from,'to'=>$to,'type'=>'TYP');
 
        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])
            -> getMock();
        // $token->setAnnotationLemma will be called exactly once
        $mockToken->expects($this->once())->will($this->returnValue(True))->method('setAnnotationLemma');
        // this methods are originally, sets token range
        $mockToken->setFrom($from); $mockToken->setTo($to);

        $ccl = new CclDocument();
        $ccl->addToken($mockToken);
		$ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(0,count($ccl->errors)); // no errors 

    } // testSetAnnotationLemmaCallTokenSetLemmaMethod()

    public function testTokenSetLemmaReturnFalseSetsError() {

        $from = 1; $to = 3;
        $annotation_lemma = array('from'=>$from,'to'=>$to,'type'=>'TYP');

        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])
            -> getMock();
        // $token->setAnnotationLemma will be called exactly once
        $mockToken->expects($this->once())->will($this->returnValue(False))->method('setAnnotationLemma');
        // this methods are originally, sets token range
        $mockToken->setFrom($from); $mockToken->setTo($to);

        $ccl = new CclDocument();
        $ccl->addToken($mockToken);
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(1,count($ccl->errors)); // is sth in errors table
        $this->assertInstanceOf('CclError',$ccl->errors[0]); // is this object
        // verify message content
        $expectedErrorMsg = array("000 cannot set annotation lemma to specific token");
        $this->assertEquals($expectedErrorMsg,$ccl->errors[0]->comments);

    } // testTokenSetLemmaReturnFalseSetsError()

    public function testLemmaOverMultiTokensCallSetLemmaForFirstOneOnly() {

        $from = 1; $to = 5;
        $annotation_lemma = array('from'=>$from,'to'=>$to,'type'=>'TYP');

        $mockToken1 = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])
            -> getMock();
        // $token->setAnnotationLemma this should will be called exactly once
        $mockToken1->expects($this->once())->will($this->returnValue(True))->method('setAnnotationLemma');
        $mockToken1->setFrom($from); $mockToken1->setTo(3);
        $mockToken2 = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotationLemma'])
            -> getMock();
        // $token->setAnnotationLemma this shouldn't never be called
        $mockToken2->expects($this->never())->method('setAnnotationLemma');
        $mockToken2->setFrom(4); $mockToken2->setTo($to);


        $ccl = new CclDocument();
        $ccl->addToken($mockToken1); $ccl->addToken($mockToken2);
        $ccl->setAnnotationLemma($annotation_lemma);
        $this->assertEquals(0,count($ccl->errors)); // no errors

    } // testLemmaOverMultiTokensCallSetLemmaForFirstOneOnly()

// function setAnnotation($annotation)

    public function testSetannotationForProperDataCallsTokensSetannotation() {

        $from = 1; $to = 3;
		$annotation = array( 'type'=>"TYP", 'from'=>1, 'to'=>3, 'value'=>'VALUE', 'name'=>'NAME' );

        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['setAnnotation'])
            -> getMock();
        // $token->setAnnotation will be called exactly once returns True
        $mockToken->expects($this->once())->will($this->returnValue(True))->method('setAnnotation');
        // this methods are originally, sets token range
        $mockToken->setFrom($from); $mockToken->setTo($to);
		$mockSentence = $this->getMockBuilder(CclSentence::class)
            -> setMethods(['incChannel','fillChannel'])
            -> getMock();
        // incChannel() should be called once on argument $annotation['type']
        $mockSentence->expects($this->once())->method('incChannel')->with($annotation['type']);
        // fillChannel() should be called once on argument $annotation['type']
        $mockSentence->expects($this->once())->method('fillChannel')->with($annotation['type']);
		$mockSentence->channels = array();
		$mockDocument = $this->getMockBuilder(CclDocument::class)
            -> setMethods(['getSentenceByToken'])
            -> getMock();
        $mockDocument->expects($this->exactly(2))->will($this->returnValue($mockSentence))->method('getSentenceByToken');
        $mockDocument->addToken($mockToken);

        $expectedTokenProp = array(
            "sense:".$annotation["name"]=>$annotation['value']
        );
        $mockDocument->setAnnotation($annotation);
        $this->assertEquals(0,count($ccl->errors)); // no errors
        // annotation.value is direct set to prop table in token
        $this->assertEquals($expectedTokenProp,$mockToken->prop);

    } // testSetannotationForProperDataCallsTokensSetannotation()

} // class
