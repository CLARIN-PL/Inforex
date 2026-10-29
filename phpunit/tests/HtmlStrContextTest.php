<?php

class HtmlStrContextTest extends PHPUnit_Framework_TestCase {
    public static function ranges() {
        return array(
            'left edge' => array('alpha beta gamma', -50, 4, true, false, 'alpha'),
            'right edge' => array('alpha beta gamma', 9, 60, false, true, 'gamma'),
            'whole document' => array('alpha beta gamma', -50, 60, true, true, 'alpha beta gamma'),
            'before first character' => array('alpha beta gamma', -50, -1, true, false, ''),
            'after last character' => array('alpha beta gamma', 14, 64, false, true, ''),
            'reported out-of-range context' => array('alpha beta gamma', 6478, 6527, true, false, ''),
            'empty document' => array('', -50, 50, true, true, ''),
            'whitespace only' => array(" \t\n", 0, 50, true, true, ''),
            'reversed range' => array('alpha beta gamma', 9, 4, true, true, ''),
            'single character at right edge' => array('a', 0, 0, true, true, 'a'),
            'whole words' => array('alpha beta gamma', 2, 11, true, true, 'alpha beta gamma'),
            'unaligned substring' => array('alpha beta gamma', 2, 11, false, false, 'pha beta gam'),
            'Unicode code points' => array('żółć 😀 koniec', 4, 4, true, true, '😀'),
            'XML context' => array('<p>alpha <b>beta</b> gamma</p>', 0, 13, false, false, 'alpha beta gamma')
        );
    }

    /** @dataProvider ranges */
    public function test_context_is_the_intersection_with_the_document($content, $from, $to, $alignLeft, $alignRight, $expected) {
        $html = new HtmlStr2($content);
        $this->assertSame($expected, $html->getTextAlign($from, $to, $alignLeft, $alignRight));
    }

    public function test_keep_tags_preserves_internal_markup() {
        $html = new HtmlStr2('<p>alpha <b>beta</b> gamma</p>');
        $this->assertSame('alpha <b>beta</b> gamma', $html->getTextAlign(-10, 50, false, false, true));
    }

    public function test_trailing_whitespace_is_not_a_visible_character() {
        $html = new HtmlStr2('alpha beta   ');
        $this->assertSame('beta', $html->getTextAlign(5, 8, false, true));
        $this->assertSame('', $html->getTextAlign(9, 59, false, true));
    }
}
