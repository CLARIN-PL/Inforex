<?php

namespace Inforex\Lpmn\Request;

use Inforex\Lpmn\Http\HttpClient;
use Inforex\Lpmn\Http\MultipartStreamBuilder;
use Inforex\Lpmn\Result\JsonlMerger;
use RuntimeException;

class FileRequest
{
    const FILES_ENDPOINT = '/files/';
    const CORPUS_ENDPOINT = '/corpus/';
    const CORPUS_METADATA_ENDPOINT = '/corpus/metadata/';
    const LPMN_CLIENT_DIR = 'lpmn_client_input';
    const UPLOADED_STATUS = 'Uploaded';

    /** @var HttpClient */
    private $client;

    /** @var Token */
    private $token;

    /** @var string */
    private $baseApiUrl;

    /** @var callable|null */
    private $logger;

    public function __construct(HttpClient $client, Token $token, $baseApiUrl)
    {
        $this->client = $client;
        $this->token = $token;
        $this->baseApiUrl = rtrim($baseApiUrl, '/');
        $this->logger = null;
    }

    public function setLogger(callable $logger = null)
    {
        $this->logger = $logger;
    }

    public function uploadFile($filePath)
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new RuntimeException('File does not exist or is not readable: ' . $filePath);
        }

        $remoteFilename = self::LPMN_CLIENT_DIR . '/' . $this->generateUuid();
        $this->log(sprintf('Uploading file: %s as %s', $filePath, $remoteFilename));
        list($contentType, $body) = MultipartStreamBuilder::build(array(), array('f' => $filePath));
        $response = $this->client->request(
            'POST',
            $this->baseApiUrl . self::FILES_ENDPOINT . $remoteFilename,
            array(
                'Content-Type' => $contentType,
                $this->token->head() => $this->token->body(),
            ),
            $body
        );

        $this->log(sprintf('Upload response HTTP %d', $response->getStatusCode()));

        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 201) {
            throw new RuntimeException('File upload failed: ' . $response->getBody());
        }

        $payload = json_decode($response->getBody(), true);
        if (is_array($payload)
            && isset($payload[$remoteFilename])
            && $payload[$remoteFilename] === self::UPLOADED_STATUS) {
            return $remoteFilename;
        }

        throw new RuntimeException('File upload response does not confirm uploaded status: ' . $response->getBody());
    }

    public function downloadFileContent($fileId)
    {
        $this->log(sprintf('Downloading file content for id: %s', $fileId));
        $response = $this->client->request(
            'GET',
            $this->baseApiUrl . self::FILES_ENDPOINT . $this->encodePath($fileId) . '?mode=download',
            array($this->token->head() => $this->token->body())
        );

        $this->log(sprintf('Download response HTTP %d', $response->getStatusCode()));

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('File download failed: ' . $response->getBody());
        }

        return JsonlMerger::mergeIfNeeded($response->getBody());
    }

    public function downloadFile($fileId, $outputPath)
    {
        $content = $this->downloadFileContent($fileId);
        if (file_put_contents($outputPath, $content) === false) {
            throw new RuntimeException('Cannot write file to: ' . $outputPath);
        }
    }

    public function downloadCorpusFile($corpusName, $outputPath)
    {
        $this->downloadByUrl(
            $this->baseApiUrl . self::CORPUS_ENDPOINT . $this->encodePath($corpusName) . '?mode=download',
            $outputPath,
            'corpus'
        );
    }

    public function downloadCorpusMetadataFile($corpusName, $outputPath)
    {
        $this->downloadByUrl(
            $this->baseApiUrl . self::CORPUS_METADATA_ENDPOINT . $this->encodePath($corpusName) . '?mode=excel',
            $outputPath,
            'corpus-metadata'
        );
    }

    public function deleteFile($fileId)
    {
        $this->log(sprintf('Deleting remote file: %s', $fileId));
        $response = $this->client->request(
            'DELETE',
            $this->baseApiUrl . self::FILES_ENDPOINT . $this->encodePath($fileId),
            array($this->token->head() => $this->token->body())
        );

        $this->log(sprintf('Delete response HTTP %d', $response->getStatusCode()));

        return $response->getStatusCode() === 200;
    }

    private function log($message)
    {
        if ($this->logger !== null) {
            call_user_func($this->logger, 'file', $message);
        }
    }

    private function downloadByUrl($url, $outputPath, $label)
    {
        $this->log(sprintf('Downloading %s to %s', $label, $outputPath));
        $response = $this->client->request(
            'GET',
            $url,
            array($this->token->head() => $this->token->body())
        );

        $this->log(sprintf('%s response HTTP %d', ucfirst($label), $response->getStatusCode()));

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException(sprintf('Cannot download %s: %s', $label, $response->getBody()));
        }

        if (file_put_contents($outputPath, $response->getBody()) === false) {
            throw new RuntimeException('Cannot write file to: ' . $outputPath);
        }
    }

    private function encodePath($path)
    {
        $parts = explode('/', $path);
        $encoded = array();

        foreach ($parts as $part) {
            $encoded[] = rawurlencode($part);
        }

        return implode('/', $encoded);
    }

    private function generateUuid()
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
