<?php

mb_internal_encoding("UTF-8");

class CclToken_Test extends PHPUnit_Framework_TestCase
{

    public function test_createObjectOfCclTokenClass() {
        $this->assertInstanceOf('CclToken',new CclToken());
    } // test_createObjectOfCclTokenClass()

    public function test_From_GetWhatWasSet() {

        $from = 1;
        $t = new CclToken();
        $t->setFrom($from);
        $result = $t->getFrom();
        $expectedFrom = $from; // what was set is what we get
        $this->assertEquals($expectedFrom,$result);

    } // test_From_GetWhatWasSet()

    public function test_To_GetWhatWasSet() {

        $to = 1;
        $t = new CclToken();
        $t->setTo($to);
        $result = $t->getTo();
        $expectedTo = $to; // what was set is what we get
        $this->assertEquals($expectedTo,$result);

    } // test_To_GetWhatWasSet()

    public function test_setAnnotationProperty() {

        $type = 1;  // ID
        $name = 'nazwa';  // string
        $value = 'wartość';
        // for this function $annotation_property must have 
        // minimal fields: 'type', 'name', 'value'
        $annotation_property = array(
            "type" => $type,
            "name" => $name,
            "value" => $value
        );

        // do test
        $t = new CclToken(); // $t->prop is null here
        $result = $t->setAnnotationProperty($annotation_property);
        
        // setAnnotationProperty returns always True
        $this->assertTrue($result);
        // there are no method to get setting property, we must examine
        // internal table prop[]
        $this->assertTrue(is_array($t->prop));
        $expectedNumerOfProperties = 1;
        $this->assertEquals($expectedNumerOfProperties,count($t->prop));
        $expectedPropTable = array(
            $type.':'.$name => $value
        );
        $this->assertEquals($expectedPropTable,$t->prop);
 
    }

} // class
