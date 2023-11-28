<?php

mb_internal_encoding("UTF-8");

class CclTokenTest extends PHPUnit_Framework_TestCase
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

// function setAnnotationLemma($annotation_lemma){...}

    public function testNullLemmaSetsPropForLemmaSuffixToNull() {

        $annotation_lemma = null;

        $token = new CclToken();
        $result = $token->setAnnotationLemma($annotation_lemma);

        $this->assertTrue($result); // always returns True
        $expectedPropKey = ":lemma";
        $this->assertNull($token->prop[$expectedPropKey]);

    } // testEmptyLemmaSetsPropForLemmaSuffixToNull()

    public function testEmptyLemmaSetsPropForLemmaSuffixToNull() {

        $annotation_lemma = array();

        $token = new CclToken();
        $result = $token->setAnnotationLemma($annotation_lemma);

        $this->assertTrue($result); // always returns True
        $expectedPropKey = ":lemma";
        $this->assertNull($token->prop[$expectedPropKey]);

    } // testEmptyLemmaSetsPropForLemmaSuffixToNull()

    public function testLemmaWithoutTypeSetsPropForLemmaSuffix() {

        $lemmaText = "LEMMA";
        $annotation_lemma = array( 'lemma'=>$lemmaText );

        $token = new CclToken();
        $result = $token->setAnnotationLemma($annotation_lemma);

        $this->assertTrue($result); // always returns True
        $expectedPropKey = ":lemma";
        $expectedLemma = $lemmaText;
        $this->assertEquals($expectedLemma,$token->prop[$expectedPropKey]);

    } // testLemmaWithoutTypeSetsPropForLemmaSuffix()

    public function testLemmaWithoutLemmaTextSetsPropWithLemmaToNull() {

        $lemmaType = "TYP"; 
        $annotation_lemma = array( 'type'=>$lemmaType );

        $token = new CclToken();
        $result = $token->setAnnotationLemma($annotation_lemma);

        $this->assertTrue($result); // always returns True
        $expectedPropKey = $lemmaType.":lemma";
        $this->assertNull($token->prop[$expectedPropKey]);

    } // testLemmaWithoutLemmaTextSetsPropWithLemmaToNull()

    public function testSetAnnotationLemmaSetsPropWithLemmaSuffix() {

        $lemmaType = "TYP"; $lemmaText = "LEMMA";
        $annotation_lemma = array( 'type'=>$lemmaType, 'lemma'=>$lemmaText );
        
        $token = new CclToken();
        $result = $token->setAnnotationLemma($annotation_lemma);

        $this->assertTrue($result); // always returns True
        $expectedPropKey = $lemmaType.":lemma";
        $expectedLemma = $lemmaText;
        $this->assertEquals($expectedLemma,$token->prop[$expectedPropKey]);

    } // testSetAnnotationLemmaSetsPropWithLemmaSuffix()

