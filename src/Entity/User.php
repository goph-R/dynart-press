<?php

namespace Dynart\Press\Entity;

class User extends NodeEntity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "bool", "notNull": true, "default": false } */
    public $active;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $email;

    /** @column { "type": "string", "size": 255 } */
    public $password;
}