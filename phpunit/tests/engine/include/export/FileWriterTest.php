<?php

mb_internal_encoding("UTF-8");

class FileWriterTest extends PHPUnit_Framework_TestCase {

    private $virtualDir = null;

    protected function setUp() {

        $this->virtualDir = org\bovigo\vfs\vfsStream::setup('root',null,[]);

    } // setUp()

    public function test_writeTextToFile() {

		$fileName = $this->virtualDir->url()."/test.txt";
		$text = "jnduie773nd n";
		$fw = new FileWriter();
		$fw->writeTextToFile($fileName,$text);
		$result = file_get_contents($fileName);
        $this->assertEquals($text,$result);

    } // test_writeTextToFile()

    public function test_writeJSONToFile() {

		$fileName = $this->virtualDir->url()."/test.txt";
        $jsonArray = array('a' => 1);
        $fw = new FileWriter();
        $fw->writeJSONToFile($fileName,$jsonArray);
        $result = file_get_contents($fileName);
        $expected =
'{
    "a": 1
}';
        $this->assertEquals($expected,$result);

    } // test_writeJSONToFile

} // FileWriterTest class

?>
