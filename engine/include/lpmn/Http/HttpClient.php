<?php

namespace Inforex\Lpmn\Http;

use RuntimeException;

class HttpClient
{
    /** @var int */
    private $timeout;

    public function __construct($timeout = 1200)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * @param array<string, string> $headers
     */
    public function request($method, $url, array $headers = array(), $body = null)
    {
        $headerLines = array();
        foreach ($headers as $name => $value) {
            $headerLines[] = $name . ': ' . $value;
        }

        $context = stream_context_create(array(
            'http' => array(
                'method' => strtoupper($method),
                'header' => implode("\r\n", $headerLines),
                'content' => $body === null ? '' : $body,
                'ignore_errors' => true,
                'timeout' => $this->timeout,
            ),
        ));

        $responseBody = @file_get_contents($url, false, $context);
        if ($responseBody === false && empty($http_response_header)) {
            throw new RuntimeException('HTTP request failed for URL: ' . $url);
        }

        return new HttpResponse(
            $this->parseStatusCode(isset($http_response_header) ? $http_response_header : array()),
            $responseBody === false ? '' : $responseBody,
            $this->parseHeaders(isset($http_response_header) ? $http_response_header : array())
        );
    }

    /**
     * @param array<int, string> $responseHeaders
     * @return array<string, string>
     */
    private function parseHeaders(array $responseHeaders)
    {
        $headers = array();
        foreach ($responseHeaders as $index => $line) {
            if ($index === 0 || strpos($line, ':') === false) {
                continue;
            }

            list($name, $value) = explode(':', $line, 2);
            $headers[trim($name)] = trim($value);
        }

        return $headers;
    }

    /**
     * @param array<int, string> $responseHeaders
     */
    private function parseStatusCode(array $responseHeaders)
    {
        if (empty($responseHeaders)) {
            return 0;
        }

        if (preg_match('/HTTP\/\S+\s+(\d{3})/', $responseHeaders[0], $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
