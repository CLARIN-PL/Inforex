<?php

final class XmlTagPointerTest extends PHPUnit_Framework_TestCase {

    const testTagName = 'TAG';
    const testTagType = HTML_TAG_SELF_CLOSE;
    const testTagStr  = '<TAG attr="attr"/>';
    private static $testTag = null;
    const testIndex = 2;

    public static function setUpBeforeClass()
    {
        self::$testTag = new HtmlTag( self::testTagName,
                         self::testTagType,
                         self::testTagStr
                        );
    }

    public function testCanBeCreatedFromValidData()
    {

        $this->assertInstanceOf(
            "XmlTagPointer",
            new XmlTagPointer( self::$testTag )
        );
    } 

    public function testCanBeUsedAsString()
    {
        $this->assertEquals(
            self::testTagStr,
            (new XmlTagPointer(self::$testTag))->toString()
        );
    }

    public function testCanGetTag()
    {
        $this->assertEquals(
            self::$testTag,
            (new XmlTagPointer(self::$testTag))->getTag()
        );
    }

    public function testCanSetAndRestoreIndex()
    {
        $x = new XmlTagPointer(self::$testTag);
        $x->setIndex(self::testIndex);
        $this->assertEquals(
            self::testIndex,
            $x->getIndex()
        );
    }

}

?>
