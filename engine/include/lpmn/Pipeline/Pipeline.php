<?php

namespace Inforex\Lpmn\Pipeline;

class Pipeline
{
    /** @var array<int, mixed> */
    private $lpmn;

    /**
     * @param array<int, mixed> $lpmn
     */
    public function __construct(array $lpmn)
    {
        $this->lpmn = array_values($lpmn);
    }

    public static function fromJson($json)
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            throw new \InvalidArgumentException('Invalid pipeline JSON.');
        }

        return new self($decoded);
    }

    /**
     * @return array<int, mixed>
     */
    public function getLpmn()
    {
        return $this->lpmn;
    }

    public function toJson()
    {
        return json_encode($this->lpmn, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function __toString()
    {
        return (string) $this->toJson();
    }
}
