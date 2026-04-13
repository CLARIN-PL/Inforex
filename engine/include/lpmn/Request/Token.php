<?php

namespace Inforex\Lpmn\Request;

class Token
{
    /** @var string */
    private $head;

    /** @var string */
    private $body;

    public function __construct($head, $body)
    {
        $this->head = $head;
        $this->body = $body;
    }

    public function head()
    {
        return $this->head;
    }

    public function body()
    {
        return $this->body;
    }
}
