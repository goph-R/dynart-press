<?php

namespace Dynart\Press\Controller;


use Dynart\Micro\Micro;
use Dynart\Micro\Request;
use Dynart\Micro\WebApp;

use Dynart\Press\OAuth\GoogleOAuth;

class OAuthController {

    /** @var GoogleOAuth */
    private $oauth;

    /** @var Request */
    private $request;

    /** @var WebApp */
    private $app;

    public function __construct(GoogleOAuth $oauth, Request $request) {
        $this->app = Micro::app();
        $this->request = $request;

        // TODO: strategy pattern for different types of login (Google, Apple, Facebook, Microsoft)
        $this->oauth = $oauth;
    }

    /**
     * @route GET /oauth/login
     */
    public function login() {
        $this->app->redirect($this->oauth->loginUrl());
    }

    /**
     * @route GET /oauth/login-response
     */
    public function loginResponse() {
        $code = $this->request->get('code');
        if ($code) {
            $response = $this->oauth->fetchTokenResponse($code);
            if (array_key_exists('id_token', $response)) {
                return (array)$this->oauth->decodeIdToken($response['id_token']);
            }
        }
        return $this->app->sendError(401);
    }

}