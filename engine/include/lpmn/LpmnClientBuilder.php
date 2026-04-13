<?php

namespace Inforex\Lpmn;

use Inforex\Lpmn\Http\HttpClient;
use Inforex\Lpmn\Request\FileRequest;
use Inforex\Lpmn\Request\LoginRequest;
use Inforex\Lpmn\Request\TaskRequest;
use Inforex\Lpmn\Request\User;
use RuntimeException;

class LpmnClientBuilder
{
    const CONFIG_API_KEY = 'lpmn_api_key';
    const CONFIG_API_URL = 'lpmn_api_url';

    /** @var HttpClient */
    private $httpClient;

    /** @var string */
    private $apiUrl = LpmnClient::DEFAULT_API_URL;

    /** @var bool */
    private $apiUrlConfigured = false;

    /** @var FileRequest|null */
    private $fileRequest;

    /** @var TaskRequest|null */
    private $taskRequest;

    /** @var callable|null */
    private $logger;

    public function __construct(HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new HttpClient();
        $this->logger = null;
    }

    public function apiUrl($url)
    {
        $this->apiUrl = rtrim($url, '/');
        $this->apiUrlConfigured = true;

        return $this;
    }

    public function authenticate($username, $password)
    {
        $this->applyDefaultApiUrl();
        $loginRequest = new LoginRequest($this->httpClient, $this->apiUrl);
        $token = $loginRequest->login(new User($username, $password));
        $this->configureRequests($token);

        return $this;
    }

    public function apikey($key = null)
    {
        $this->applyDefaultApiUrl();

        if ($key === null || $key === '') {
            $key = $this->getDefaultApiKey();
        }

        if ($key === null || $key === '') {
            throw new RuntimeException('Configure LPMN API key before using apikey().');
        }

        $loginRequest = new LoginRequest($this->httpClient, $this->apiUrl);
        $token = $loginRequest->apikey($key);
        $this->configureRequests($token);

        return $this;
    }

    public function logger(callable $logger = null)
    {
        $this->logger = $logger;

        if ($this->fileRequest !== null) {
            $this->fileRequest->setLogger($logger);
        }

        if ($this->taskRequest !== null) {
            $this->taskRequest->setLogger($logger);
        }

        return $this;
    }

    public function build()
    {
        $this->applyDefaultApiUrl();

        if ($this->fileRequest === null || $this->taskRequest === null) {
            $apiKey = $this->getDefaultApiKey();
            if ($apiKey !== null && $apiKey !== '') {
                $this->apikey($apiKey);
            }
        }

        if ($this->fileRequest === null || $this->taskRequest === null) {
            throw new RuntimeException('Configure authentication before build().');
        }

        $client = new LpmnClient($this->fileRequest, $this->taskRequest);
        $client->setLogger($this->logger);

        return $client;
    }

    private function configureRequests($token)
    {
        $this->fileRequest = new FileRequest($this->httpClient, $token, $this->apiUrl);
        $this->taskRequest = new TaskRequest($this->httpClient, $token, $this->apiUrl);
        $this->fileRequest->setLogger($this->logger);
        $this->taskRequest->setLogger($this->logger);
    }

    private function getDefaultApiKey()
    {
        return $this->getConfigValue(self::CONFIG_API_KEY);
    }

    private function getDefaultApiUrl()
    {
        return $this->getConfigValue(self::CONFIG_API_URL);
    }

    private function applyDefaultApiUrl()
    {
        if ($this->apiUrlConfigured) {
            return;
        }

        $apiUrl = $this->getDefaultApiUrl();
        if ($apiUrl !== null && $apiUrl !== '') {
            $this->apiUrl = rtrim($apiUrl, '/');
        }
    }

    private function getConfigValue($key)
    {
        if (!class_exists('\Config')) {
            return null;
        }

        try {
            return \Config::Cfg()->{'get_' . $key}();
        } catch (\Exception $e) {
            return null;
        }
    }
}
