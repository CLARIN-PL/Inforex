<?php

class PerspectiveAgreementTest extends PHPUnit_Framework_TestCase
{
    /** @dataProvider emptySelections */
    public function testOpensWithoutSelectedAnnotators($cookies)
    {
        $previousDb = $GLOBALS['db'] ?? null;
        $previousCorpus = $GLOBALS['corpus'] ?? null;
        $previousCookies = $_COOKIE;
        $previousPost = $_POST;
        $db = $this->getMockBuilder(DatabaseEmulator::class)->onlyMethods(array('fetch_rows'))->getMock();
        $db->method('fetch_rows')->willReturn(array());
        $GLOBALS['db'] = $db;
        $GLOBALS['corpus'] = array('id' => 158);
        $_COOKIE = $cookies;
        $_POST = array();
        try {
            $document = array('id' => 1, 'corpora' => 158, 'format' => 'plain', 'content' => 'Test document.');
            $page = (new ReflectionClass(Page_report::class))->newInstanceWithoutConstructor();
            $page->template = new Smarty();
            $page->report = $document;
            (new PerspectiveAgreement($page, $document))->execute();

            $this->assertSame(array(), $page->get('groups'));
            $this->assertSame(array(), $page->get('errors'));
            $this->assertSame(array(), $page->get('annotations'));
            $this->assertSame(0, $page->get('annotator_a_id'));
            $this->assertSame(0, $page->get('annotator_b_id'));
            $this->assertSame('Test document.', $page->get('content_inline'));
        } finally {
            $GLOBALS['db'] = $previousDb;
            $GLOBALS['corpus'] = $previousCorpus;
            $_COOKIE = $previousCookies;
            $_POST = $previousPost;
        }
    }

    public static function emptySelections()
    {
        return array(
            'no saved selection' => array(array()),
            'saved empty selection' => array(array(
                'agreement_annotations_158_annotator_id_a' => '0',
                'agreement_annotations_158_annotator_id_b' => '0',
                '158_annotation_lemma_types' => ''
            ))
        );
    }
}
