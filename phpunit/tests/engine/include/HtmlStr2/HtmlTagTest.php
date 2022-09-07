<?php

final class HtmlTagTest extends PHPUnit_Framework_TestCase {

    private static $testTagName = 'TAG';
    private static $testTagStr = '<TAG attr="attr"/>';
    private static $testTagType = null; // initialize later

    /**
     * @before
     */
    protected function initializeEachTest() {
        self::$testTagType = HtmlTag::$HTML_TAG_SELF_CLOSE;
    }

    public function testCanBeCreatedFromValidData()
    {

        $this->assertInstanceOf(
            "HtmlTag",
            new HtmlTag( self::$testTagName,
                         self::$testTagType,
                         self::$testTagStr
                        )
        );
    } 

    public function testCanBeUsedAsString()
    {
        $this->assertEquals(
            self::$testTagStr,
            (new HtmlTag(self::$testTagName,
                         self::$testTagType,
                         self::$testTagStr
            ))->toString()
        );
    }

    public function testCanReturnName()
    {
        $this->assertEquals(
            self::$testTagName,
            (new HtmlTag(self::$testTagName,
                         self::$testTagType,
                         self::$testTagStr
            ))->getName()
        );    
    }

    public function testCanReturnType()
    {
        $this->assertEquals(
            self::$testTagType,
            (new HtmlTag(self::$testTagName,
                         self::$testTagType,
                         self::$testTagStr
            ))->getType()
        );
    }

}

?>
