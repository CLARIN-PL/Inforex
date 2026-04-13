<?php

namespace Inforex\Lpmn\Pipeline;

class PosTaggerProperties
{
    /** @var array<string, string> */
    private $params;

    /**
     * @param array<string, string> $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return array<string, string>
     */
    public function getParameters()
    {
        return $this->params;
    }
}
