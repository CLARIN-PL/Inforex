<?php

namespace Inforex\Lpmn\Http;

class MultipartStreamBuilder
{
    /**
     * @param array<string, string> $fields
     * @param array<string, string> $files
     * @return array{0:string,1:string}
     */
    public static function build(array $fields, array $files)
    {
        $boundary = '--------------------------' . md5((string) microtime(true));
        $body = '';

        foreach ($fields as $name => $value) {
            $body .= '--' . $boundary . "\r\n";
            $body .= 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n";
            $body .= $value . "\r\n";
        }

        foreach ($files as $name => $filePath) {
            $body .= '--' . $boundary . "\r\n";
            $body .= 'Content-Disposition: form-data; name="' . $name . '"; filename="' . basename($filePath) . '"' . "\r\n";
            $body .= "Content-Type: application/octet-stream\r\n\r\n";
            $body .= file_get_contents($filePath) . "\r\n";
        }

        $body .= '--' . $boundary . "--\r\n";

        return array('multipart/form-data; boundary=' . $boundary, $body);
    }
}
