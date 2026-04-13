<?php

namespace Inforex\Lpmn\Http;

class HttpResponse
{
    /** @var int */
    private $statusCode;

    /** @var string */
    private $body;

    /** @var array<string, string> */
    private $headers;

    /**
     * @param array<string, string> $headers
     */
    public function __construct($statusCode, $body, array $headers = array())
    {
        $this->statusCode = (int) $statusCode;
        $this->body = (string) $body;
        $this->headers = $headers;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
