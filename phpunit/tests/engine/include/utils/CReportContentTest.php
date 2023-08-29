<?php

mb_internal_encoding("UTF-8");

class CReportContentTest extends PHPUnit_Framework_TestCase
{
    private $testContent = "jakiś tekst z tagami <a href='test'>Test</a>";

    public function test_getHtmlStr_returnsHtmlStr2Object_onEmptyArray() {

        $testReport = array();
        $result = ReportContent::getHtmlStr($testReport);
        $this->assertInstanceOf('HtmlStr2',$result);
        
    } // test_getHtmlStr_returnsHtmlStr2Object_onEmptyArray() 

    public function test_getHtmlStr_returnsHtmlStr2Object_onArrayWoFormat() {

        $testReport = array("content"=>$this->testContent);       
        $result = ReportContent::getHtmlStr($testReport);
        $this->assertInstanceOf('HtmlStr2',$result);

    } // test_getHtmlStr_returnsHtmlStr2Object_onArrayWoFormat()

    public function test_getHtmlStr_returnsHtmlStr2Object()
    {

        $testReport = array("content"=>$this->testContent);

        $testReport['format'] = 'unknown';
        $result = ReportContent::getHtmlStr($testReport);
        $this->assertInstanceOf('HtmlStr2',$result);

        $testReport['format'] = 'plain';
        $result = ReportContent::getHtmlStr($testReport);
        $this->assertInstanceOf('HtmlStr2',$result);

    } // test_getHtmlStr_returnsHtmlStr2Object

    public function test_getHtmlStr_returnsHtmlStr2_withFormatedContent() {

        $testReport = array("content"=>$this->testContent);

        $testReport['format'] = 'unknown';
        $result = ReportContent::getHtmlStr($testReport);
        // not changed
        $this->assertEquals($this->testContent,$result->getContent());

        $testReport['format'] = 'plain';
        $result = ReportContent::getHtmlStr($testReport);
        // expected converted html tags and entities
        $expectedContent = "jakiś tekst z tagami &lt;a href='test'&gt;Test&lt;/a&gt;";
        $this->assertEquals($expectedContent,$result->getContent());

    } // test_getHtmlStr_returnsHtmlStr2_withFormatedContent()

} // class

?>
