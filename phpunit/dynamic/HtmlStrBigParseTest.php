<?php

mb_internal_encoding("UTF-8");

final class HtmlStrBigParseTest extends PHPUnit_Framework_TestCase
{

    private static $testURL = "https://nikic.github.io/2015/05/05/Internal-value-representation-in-PHP-7-part-1.html";

    public function testCanBeCreatedFromValidUTF8Html()
    {
    	$text = file_get_contents(self::$testURL);
        $this->assertInstanceOf("HtmlStr2",new HtmlStr2($text));

    } // testCanBeCreatedFromValidUTF8Html

    public function testCanBeRestoredFromInternalRepresentation()
    {
        $text = file_get_contents(self::$testURL);
        $this->assertEquals($text,(new HtmlStr2($text))->getContent());

    } // testCanBeRestoredFromInternalRepresentation

}
?>
