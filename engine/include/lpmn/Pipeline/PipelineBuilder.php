<?php

namespace Inforex\Lpmn\Pipeline;

class PipelineBuilder
{
    /** @var array<int, mixed> */
    private $json = array();

    public function any2Txt()
    {
        $this->json[] = 'any2txt';

        return $this;
    }

    public function postagger(PosTaggerProperties $properties = null)
    {
        if ($properties === null) {
            $this->json[] = 'postagger';
            return $this;
        }

        $this->json[] = array(
            'postagger' => $properties->getParameters(),
        );

        return $this;
    }

    public function build()
    {
        return new Pipeline($this->json);
    }
}
