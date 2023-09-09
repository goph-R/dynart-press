<?php

namespace Dynart\Press\OAuth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger as BigInteger2;


use Dynart\Micro\Config;
use Dynart\Micro\Router;

class GoogleOAuth {

    /** @var Config */
    private $config;

    /** @var Router */
    private $router;

    const CONFIG_AUTH_URL = 'oauth.google.auth_url';
    const CONFIG_TOKEN_URL = 'oauth.google.token_url';
    const CONFIG_CLIENT_ID = 'oauth.google.client_id';
    const CONFIG_CLIENT_SECRET = 'oauth.google.client_secret';

    const REDIRECT_URI = '/oauth/login-response';

    const RESPONSE_TYPE_CODE = 'code';
    const SCOPE_OPEN_ID = 'openid';
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

    const HEADER_CONTENT_TYPE_FORM_URL_ENCODED = 'Content-Type: application/x-www-form-urlencoded';

    public function __construct(Config $config, Router $router) {
        $this->config = $config;
        $this->router = $router;
    }

    public function loginUrl() {
        return $this->authUrl() . '?' . http_build_query($this->loginParameters());
    }

    public function fetchTokenResponse(string $code) {
        $response = $this->post(
            $this->tokenUrl(),
            $this->codeParameters($code)
        );
        return json_decode($response, true);
    }

    protected function authUrl() {
        return $this->config->get(self::CONFIG_AUTH_URL);
    }

    protected function tokenUrl() {
        return $this->config->get(self::CONFIG_TOKEN_URL);
    }

    protected function clientId() {
        return $this->config->get(self::CONFIG_CLIENT_ID);
    }

    public function clientSecret() {
        return $this->config->get(self::CONFIG_CLIENT_SECRET);
    }

    protected function redirectUri() {
        return $this->router->url(self::REDIRECT_URI);
    }

    protected function loginParameters() {
        return [
            'client_id'     => $this->clientId(),
            'redirect_uri'  => $this->redirectUri(),
            'response_type' => self::RESPONSE_TYPE_CODE,
            'scope'         => self::SCOPE_OPEN_ID
        ];
    }

    protected function codeParameters(string $code) {
        return [
            'code'          => $code,
            'client_id'     => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'redirect_uri'  => $this->redirectUri(),
            'grant_type'    => self::GRANT_TYPE_AUTHORIZATION_CODE
        ];
    }

    protected function post(string $url, array $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::HEADER_CONTENT_TYPE_FORM_URL_ENCODED));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }


    public function decodeIdToken(string $idToken) {
        $googlePublicKeys = json_decode(file_get_contents('google-keys.json'), true); // TODO: how to get this properly?
        $keys = [];
        foreach ($googlePublicKeys['keys'] as $googleKey) {
            $keys[$googleKey['kid']] = new Key(
                $this->loadPhpsecPublicKey($googleKey['n'], $googleKey['e']),
                $googleKey['alg']
            );
        }
        return JWT::decode($idToken, $keys);
    }

    private function loadPhpsecPublicKey(string $modulus, string $exponent): string { // TODO: phpseclib3
        $key = new RSA();
        $key->loadKey([
            'n' => new BigInteger2(JWT::urlsafeB64Decode($modulus), 256),
            'e' => new BigInteger2(JWT::urlsafeB64Decode($exponent), 256)
        ]);
        return $key->getPublicKey();
    }
}