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



} // class
