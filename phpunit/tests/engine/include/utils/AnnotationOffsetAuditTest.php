<?php

require_once __DIR__ . '/../../../../../local/lib/AnnotationOffsetAudit.php';

class AnnotationOffsetAuditTest extends PHPUnit_Framework_TestCase {
    private function report($text, $format = 'plain') {
        return array('id' => 1, 'corpora' => 2, 'content' => $text, 'format' => $format);
    }

    private function annotation($id, $from, $to, $text) {
        return array('id' => $id, 'from' => $from, 'to' => $to, 'text' => $text);
    }

    public function test_repairs_a_span_after_and_across_the_lost_character_and_is_idempotent() {
        $report = $this->report('A ¯\\_(ツ)_/¯ słowo koniec');
        $annotations = array(
            $this->annotation(1, 0, 0, 'A'),
            $this->annotation(2, 1, 8, '¯_(ツ)_/¯'),
            $this->annotation(3, 9, 13, 'słowo'),
            $this->annotation(4, 10, 14, 'słowo') // Already uses raw coordinates.
        );
        $plan = AnnotationOffsetAudit::plan($report, $annotations);
        $this->assertSame(array(), $plan['unresolved']);
        $this->assertCount(2, $plan['changes']);
        $this->assertSame(array('from' => 1, 'to' => 9, 'text' => '¯\\_(ツ)_/¯'), $plan['changes'][0]['after']);
        $this->assertSame(array('from' => 10, 'to' => 14, 'text' => 'słowo'), $plan['changes'][1]['after']);
        foreach ($plan['changes'] as $change) {
            foreach ($annotations as &$a) {
                if ($a['id'] === $change['id']) $a = array_merge($a, $change['after']);
            }
            unset($a);
        }
        $this->assertSame($plan['annotations_after_sha256'], AnnotationOffsetAudit::annotationHash($annotations));
        $this->assertSame(array(), AnnotationOffsetAudit::plan($report, $annotations)['changes']);
    }

    public function test_does_not_guess_for_unrelated_mismatch_or_invalid_bounds() {
        $plan = AnnotationOffsetAudit::plan($this->report('A\\ B B'), array(
            $this->annotation(1, 0, 0, 'B'),
            $this->annotation(2, 99, 100, 'B'),
            $this->annotation(3, -1, 1, 'AB')
        ));
        $this->assertSame(array(), $plan['changes']);
        $this->assertSame(array(1, 2, 3), $plan['unresolved']);
    }

    public function test_handles_consecutive_slashes_unicode_and_xml_entities() {
        $plan = AnnotationOffsetAudit::plan($this->report('<p>😀 \\\\ &amp; <b>żółć</b></p>', 'xml'), array(
            $this->annotation(1, 3, 6, 'żółć')
        ));
        $this->assertSame(array(), $plan['unresolved']);
        $this->assertSame(array('from' => 4, 'to' => 7, 'text' => 'żółć'), $plan['changes'][0]['after']);
    }

    public function test_escaped_zero_is_excluded_from_automatic_repair() {
        $plan = AnnotationOffsetAudit::plan($this->report('A\\0 B'), array($this->annotation(1, 1, 1, 'B')));
        $this->assertSame(array(), $plan['changes']);
        $this->assertSame(array(1), $plan['unresolved']);
    }
}
