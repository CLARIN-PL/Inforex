<?php

mb_internal_encoding("UTF-8");

class CReportContentTest extends PHPUnit_Framework_TestCase
{
    private $testContent = "jakiś tekst z tagami <a href='test'>Test</a>";

    public function test_getHtmlStr_returnsHtmlStr2Object_onEmptyArray() {

        $testReport = array('content'=>null, 'format'=>null);
        $result = ReportContent::getHtmlStr($testReport);
        $this->assertInstanceOf('HtmlStr2',$result);
        
    } // test_getHtmlStr_returnsHtmlStr2Object_onEmptyArray() 

    public function test_getHtmlStr_returnsHtmlStr2Object_onArrayWoFormat() {

        $testReport = array("content"=>$this->testContent, 'format'=>null);       
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

    public function test_database_models_preserve_source_characters_and_annotation_text() {
        $text = '¯\\_(ツ)_/¯ C:\\tmp \\\\ \\0';
        $report = new TableReport();
        $report->assign(array('content' => $text, 'format_id' => DB_REPORT_FORMATS_PLAIN));
        $this->assertSame($text, $report->getContent());
        $this->assertSame($text, ReportContent::getHtmlStrForReport($report)->getContent());

        $annotation = new TableReportAnnotation();
        $annotation->assign(array('text' => $text));
        $this->assertSame($text, $annotation->getText());
        $this->assertSame($text, normalize_content($text));
    }

    public function test_rendering_keeps_backslashes_and_the_selected_span_at_its_stored_offsets() {
        require_once __DIR__ . '/../../../../../engine/external/Smarty-3.1.39/libs/plugins/modifier.format_annotations.php';
        require_once __DIR__ . '/../../../../../engine/ajax/ajax_report_content.php';
        $prefix = '😀 ¯\\_(ツ)_/¯ ';
        $phrase = 'C:\\tmp \\\\';
        $html = ReportContent::getHtmlStr(array('format' => 'plain', 'content' => $prefix . $phrase . ' koniec'));
        $from = mb_strlen(preg_replace('/\s/u', '', $prefix), 'UTF-8');
        $to = $from + mb_strlen(preg_replace('/\s/u', '', $phrase), 'UTF-8');
        $html->insertTag($from, '<an#1:example:1:0>', $to, '</an>');
        $ajax = (new ReflectionClass(Ajax_report_content::class))->newInstanceWithoutConstructor();
        foreach (array(smarty_modifier_format_annotations($html->getContent()), $ajax->format_annotations($html->getContent())) as $rendered) {
            $dom = new DOMDocument();
            $dom->loadHTML('<meta charset="UTF-8"><div>' . $rendered . '</div>');
            $this->assertSame($phrase, $dom->getElementById('an1')->textContent);
            $this->assertSame($prefix . $phrase . ' koniec', $dom->getElementsByTagName('div')->item(0)->textContent);
        }
    }

} // class

?>
