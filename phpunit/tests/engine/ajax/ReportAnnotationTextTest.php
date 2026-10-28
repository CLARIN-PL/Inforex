<?php

require_once __DIR__ . '/../../../../engine/ajax/ajax_report_add_annotation.php';
require_once __DIR__ . '/../../../../engine/ajax/ajax_report_update_annotation.php';

class ReportAnnotationTextDatabaseStub extends Database {
    public $report;
    public $inserted = array();

    public function __construct() {}

    public function fetch($sql, $args = null) {
        return $this->report;
    }

    public function insert($table, $values) {
        $this->inserted[] = array($table, $values);
    }

    public function last_id() {
        return 123;
    }
}

class ReportAnnotationTextTest extends PHPUnit_Framework_TestCase {
    private $oldPost;
    private $oldDb;
    private $oldUser;

    protected function setUp(): void {
        $this->oldPost = $_POST;
        $this->oldDb = $GLOBALS['db'] ?? null;
        $this->oldUser = $GLOBALS['user'] ?? null;
        $GLOBALS['db'] = new ReportAnnotationTextDatabaseStub();
        $GLOBALS['user'] = array('user_id' => 1);
        $_POST = array(
            'type' => 'emotion', 'annotation_type_id' => '1',
            'report_id' => '10', 'context' => '', 'stage' => 'agreement'
        );
    }

    protected function tearDown(): void {
        $_POST = $this->oldPost;
        $GLOBALS['db'] = $this->oldDb;
        $GLOBALS['user'] = $this->oldUser;
    }

    private function action($class) {
        return (new ReflectionClass($class))->newInstanceWithoutConstructor();
    }

    public static function textCases() {
        return array(
            'reported music fragment' => array('plain', "co do wyboru miejsc: z relacji z ostatnich tras gn'r wynika, że nagłośnienie bywa różne."),
            'reported autumn fragment' => array('plain', "Life is Strange grałem pierwszy raz właśnie jesienią i to był strzał w 10, do dziś mi się kojarzy z tym vibe'em."),
            'apostrophes at boundaries' => array('plain', "'jesień'"),
            'quotes and ampersand' => array('plain', '"Rock & roll" <koncert>'),
            'literal entity text stays literal' => array('plain', "&apos; &#039; &amp; &lt; '"),
            'Unicode and emoji' => array('plain', "Żółć 😀 i muzyka z lat '90 — jesień 🍂"),
            'XML with apostrophe' => array('xml', "<p>Koncert <b>gn'r</b> &amp; jesień.</p>")
        );
    }

    /** @dataProvider textCases */
    public function test_add_and_update_validate_the_text_displayed_by_the_annotator($format, $fragment) {
        // A nonzero offset and trailing text ensure we validate the selected span only.
        $prefix = 'Wstęp 😀. ';
        $GLOBALS['db']->report = array('format' => $format, 'content' => $prefix . $fragment . ' Koniec.');
        $from = mb_strlen(preg_replace('/\s/u', '', $prefix), 'UTF-8');
        $text = html_entity_decode(strip_tags($format === 'plain'
            ? htmlspecialchars($fragment, ENT_COMPAT | ENT_HTML401, 'UTF-8') : $fragment), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $to = $from + mb_strlen(preg_replace('/\s/u', '', $text), 'UTF-8') - 1;
        $_POST += array('from' => $from, 'to' => $to, 'text' => $text);

        $result = $this->action(Ajax_report_add_annotation::class)->execute();
        $this->assertSame(1, $result['success']);
        $this->assertSame($text, $result['text']);
        $this->assertCount(1, $GLOBALS['db']->inserted);
        $saved = $GLOBALS['db']->inserted[0][1];
        $this->assertSame($from, $saved['from']);
        $this->assertSame($to, $saved['to']);
        $this->assertSame($text, $saved['text']);
        $this->assertSame('agreement', $saved['stage']);

        $this->action(Ajax_report_update_annotation::class)->validateText(
            $GLOBALS['db']->report, $text, $from, $to, 1
        );
    }

    public function test_real_text_mismatch_is_rejected_without_an_insert() {
        $GLOBALS['db']->report = array('format' => 'plain', 'content' => "gn'r");
        $_POST += array('from' => 0, 'to' => 3, 'text' => 'gnxr');
        try {
            $this->action(Ajax_report_add_annotation::class)->execute();
            $this->fail('A different phrase must not be saved.');
        } catch (Exception $e) {
            $this->assertStringContainsString('rozbieżność anotacji', $e->getMessage());
            $this->assertSame(array(), $GLOBALS['db']->inserted);
        }
    }

    public function test_incorrect_offsets_are_still_rejected() {
        $this->expectException(GeneralAjaxException::class);
        $this->expectExceptionMessage('rozbieżność anotacji');
        $this->action(Ajax_report_update_annotation::class)->validateText(
            array('format' => 'plain', 'content' => "A gn'r B"), "gn'r", 0, 3, 1
        );
    }
}
