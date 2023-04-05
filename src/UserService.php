<?php

namespace Dynart\Press;

class UserService {

    public function requireAdmin() {
        $this->requireLogin();

    }

    public function requireLogin() {

    }

}