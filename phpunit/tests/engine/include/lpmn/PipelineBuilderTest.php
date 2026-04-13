<?php

use Inforex\Lpmn\Pipeline\PipelineBuilder;
use Inforex\Lpmn\Pipeline\PosTaggerPropertiesBuilder;
use PHPUnit\Framework\TestCase;

class PipelineBuilderTest extends TestCase
{
    public function testPipelineJsonMatchesExpectedShape()
    {
        $pipeline = (new PipelineBuilder())
            ->any2Txt()
            ->postagger(
                (new PosTaggerPropertiesBuilder())
                    ->methodNer()
                    ->build()
            )
            ->build();

        $this->assertSame('["any2txt",{"postagger":{"method":"ner"}}]', $pipeline->toJson());
        $this->assertSame(
            array(
                'any2txt',
                array(
                    'postagger' => array(
                        'method' => 'ner',
                    ),
                ),
            ),
            $pipeline->getLpmn()
        );
    }
}
