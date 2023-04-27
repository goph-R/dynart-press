<?php

namespace Dynart\Press\Service;

use Dynart\Press\Entity\User;

class UserService {

    /** @var User */
    protected $current;

    /** @var User */
    protected $anonymous;

    public function __construct() {
        $this->anonymous = new User();
    }

    public function current(): User {
        return $this->current ? $this->current : $this->anonymous;
    }
}