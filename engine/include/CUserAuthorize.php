<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class UserAuthorize
{
    const SESSION_KEY = '_authsession';
    const OIDC_SESSION_KEY = '_oidc_auth';
    const PROVIDER_KEYCLOAK = 'keycloak';

    private $dsn;
    private $status = '';
    private $oidcMetadata = null;
    private $oidcJwks = null;

    function __construct($dsn)
    {
        $this->dsn = $dsn;
        $this->start();
    }

    function authorize($logout = true)
    {
        if ($logout) {
            $this->logout();
        } else {
            $this->start();
        }
    }

    function start()
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = array(
                'username' => null,
                'authenticated' => false,
                'data' => array()
            );
        }
    }

    function handleRequest()
    {
        if (!$this->isOidcEnabled()) {
            return;
        }

        $page = isset($_GET['page']) ? $_GET['page'] : null;
        if ($page === 'login_oidc') {
            $this->redirectToOidcLogin();
        }
        if ($page === 'oidc_callback') {
            $this->handleOidcCallback();
        }
        if ($page === 'logout_oidc') {
            $this->performOidcLogout();
        }
    }

    function logout()
    {
        $this->start();
        $_SESSION[self::SESSION_KEY] = array(
            'username' => null,
            'authenticated' => false,
            'data' => array()
        );
        unset($_SESSION[self::OIDC_SESSION_KEY]);
    }

    function checkAuth()
    {
        return !empty($_SESSION[self::SESSION_KEY]['authenticated']);
    }

    function getAuth()
    {
        return $this->checkAuth();
    }

    function getStatus()
    {
        return $this->status;
    }

    function setAuth($username)
    {
        $_SESSION[self::SESSION_KEY]['username'] = $username;
        $_SESSION[self::SESSION_KEY]['authenticated'] = true;
    }

    function setAuthData($name, $value, $overwrite = true)
    {
        if ($overwrite || !array_key_exists($name, $_SESSION[self::SESSION_KEY]['data'])) {
            $_SESSION[self::SESSION_KEY]['data'][$name] = $value;
        }
    }

    function getAuthData($name = null)
    {
        if ($name === null) {
            return $_SESSION[self::SESSION_KEY]['data'];
        }

        return isset($_SESSION[self::SESSION_KEY]['data'][$name]) ? $_SESSION[self::SESSION_KEY]['data'][$name] : null;
    }

    function getUserData()
    {
        global $db;

        $user = $this->getAuthData();
        if ($user && isset($user['user_id'])) {
            $roles = $db->fetch_rows("SELECT * FROM users_roles us JOIN roles USING (role) WHERE user_id=?", array($user['user_id']));
            $userData = $db->fetch(
                "SELECT login, screename, auth_provider, auth_subject, auth_username, auth_email FROM users WHERE user_id=?",
                array($user['user_id'])
            );

            $user['login'] = $userData['login'];
            $user['screename'] = $userData['screename'];
            $user['auth_provider'] = $userData['auth_provider'];
            $user['auth_subject'] = $userData['auth_subject'];
            $user['auth_username'] = $userData['auth_username'];
            $user['auth_email'] = $userData['auth_email'];
            $user['role'][ROLE_SYSTEM_USER_PUBLIC] = "Has access to public pages";
            $user['role'][ROLE_SYSTEM_USER_LOGGEDIN] = "User is loggedin to the system";
            foreach ($roles as $role) {
                $user['role'][$role['role']] = $role['description'];
            }

            UserActivity::log($user['user_id']);
        }

        return $user;
    }

    function isOidcEnabled()
    {
        return (bool) Config::Cfg()->get_oidcEnabled();
    }

    function redirectToClarinLogin()
    {
        if ($this->isOidcEnabled()) {
            header('Location: index.php?page=login_oidc');
            exit;
        }

        throw new Exception('Legacy federation login is disabled.');
    }

    function getClarinUser()
    {
        return null;
    }

    function getClarinLogin()
    {
        return null;
    }

    function getPendingOidcIdentity()
    {
        if (!isset($_SESSION[self::OIDC_SESSION_KEY]['pending_identity'])) {
            return null;
        }

        return $_SESSION[self::OIDC_SESSION_KEY]['pending_identity'];
    }

    function clearPendingOidcIdentity()
    {
        unset($_SESSION[self::OIDC_SESSION_KEY]['pending_identity']);
    }

    function linkPendingOidcIdentityToUser($userId)
    {
        $pendingIdentity = $this->getPendingOidcIdentity();
        if (!$pendingIdentity) {
            throw new Exception('Missing pending Keycloak identity.');
        }

        DbUser::updateAuthIdentity($userId, self::PROVIDER_KEYCLOAK, $pendingIdentity);
        $user = DbUser::get($userId);
        $this->completeLocalLogin($user);
        $this->clearPendingOidcIdentity();
    }

    function createUserFromPendingOidcIdentity($screenname, $email = null)
    {
        $pendingIdentity = $this->getPendingOidcIdentity();
        if (!$pendingIdentity) {
            throw new Exception('Missing pending Keycloak identity.');
        }

        $login = $pendingIdentity['username'] ? $pendingIdentity['username'] : $pendingIdentity['email'];
        $resolvedEmail = $email ? $email : ($pendingIdentity['email'] ? $pendingIdentity['email'] : 'unknown');

        DbUser::createNewUser(
            $login,
            $screenname,
            $resolvedEmail,
            'NOT SET',
            null,
            self::PROVIDER_KEYCLOAK,
            $pendingIdentity
        );

        $user = DbUser::get($this->getLastInsertId());
        $this->completeLocalLogin($user);
        $this->clearPendingOidcIdentity();
    }

    function getOidcLinkContext()
    {
        $pendingIdentity = $this->getPendingOidcIdentity();
        if (!$pendingIdentity) {
            return array();
        }

        return array(
            'screenname' => $pendingIdentity['name'],
            'email' => $pendingIdentity['email'],
            'username' => $pendingIdentity['username'],
            'provider' => self::PROVIDER_KEYCLOAK
        );
    }

    function consumePostLoginReturnUrl()
    {
        $returnUrl = isset($_SESSION[self::OIDC_SESSION_KEY]['return_url']) ? $_SESSION[self::OIDC_SESSION_KEY]['return_url'] : 'index.php';
        unset($_SESSION[self::OIDC_SESSION_KEY]['state']);
        unset($_SESSION[self::OIDC_SESSION_KEY]['nonce']);
        unset($_SESSION[self::OIDC_SESSION_KEY]['return_url']);

        return $returnUrl;
    }

    private function getLastInsertId()
    {
        global $db;
        return $db->last_id();
    }

    private function completeLocalLogin(array $user)
    {
        $this->setAuth($user['login']);
        $this->setAuthData('user_id', $user['user_id']);
        $this->setAuthData('screename', $user['screename']);
        $this->setAuthData('auth_provider', isset($user['auth_provider']) ? $user['auth_provider'] : null);
        UserActivity::login($user['user_id']);
        DbUser::updateLastLoginAt($user['user_id']);
    }

    private function redirectToOidcLogin()
    {
        $state = bin2hex(random_bytes(16));
        $nonce = bin2hex(random_bytes(16));
        $returnUrl = $this->resolveReturnUrl();
        $metadata = $this->getOidcMetadata();

        $_SESSION[self::OIDC_SESSION_KEY] = array(
            'state' => $state,
            'nonce' => $nonce,
            'return_url' => $returnUrl
        );

        $params = array(
            'client_id' => Config::Cfg()->get_oidcClientId(),
            'response_type' => 'code',
            'scope' => Config::Cfg()->get_oidcScopes(),
            'redirect_uri' => $this->getOidcRedirectUri(),
            'state' => $state,
            'nonce' => $nonce
        );

        header('Location: ' . $metadata['authorization_endpoint'] . '?' . http_build_query($params));
        exit;
    }

    private function handleOidcCallback()
    {
        $oidcState = isset($_SESSION[self::OIDC_SESSION_KEY]['state']) ? $_SESSION[self::OIDC_SESSION_KEY]['state'] : null;
        $receivedState = isset($_GET['state']) ? $_GET['state'] : null;
        $code = isset($_GET['code']) ? $_GET['code'] : null;

        if (!$oidcState || !$receivedState || !hash_equals($oidcState, $receivedState)) {
            throw new Exception('Invalid OIDC state.');
        }
        if (!$code) {
            throw new Exception('Missing OIDC authorization code.');
        }

        $tokens = $this->exchangeCodeForTokens($code);
        $claims = $this->validateIdToken($tokens['id_token']);
        $user = DbUser::getByAuthIdentity(self::PROVIDER_KEYCLOAK, $claims['subject']);

        if ($user) {
            DbUser::updateAuthIdentity($user['user_id'], self::PROVIDER_KEYCLOAK, $claims);
            $user = DbUser::get($user['user_id']);
            $this->completeLocalLogin($user);
            $this->redirectAfterLogin();
        }

        $_SESSION[self::OIDC_SESSION_KEY]['pending_identity'] = $claims;
        header('Location: index.php?page=login_oidc_link');
        exit;
    }

    private function performOidcLogout()
    {
        $idToken = isset($_SESSION[self::OIDC_SESSION_KEY]['id_token']) ? $_SESSION[self::OIDC_SESSION_KEY]['id_token'] : null;
        $this->logout();

        $metadata = $this->getOidcMetadata();
        $params = array(
            'post_logout_redirect_uri' => $this->getOidcPostLogoutRedirectUri()
        );
        if ($idToken) {
            $params['id_token_hint'] = $idToken;
        }

        header('Location: ' . $metadata['end_session_endpoint'] . '?' . http_build_query($params));
        exit;
    }

    private function exchangeCodeForTokens($code)
    {
        $metadata = $this->getOidcMetadata();
        $response = $this->httpPostForm($metadata['token_endpoint'], array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->getOidcRedirectUri(),
            'client_id' => Config::Cfg()->get_oidcClientId(),
            'client_secret' => Config::Cfg()->get_oidcClientSecret()
        ));

        if (empty($response['id_token'])) {
            throw new Exception('OIDC token response did not contain id_token.');
        }

        $_SESSION[self::OIDC_SESSION_KEY]['id_token'] = $response['id_token'];

        return $response;
    }

    private function validateIdToken($idToken)
    {
        $segments = explode('.', $idToken);
        if (count($segments) !== 3) {
            throw new Exception('Malformed id_token.');
        }

        $header = json_decode($this->base64UrlDecode($segments[0]), true);
        $payload = json_decode($this->base64UrlDecode($segments[1]), true);
        $signature = $this->base64UrlDecode($segments[2]);
        if (!$header || !$payload) {
            throw new Exception('Malformed id_token payload.');
        }

        $this->verifyJwtSignature($segments[0] . '.' . $segments[1], $signature, $header);
        $this->validateJwtClaims($payload);

        return array(
            'subject' => $payload['sub'],
            'username' => isset($payload['preferred_username']) ? $payload['preferred_username'] : null,
            'email' => isset($payload['email']) ? $payload['email'] : null,
            'email_verified' => !empty($payload['email_verified']),
            'name' => isset($payload['name']) ? $payload['name'] : (isset($payload['preferred_username']) ? $payload['preferred_username'] : $payload['sub'])
        );
    }

    private function verifyJwtSignature($signedData, $signature, array $header)
    {
        if (!isset($header['alg']) || $header['alg'] !== 'RS256') {
            throw new Exception('Unsupported id_token algorithm.');
        }
        if (empty($header['kid'])) {
            throw new Exception('Missing id_token key identifier.');
        }

        $jwks = $this->getOidcJwks();
        foreach ($jwks['keys'] as $jwk) {
            if (isset($jwk['kid']) && $jwk['kid'] === $header['kid']) {
                $publicKey = $this->buildPemFromJwk($jwk);
                $verified = openssl_verify($signedData, $signature, $publicKey, OPENSSL_ALGO_SHA256);
                if ($verified !== 1) {
                    throw new Exception('Invalid id_token signature.');
                }
                return;
            }
        }

        throw new Exception('Unable to find matching JWKS key.');
    }

    private function validateJwtClaims(array $payload)
    {
        $issuer = $this->getOidcMetadata()['issuer'];
        $audience = Config::Cfg()->get_oidcClientId();
        $nonce = isset($_SESSION[self::OIDC_SESSION_KEY]['nonce']) ? $_SESSION[self::OIDC_SESSION_KEY]['nonce'] : null;
        $now = time();

        if (!isset($payload['iss']) || $payload['iss'] !== $issuer) {
            throw new Exception('Unexpected OIDC issuer.');
        }

        $audClaim = isset($payload['aud']) ? $payload['aud'] : null;
        $audiences = is_array($audClaim) ? $audClaim : array($audClaim);
        if (!in_array($audience, $audiences, true)) {
            throw new Exception('Unexpected OIDC audience.');
        }

        if (!isset($payload['exp']) || $payload['exp'] < $now) {
            throw new Exception('Expired id_token.');
        }
        if (!isset($payload['iat']) || $payload['iat'] > ($now + 60)) {
            throw new Exception('Invalid id_token issue time.');
        }
        if (!$nonce || !isset($payload['nonce']) || !hash_equals($nonce, $payload['nonce'])) {
            throw new Exception('Invalid OIDC nonce.');
        }
    }

    private function getOidcMetadata()
    {
        if ($this->oidcMetadata !== null) {
            return $this->oidcMetadata;
        }

        $discoveryUrl = Config::Cfg()->get_oidcDiscoveryUrl();
        if (!$discoveryUrl) {
            $issuerUrl = rtrim(Config::Cfg()->get_oidcIssuerUrl(), '/');
            $discoveryUrl = $issuerUrl . '/.well-known/openid-configuration';
        }

        $this->oidcMetadata = $this->httpGetJson($discoveryUrl);
        if (empty($this->oidcMetadata['authorization_endpoint']) || empty($this->oidcMetadata['token_endpoint'])) {
            throw new Exception('OIDC discovery document is incomplete.');
        }

        return $this->oidcMetadata;
    }

    private function getOidcJwks()
    {
        if ($this->oidcJwks !== null) {
            return $this->oidcJwks;
        }

        $metadata = $this->getOidcMetadata();
        $this->oidcJwks = $this->httpGetJson($metadata['jwks_uri']);

        return $this->oidcJwks;
    }

    private function getOidcRedirectUri()
    {
        return Config::Cfg()->get_oidcRedirectUri() ? Config::Cfg()->get_oidcRedirectUri() : $this->buildAbsoluteUrl('index.php?page=oidc_callback');
    }

    private function getOidcPostLogoutRedirectUri()
    {
        return Config::Cfg()->get_oidcPostLogoutRedirectUri() ? Config::Cfg()->get_oidcPostLogoutRedirectUri() : $this->buildAbsoluteUrl('index.php');
    }

    private function buildAbsoluteUrl($path)
    {
        $baseUrl = Config::Cfg()->get_url();
        if ($baseUrl) {
            return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        }

        $scheme = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'));
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }

    private function resolveReturnUrl()
    {
        if (!empty($_GET['return'])) {
            return $_GET['return'];
        }

        if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'page=login_oidc') === false) {
            return $_SERVER['HTTP_REFERER'];
        }

        return $this->buildAbsoluteUrl('index.php');
    }

    private function redirectAfterLogin()
    {
        header('Location: ' . $this->consumePostLoginReturnUrl());
        exit;
    }

    private function httpGetJson($url)
    {
        $backendUrl = $this->getOidcBackendUrl($url);
        $curl = curl_init($backendUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($response === false || $httpCode >= 400) {
            throw new Exception('OIDC GET request failed for ' . $backendUrl . ': ' . $curlError . ' [' . $httpCode . ']');
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new Exception('Unable to decode OIDC JSON response.');
        }

        return $decoded;
    }

    private function httpPostForm($url, array $fields)
    {
        $backendUrl = $this->getOidcBackendUrl($url);
        $curl = curl_init($backendUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($response === false || $httpCode >= 400) {
            throw new Exception('OIDC POST request failed for ' . $backendUrl . ': ' . $curlError . ' [' . $httpCode . ']');
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new Exception('Unable to decode OIDC token response.');
        }

        return $decoded;
    }

    private function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    private function getOidcBackendUrl($url)
    {
        $internalBaseUrl = Config::Cfg()->get_oidcInternalBaseUrl();
        if (!$internalBaseUrl) {
            return $url;
        }

        $parsedUrl = parse_url($url);
        if (!$parsedUrl || !isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
            return $url;
        }

        $publicOrigin = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '');
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        return rtrim($internalBaseUrl, '/') . $path . $query . $fragment;
    }

    private function buildPemFromJwk(array $jwk)
    {
        $modulus = $this->base64UrlDecode($jwk['n']);
        $exponent = $this->base64UrlDecode($jwk['e']);

        $components = array(
            $this->asn1EncodeInteger($modulus),
            $this->asn1EncodeInteger($exponent)
        );
        $rsaPublicKey = $this->asn1EncodeSequence(implode('', $components));

        $algorithm = $this->asn1EncodeSequence(
            $this->asn1EncodeObjectIdentifier("\x2A\x86\x48\x86\xF7\x0D\x01\x01\x01") .
            $this->asn1EncodeNull()
        );

        $subjectPublicKeyInfo = $this->asn1EncodeSequence(
            $algorithm .
            $this->asn1EncodeBitString($rsaPublicKey)
        );

        return "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($subjectPublicKeyInfo), 64, "\n") .
            "-----END PUBLIC KEY-----\n";
    }

    private function asn1EncodeLength($length)
    {
        if ($length < 128) {
            return chr($length);
        }

        $temp = ltrim(pack('N', $length), "\x00");

        return chr(0x80 | strlen($temp)) . $temp;
    }

    private function asn1EncodeInteger($value)
    {
        if (ord($value[0]) > 0x7f) {
            $value = "\x00" . $value;
        }

        return "\x02" . $this->asn1EncodeLength(strlen($value)) . $value;
    }

    private function asn1EncodeSequence($value)
    {
        return "\x30" . $this->asn1EncodeLength(strlen($value)) . $value;
    }

    private function asn1EncodeBitString($value)
    {
        return "\x03" . $this->asn1EncodeLength(strlen($value) + 1) . "\x00" . $value;
    }

    private function asn1EncodeNull()
    {
        return "\x05\x00";
    }

    private function asn1EncodeObjectIdentifier($value)
    {
        return "\x06" . $this->asn1EncodeLength(strlen($value)) . $value;
    }
}

?>
