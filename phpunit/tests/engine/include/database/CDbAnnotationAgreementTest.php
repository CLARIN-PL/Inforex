<?php

class CDbAnnotationAgreementTest extends PHPUnit_Framework_TestCase
{
    private function annotation($id, $user, $type, $stage = 'agreement', $from = 0, $to = 3)
    {
        return array('id' => $id, 'user_id' => $user, 'type_id' => $type,
            'type' => 'type_' . $type, 'stage' => $stage,
            'from' => $from, 'to' => $to, 'text' => 'test');
    }

    private function group($annotations)
    {
        return DbAnnotation::groupAnnotationsByRanges($annotations, 1, 2, array(
            array('annotation_type_id' => 10, 'name' => 'type_10'),
            array('annotation_type_id' => 20, 'name' => 'type_20')
        ));
    }

    public function testEmptySelectionFromReportedError()
    {
        $this->assertSame(array(), DbAnnotation::groupAnnotationsByRanges(array(), 0, 0, array()));
    }

    public function testMatchingAnnotationsWithoutFinalDecision()
    {
        $groups = $this->group(array($this->annotation(1, 1, 10), $this->annotation(2, 2, 10)));
        $group = $groups['0:3'];
        $this->assertCount(1, $group['all_annotations']);
        $this->assertSame('a_and_b', $group['all_annotations'][0]['agreement']);
        $this->assertCount(1, $group['a_and_b']);
        $this->assertTrue($group['available_annotation_types'][0]['checked']);
        $this->assertEmpty($group['all_final'] ?? false);
        $this->assertNull($group['final']);
    }

    public function testDifferentTypesRetainTheirAnnotator()
    {
        $group = $this->group(array($this->annotation(1, 1, 10), $this->annotation(2, 2, 20)))['0:3'];
        $this->assertSame(array(10 => 'only_a', 20 => 'only_b'),
            array_column($group['all_annotations'], 'agreement', 'type_id'));
        $this->assertEmpty($group['a_and_b'] ?? array());
        $this->assertEmpty($group['available_annotation_types'][0]['checked'] ?? false);
        $this->assertEmpty($group['available_annotation_types'][1]['checked'] ?? false);
    }

    public function testPartialFinalDecisionLeavesRemainingConsensusPending()
    {
        $group = $this->group(array(
            $this->annotation(1, 1, 10), $this->annotation(2, 2, 10),
            $this->annotation(3, 1, 20), $this->annotation(4, 2, 20),
            $this->annotation(5, 3, 10, 'final')
        ))['0:3'];
        $annotations = array_column($group['all_annotations'], null, 'type_id');
        $this->assertTrue($annotations[10]['final']);
        $this->assertEmpty($annotations[20]['final'] ?? false);
        $this->assertSame(array(20), array_column($group['a_and_b'], 'type_id'));
        $this->assertSame(5, $group['final'][0]['annotation_id']);
        $this->assertEmpty($group['all_final'] ?? false);
        $this->assertEmpty($group['available_annotation_types'][0]['checked'] ?? false);
        $this->assertTrue($group['available_annotation_types'][1]['checked']);
    }

    public function testFinalConsensusIsNotProposedAgain()
    {
        $group = $this->group(array(
            $this->annotation(1, 1, 10), $this->annotation(2, 2, 10),
            $this->annotation(3, 3, 10, 'final')
        ))['0:3'];
        $this->assertTrue($group['all_final']);
        $this->assertSame(array(), $group['a_and_b']);
        $this->assertTrue($group['user1'][0]['final']);
        $this->assertTrue($group['user2'][0]['final']);
    }

    public function testSeparateRangesAndFinalOnlyAnnotationsArePreserved()
    {
        $groups = $this->group(array(
            $this->annotation(1, 1, 10),
            $this->annotation(2, 2, 20, 'agreement', 10, 13),
            $this->annotation(3, 3, 10, 'final', 20, 23),
            $this->annotation(4, 3, 20, 'agreement', 30, 33)
        ));
        $this->assertSame(array('0:3', '10:13', '20:23'), array_keys($groups));
        $this->assertSame('only_a', $groups['0:3']['all_annotations'][0]['agreement']);
        $this->assertSame('only_b', $groups['10:13']['all_annotations'][0]['agreement']);
        $this->assertSame(array(), $groups['20:23']['all_annotations']);
        $this->assertSame(3, $groups['20:23']['final'][0]['annotation_id']);
    }

    /** @dataProvider distributionMethods */
    public function testDistributionQueriesCanBeCalledStatically($method, $rows)
    {
        $previousDb = $GLOBALS['db'] ?? null;
        $db = $this->getMockBuilder(DatabaseEmulator::class)->onlyMethods(array('fetch_rows'))->getMock();
        $db->expects($this->once())->method('fetch_rows')
            ->with($this->stringContains('WHERE r.corpora = ?'), array(158))->willReturn($rows);
        $GLOBALS['db'] = $db;
        try {
            $this->assertSame($rows, DbAnnotation::$method(158));
        } finally {
            $GLOBALS['db'] = $previousDb;
        }
    }

    public static function distributionMethods()
    {
        return array(
            'types' => array('getAnnotationByTypeCount', array(array('annotation_type_id' => 10, 'name' => 'type_10', 'c' => 2))),
            'stages' => array('getAnnotationByStageCount', array(array('stage' => 'agreement', 'c' => 2))),
            'sets' => array('getAnnotationBySetCount', array(array('annotation_set_id' => 1, 'name' => 'test', 'c' => 2)))
        );
    }
}
