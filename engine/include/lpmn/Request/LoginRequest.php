<?php

namespace Inforex\Lpmn\Request;

use Inforex\Lpmn\Http\HttpClient;
use RuntimeException;

class LoginRequest
{
    const ACCESS_TOKEN_FIELD = 'access_token';
    const API_TOKEN = 'API-TOKEN';

    /** @var HttpClient */
    private $client;

    /** @var string */
    private $baseApiUrl;

    public function __construct(HttpClient $client, $baseApiUrl)
    {
        $this->client = $client;
        $this->baseApiUrl = rtrim($baseApiUrl, '/');
    }

    public function login(User $user)
    {
        $response = $this->client->request(
            'POST',
            $this->baseApiUrl . '/oauth/login',
            array('Content-Type' => 'application/json'),
            $user->toJson()
        );

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Login failed: ' . $response->getBody());
        }

        $content = json_decode($response->getBody(), true);
        if (!is_array($content) || !isset($content[self::ACCESS_TOKEN_FIELD])) {
            throw new RuntimeException('Login response does not contain access token.');
        }

        return new Token('Authorization', sprintf('Bearer %s', $content[self::ACCESS_TOKEN_FIELD]));
    }

    public function apikey($key)
    {
        return new Token(self::API_TOKEN, $key);
    }
}
