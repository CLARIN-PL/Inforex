<?php

class AnnotationContextsExportDatabaseStub extends Database {
    public $reports;
    public $annotations;
    public $reportReads = 0;
    public $filters;

    public function __construct($reports, $annotations) {
        $this->reports = $reports;
        $this->annotations = $annotations;
    }
    public function fetch_rows($sql, $args = null) {
        $this->filters = $args;
        return $this->annotations;
    }
    public function fetch($sql, $args = null) {
        $this->reportReads++;
        return $this->reports[$args[0]];
    }
    public function fetch_one($sql, $args = null) {
        $this->reportReads++;
        return $this->reports[$args[0]]['content'];
    }
}

/** @runTestsInSeparateProcesses */
class AnnotationContextsExportTest extends PHPUnit_Framework_TestCase {
    private function export($reports, $annotations, $filters = array()) {
        $GLOBALS['db'] = new AnnotationContextsExportDatabaseStub($reports, $annotations);
        $GLOBALS['corpus'] = array('id' => 246);
        $_GET = $filters;
        $page = (new ReflectionClass(Page_corpus_annotation_contexts_export::class))->newInstanceWithoutConstructor();
        $page->template = new Smarty();
        $page->execute();
        $this->assertSame(0, $page->get('interupted'));
        $page->template->compile_dir = sys_get_temp_dir();
        $csv = $page->template->fetch(__DIR__ . '/../../../../engine/templates/page_corpus_annotation_contexts_export.tpl');
        $lines = explode("\n", trim($csv));
        $this->assertSame('sep=, ', $lines[0]);
        $this->assertCount(8, str_getcsv($lines[1], ',', '"', ''));
        return array_map(function($line) { return str_getcsv($line, ',', '"', ''); }, array_slice($lines, 2));
    }
    private function annotation($id, $reportId, $from, $to, $text) {
        return array('id'=>$id, 'report_id'=>$reportId, 'from'=>$from, 'to'=>$to,
            'text'=>$text, 'type'=>'example', 'stage'=>'final', 'source'=>'user');
    }

    public function test_export_all_handles_boundaries_and_caches_each_report() {
        $rows = $this->export(array(10=>array('content'=>'alpha beta', 'format'=>'plain')),
            array($this->annotation(1,10,0,4,'alpha'), $this->annotation(2,10,5,8,'beta')));
        $this->assertSame(array('10','1','example','','alpha','beta','final','user'), $rows[0]);
        $this->assertSame(array('10','2','example','alpha','beta','','final','user'), $rows[1]);
        $this->assertSame(1, $GLOBALS['db']->reportReads);
        $this->assertSame(array(246), $GLOBALS['db']->filters);
    }

    public function test_plain_text_angle_brackets_and_entities_keep_their_offsets() {
        $rows = $this->export(array(10=>array('content'=>'<tag> A &amp; marker "B", & koniec', 'format'=>'plain')),
            array($this->annotation(1,10,11,16,'marker')),
            array('annotation_type_id'=>'7','annotation_orth'=>'marker','annotation_lemma'=>'lemma','annotation_stage'=>'final'));
        $this->assertSame('<tag> A &amp;', $rows[0][3]);
        $this->assertSame('marker', $rows[0][4]);
        $this->assertSame('"B", & koniec', $rows[0][5]);
        $this->assertSame(array(246,7,'marker','lemma','final'), $GLOBALS['db']->filters);
    }

    public function test_xml_context_is_exported_as_decoded_text() {
        $rows = $this->export(array(10=>array('content'=>'<p>A &amp; <b>marker</b> B&#039;s &lt;3</p>', 'format'=>'xml')),
            array($this->annotation(1,10,2,7,'marker')));
        $this->assertSame('A &', $rows[0][3]);
        $this->assertSame("B's <3", $rows[0][5]);
    }

    public function test_stale_offsets_and_empty_documents_do_not_abort_the_csv() {
        $rows = $this->export(array(10=>array('content'=>'short', 'format'=>'plain'),11=>array('content'=>'', 'format'=>'plain')),
            array($this->annotation(1,10,6528,6530,'old'),$this->annotation(2,11,0,2,'old')));
        $this->assertCount(2, $rows);
        $this->assertSame('', $rows[0][3]);
        $this->assertSame('', $rows[0][5]);
        $this->assertSame('old', $rows[0][4]);
        $this->assertSame('', $rows[1][3]);
        $this->assertSame('', $rows[1][5]);
    }

    public function test_empty_result_still_has_a_csv_header() {
        $this->assertSame(array(), $this->export(array(), array()));
    }
}
