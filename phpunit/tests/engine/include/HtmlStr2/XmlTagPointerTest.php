<?php

final class XmlTagPointerTest extends PHPUnit_Framework_TestCase {

    private static $testTagName = 'TAG';
    private static $testTagType = null; // initialize later
    private static $testTagStr  = '<TAG attr="attr"/>';
    private static $testTag = null;
    private static $testIndex = 2;

    /**
     * @before
     */
    protected function readTag() 
    {
        self::$testTagType = HtmlTag::$HTML_TAG_SELF_CLOSE;
        self::$testTag = new HtmlTag( self::$testTagName,
                         self::$testTagType,
                         self::$testTagStr
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
            self::$testTagStr,
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
        $x->setIndex(self::$testIndex);
        $this->assertEquals(
            self::$testIndex,
            $x->getIndex()
        );
    }

}

?>
