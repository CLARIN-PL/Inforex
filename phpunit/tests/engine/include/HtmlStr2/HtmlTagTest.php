<?php

final class HtmlTagTest extends PHPUnit_Framework_TestCase {

    const testTagName = 'TAG';
    const testTagType = HTML_TAG_SELF_CLOSE;
    const testTagStr  = '<TAG attr="attr"/>';

    public function testCanBeCreatedFromValidData()
    {

        $this->assertInstanceOf(
            "HtmlTag",
            new HtmlTag( self::testTagName,
                         self::testTagType,
                         self::testTagStr
                        )
        );
    } 

    public function testCanBeUsedAsString()
    {
        $this->assertEquals(
            self::testTagStr,
            (new HtmlTag(self::testTagName,
                         self::testTagType,
                         self::testTagStr
            ))->toString()
        );
    }

    public function testCanReturnName()
    {
        $this->assertEquals(
            self::testTagName,
            (new HtmlTag(self::testTagName,
                         self::testTagType,
                         self::testTagStr
            ))->getName()
        );    
    }

    public function testCanReturnType()
    {
        $this->assertEquals(
            self::testTagType,
            (new HtmlTag(self::testTagName,
                         self::testTagType,
                         self::testTagStr
            ))->getType()
        );
    }

}

?>