// function setAnnotation($annotation,$parentChannels = null)

    public function testSetannotationWithoutTypeFieldWorksAsNonSense() {

        $id=10;
        $annotation = array( 'id'=>$id, 'value'=>array(1,2,3));
        $parentChannels = null;

        $token = new CclToken();
        $result = $token->setAnnotation($annotation,$parentChannels);
        // should return True
        $this->assertTrue($result);
        // in tabel token.channels key "" should be used if type not exist
        $expectedTypeChannel = $id; 
        $this->assertEquals($expectedTypeChannel,$token->channels[""]);
        // for other types than 'sense' there are not prop attribute
        $this->assertNull($token->prop);

    } // testSetannotationWithoutTypeFieldWorksAsNonSense()

    public function testSetannotationIfChannelForTypeExistsPreserveExistingAndReturnsFalse() {

        $type = 'TYP'; $id=10; $existingIdForChannel = 17;
        $annotation = array('type'=>$type, 'id'=>$id, 'value'=>array(1,2,3));
        $parentChannels = null;

        $token = new CclToken();
		$token->channels[$type] = $existingIdForChannel;
        $result = $token->setAnnotation($annotation,$parentChannels);
        // should return False in this case
        $this->assertFalse($result);
        // in tabel token.channels should be [$type]=>$annotation.id
        $expectedTypeChannel = $existingIdForChannel; // preserve existing
        $this->assertEquals($expectedTypeChannel,$token->channels[$type]);
        // for other types than 'sense' there are not prop attribute
        $this->assertNull($token->prop);

    } // testSetannotationIfChannelForTypeExistsPreserveExistingAndReturnsFalse() 

    public function testParentChannelsWithoutTypeKeyBlocksAdding() {

        $type = 'TYP'; $id=10;
        $annotation = array('type'=>$type, 'id'=>$id, 'value'=>array(1,2,3));
        $parentChannels = array( );

        $token = new CclToken();
        $result = $token->setAnnotation($annotation,$parentChannels);
        // should return False if parent channels for type doesn't exists
        $this->assertFalse($result);
        // table token.channels remains empty
        $this->assertEquals(array(),$token->channels);
        // for other types than 'sense' there are not prop attribute
        $this->assertNull($token->prop);

    } // testParentChannelsWithoutTypeKeyBlocksAdding()

    public function testParentChannelsWithTypeKeyAddsAnnotationLocally() {

        $type = 'TYP'; $id=10;
        $annotation = array('type'=>$type, 'id'=>$id, 'value'=>array(1,2,3));
        $parentChannels = array( $type=>0 );

        $token = new CclToken();
        $result = $token->setAnnotation($annotation,$parentChannels);
        // should return True if add locally
        $this->assertTrue($result);
        // in tabel token.channels should be [$type]=>$annotation.id
        $expectedTypeChannel = $id;
        $this->assertEquals($expectedTypeChannel,$token->channels[$type]);
        // for other types than 'sense' there are not prop attribute
        $this->assertNull($token->prop);

    } // testParentChannelsWithTypeKeyAddsAnnotationLocally()

    public function testSenseTypeWhenExistsValueWithSameCountReturnsFalse() {

        $type = 'sense'; $id=10;
        $annotation = array('type'=>$type, 'id'=>$id, 'value'=>array(1,2,3));
        $parentChannels = null;
		$existingProp = array(1,1,1); // 3 times as in 'value'

        $token = new CclToken();
		$token->prop = $existingProp;
        $result = $token->setAnnotation($annotation,$parentChannels);
        // should returs False for this case
        $this->assertFalse($result);
        // table token.channels remains empty
        $this->assertEquals(array(),$token->channels);
        // token.prop remains as was
        $this->assertEquals($existingProp,$token->prop);

    } // testSenseTypeWhenExistsValueWithSameCountReturnsFalse() 

    public function testSenseTypeWhenExistsValueWithLessCountReplaceProp() {

        $type = 'sense'; $id=10;
        $annotation = array('type'=>$type, 'id'=>$id, 'value'=>array(1,2,3));
        $parentChannels = null;
		$existingProp = array(1,1); // 2 times less as in 'value'

        $token = new CclToken();
		$token->prop = $existingProp;
        $result = $token->setAnnotation($annotation,$parentChannels);
        // should return True
        $this->assertTrue($result);
        // in tabel token.channels should be [$type]=>$annotation.id
        $expectedTypeChannel = $id;
        $this->assertEquals($expectedTypeChannel,$token->channels[$type]);
        // for 'sense' type prop is set to value
        $this->assertEquals($annotation['value'],$token->prop);

    } // testSenseTypeWhenExistsValueWithLessCountReplaceProp()


    public function testSetannotationWithFullDataSetsChannels() {

        $type = 'TYP'; $id=10;
        $annotation = array('type'=>$type, 'id'=>$id, 'value'=>array(1,2,3));
        $parentChannels = null;

        $token = new CclToken();
        $result = $token->setAnnotation($annotation,$parentChannels);
        // should return True
        $this->assertTrue($result);
        // in tabel token.channels should be [$type]=>$annotation.id 
        $expectedTypeChannel = $id;
        $this->assertEquals($expectedTypeChannel,$token->channels[$type]);
        // for other types than 'sense' there are not prop attribute
        $this->assertNull($token->prop);

    } // testSetannotationWithFullDataSetsChannels()

    public function testSetannotationWithSenseTypeSetsPropTable() {

        $type = 'sense'; $id=10;
        $annotation = array('type'=>$type, 'id'=>$id, 'value'=>array(1,2,3));
        $parentChannels = null;

        $token = new CclToken();
        $result = $token->setAnnotation($annotation,$parentChannels);
        // should return True
        $this->assertTrue($result);
        // in tabel token.channels should be [$type]=>$annotation.id
        $expectedTypeChannel = $id;
        $this->assertEquals($expectedTypeChannel,$token->channels[$type]);
        // for 'sense' type prop is set to value
        $this->assertEquals($annotation['value'],$token->prop);

    } // testSetannotationWithSenseTypeSetsPropTable()

} // CclTokenTest class
