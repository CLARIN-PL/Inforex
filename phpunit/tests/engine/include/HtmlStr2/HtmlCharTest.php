<?php 

final class HtmlCharTest extends PHPUnit_Framework_TestCase {

    public function testCanBeCreatedFromValidUTF8Char()
    {
        $this->assertInstanceOf(
            "HtmlChar",
            new HtmlChar('ą')
        );
    } 

    public function testCanBeUsedAsString()
    {
        $testChar = 'ą';
        $this->assertEquals(
            $testChar,
            (new HtmlChar($testChar))->toString()
        );
    }

}

?>
