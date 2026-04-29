<?php

use Inforex\Lpmn\Result\JsonlMerger;

class JsonlMergerTest extends PHPUnit_Framework_TestCase
{
    public function testLeavesRegularJsonUntouched()
    {
        $json = '{"text":"Ala","tokens":{"default":[{"id":"1","start":0,"stop":3}]}}';

        $this->assertSame($json, JsonlMerger::mergeIfNeeded($json));
    }

    public function testMergesJsonlChunksAndShiftsOffsets()
    {
        $jsonl = implode("\n", array(
            json_encode(array(
                'id' => 'chunk-1',
                'text' => 'Ala ma ',
                'tokens' => array(
                    'default' => array(
                        array('id' => 't1', 'start' => 0, 'stop' => 3, 'lexemes' => array()),
                        array('id' => 't2', 'start' => 4, 'stop' => 6, 'lexemes' => array()),
                    ),
                ),
                'spans' => array(
                    'sentence' => array(
                        array('id' => 's1', 'start' => 0, 'stop' => 6),
                    ),
                ),
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            json_encode(array(
                'id' => 'chunk-2',
                'text' => 'kota.',
                'tokens' => array(
                    'default' => array(
                        array('id' => 't3', 'start' => 0, 'stop' => 4, 'lexemes' => array()),
                        array('id' => 't4', 'start' => 4, 'stop' => 5, 'lexemes' => array()),
                    ),
                ),
                'spans' => array(
                    'sentence' => array(
                        array('id' => 's2', 'start' => 0, 'stop' => 5),
                    ),
                ),
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ));

        $merged = json_decode(JsonlMerger::mergeIfNeeded($jsonl), true);

        $this->assertSame('Ala ma kota.', $merged['text']);
        $this->assertCount(4, $merged['tokens']['default']);
        $this->assertSame(7, $merged['tokens']['default'][2]['start']);
        $this->assertSame(11, $merged['tokens']['default'][2]['stop']);
        $this->assertSame(11, $merged['tokens']['default'][3]['start']);
        $this->assertSame(12, $merged['tokens']['default'][3]['stop']);
        $this->assertSame(7, $merged['spans']['sentence'][1]['start']);
        $this->assertSame(12, $merged['spans']['sentence'][1]['stop']);
    }
}
